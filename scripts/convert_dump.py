#!/usr/bin/env python3
"""
Convert a phpMyAdmin/MariaDB SQL dump (db_drcr) into a SQLite database.

The dump is phpMyAdmin-flavored:
  CREATE TABLE x (...);            <- column defs only
  ALTER TABLE x ADD PRIMARY KEY (id) USING BTREE;
  ALTER TABLE x ADD KEY ... / FOREIGN KEY ...
  ALTER TABLE x MODIFY id int(11) NOT NULL AUTO_INCREMENT ...

We keep the original column declarations (SQLite ignores unknown types),
promote the primary-key column to `INTEGER PRIMARY KEY AUTOINCREMENT` so
future inserts auto-assign ids, and drop every ALTER statement (indexes/FKs
are not needed for local dev; production keeps the real MySQL schema).

Usage: python3 convert_dump.py <dump.sql> <output.sqlite>
"""
import os
import re
import sqlite3
import sys

ALTER_PK_RE = re.compile(
    r"ALTER\s+TABLE\s+`?(?P<table>[^\s`(]+)`?\s+ADD\s+PRIMARY\s+KEY\s*\(`?(?P<col>\w+)`?\)",
    re.IGNORECASE,
)


def collect_primary_keys(dump: str) -> dict:
    """Map table name -> primary key column name from ALTER statements."""
    return {m.group("table"): m.group("col") for m in ALTER_PK_RE.finditer(dump)}


def _split_columns(body: str):
    """Split a CREATE TABLE body on top-level commas (ignoring parentheses)."""
    parts, depth, start = [], 0, 0
    for i, ch in enumerate(body):
        if ch == "(":
            depth += 1
        elif ch == ")":
            depth -= 1
        elif ch == "," and depth == 0:
            parts.append(body[start:i])
            start = i + 1
    parts.append(body[start:])
    return [p.strip() for p in parts if p.strip()]


def _rewrite_pk_column(column: str, pk_col: str) -> str:
    col_name = column.strip().strip("`").split()[0] if column.strip() else ""
    if col_name == pk_col and "PRIMARY KEY" not in column:
        return f"{pk_col} INTEGER PRIMARY KEY AUTOINCREMENT"
    return column


def clean_statement(stmt: str, primary_keys: dict) -> str:
    """Return an SQLite-friendly version of one MySQL statement, or '' to skip."""
    stmt = stmt.replace("`", "")
    stmt = re.sub(r"DEFAULT\s+current_timestamp\(\)", "DEFAULT CURRENT_TIMESTAMP", stmt, flags=re.I)
    # MySQL-only clauses SQLite cannot parse
    stmt = re.sub(r"\s+ON\s+UPDATE\s+(?:current_timestamp\(\)|CURRENT_TIMESTAMP)", "", stmt, flags=re.I)
    stmt = re.sub(r"\s+(?:UNSIGNED|ZEROFILL)\b", "", stmt, flags=re.I)
    stmt = re.sub(r"\s+COMMENT\s+'[^']*'", "", stmt)
    stmt = re.sub(r"\s+AUTO_INCREMENT\b", "", stmt, flags=re.I)
    stmt = re.sub(r"\benum\([^)]*\)", "TEXT", stmt, flags=re.I)
    stmt = re.sub(r"\bset\([^)]*\)", "TEXT", stmt, flags=re.I)

    upper = stmt.lstrip().upper()

    if upper.startswith("ALTER TABLE"):
        return ""  # PKs/indexes/FKs/auto-inc handled elsewhere

    if upper.startswith("CREATE TABLE"):
        m = re.match(
            r"(?is)^\s*CREATE TABLE\s+(?P<name>[^\s(]+)\s*\((?P<body>.*)\)\s+ENGINE.*$",
            stmt,
        )
        if not m:
            raise ValueError(f"Unhandled CREATE TABLE:\n{stmt[:300]}")
        name = m.group("name").strip()
        body = m.group("body").strip().rstrip(",").strip()
        pk_col = primary_keys.get(name)
        if pk_col:
            body = ",".join(_rewrite_pk_column(col, pk_col) for col in _split_columns(body))
        return f"CREATE TABLE {name} ({body});"
    return stmt


def split_statements(dump: str):
    """Yield logical SQL statements, dropping MySQL bookkeeping noise."""
    buf = []
    for raw in dump.splitlines():
        line = raw.strip()
        if not line or line.startswith("--"):
            continue
        upper = line.upper()
        if upper.startswith("SET ") or upper.startswith("/*!"):
            continue
        if upper.startswith("START TRANSACTION") or upper == "COMMIT;":
            continue
        buf.append(line)
        if line.endswith(";"):
            yield " ".join(buf)
            buf = []
    if buf:
        yield " ".join(buf)


def main() -> None:
    if len(sys.argv) != 3:
        print(__doc__)
        sys.exit(1)
    dump_path, out_path = sys.argv[1], sys.argv[2]

    with open(dump_path, "r", encoding="utf-8", errors="replace") as fh:
        dump = fh.read()

    primary_keys = collect_primary_keys(dump)
    statements = []
    for stmt in split_statements(dump):
        try:
            cleaned = clean_statement(stmt, primary_keys)
        except ValueError as exc:  # pragma: no cover - defensive
            print(f"WARN skipping statement: {exc}", file=sys.stderr)
            continue
        if cleaned:
            statements.append(cleaned)

    os.makedirs(os.path.dirname(os.path.abspath(out_path)), exist_ok=True)

    conn = sqlite3.connect(out_path)
    conn.executescript("\n".join(statements))
    conn.commit()

    cur = conn.cursor()
    tables = [
        r[0]
        for r in cur.execute(
            "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        )
    ]
    print(f"OK: {len(tables)} tablas en {out_path}")
    for t in ["tbl_producto", "tbl_producto_imagenes", "tbl_venta", "tbl_entrada",
              "tbl_detalle", "tbl_master", "tbl_categories", "register", "images",
              "categories", "menu_items", "tbl_rastreo"]:
        if t in tables:
            n = cur.execute(f'SELECT COUNT(*) FROM "{t}"').fetchone()[0]
            print(f"  {t}: {n} filas")
    integrity = cur.execute("PRAGMA integrity_check").fetchone()[0]
    print(f"integrity: {integrity}")
    conn.close()


if __name__ == "__main__":
    main()
