import { Link } from '@inertiajs/react';

export default function CategoryNav({ categories, activeId = null }) {
    if (!categories?.length) return null;

    return (
        <nav className="category-nav" aria-label="Categorías">
            <Link href="/" className={activeId === null ? 'is-active' : ''}>
                Todos
            </Link>
            {categories.map((c) => (
                <Link
                    key={c.id}
                    href={`/categorias/${c.id}`}
                    className={activeId === c.id ? 'is-active' : ''}
                >
                    {c.name}
                </Link>
            ))}
        </nav>
    );
}
