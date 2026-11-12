const currency = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });
const dateFormat = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    timeZone: 'America/Chicago',
});

export function money(value: number | string | null | undefined): string {
    return currency.format(Number(value ?? 0));
}

export function date(iso: string | null | undefined): string {
    return iso ? dateFormat.format(new Date(iso)) : '';
}

export function initials(name: string): string {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]!.toUpperCase())
        .join('');
}
