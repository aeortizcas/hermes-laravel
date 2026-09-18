/**
 * Precio estilo Amazon: número grande con los céntimos en superíndice.
 * Ej.: $12.34  ->  $ 12 .34
 */
export default function Price({ value, className = '' }) {
    const [int, cents] = Number(value || 0).toFixed(2).split('.');

    return (
        <span className={`price ${className}`} aria-label={`${Number(value || 0).toFixed(2)}`}>
            <span className="price__symbol" aria-hidden="true">$</span>
            <span className="price__int">{int}</span>
            <span className="price__cents" aria-hidden="true">.{cents}</span>
        </span>
    );
}