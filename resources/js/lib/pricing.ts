// Ported exactly from the original Crystal Creek desk (functions N, P, F).
// Mirrors app/Support/Pricing.php; both are pinned by tests/fixtures/pricing-cases.json.
import type { Line, Pricing } from '@/types/desk';

export interface PricingResult {
    cost: number;
    subtotal: number;
    discount: number;
    total: number;
    incomplete: boolean;
    margin: number;
}

type LineLike = Pick<Line, 'qty' | 'labor' | 'materials'> & { fixed: number | null | undefined };
type PricingLike = Pick<Pricing, 'markup' | 'discount' | 'trip'>;

const num = (v: unknown): number => {
    const n = Number(v);

    return Number.isFinite(n) ? n : 0;
};

export const round2 = (value: number): number => Math.round((value + 2 ** -52) * 100) / 100;

const fixedOf = (line: LineLike): number | null =>
    line.fixed === null || line.fixed === undefined || (line.fixed as unknown) === '' ? null : num(line.fixed);

export function lineSell(line: LineLike, markup: number): number {
    const fixed = fixedOf(line);

    return round2(num(line.qty) * (fixed ?? (num(line.labor) + num(line.materials)) * (1 + num(markup) / 100)));
}

export function calculate(lines: LineLike[], pricing: PricingLike): PricingResult {
    const cost = round2(lines.reduce((sum, l) => sum + round2(num(l.qty) * (num(l.labor) + num(l.materials))), 0));
    const subtotal = round2(lines.reduce((sum, l) => sum + lineSell(l, pricing.markup), 0));
    const discount = round2((subtotal * num(pricing.discount)) / 100);
    const total = round2(subtotal - discount + num(pricing.trip));

    return {
        cost,
        subtotal,
        discount,
        total,
        incomplete: lines.some((l) => num(l.qty) > 0 && num(l.labor) + num(l.materials) === 0),
        margin: total > 0 ? ((total - cost) / total) * 100 : 0,
    };
}

export function flags(result: PricingResult, pricing: Pick<Pricing, 'target' | 'authorization' | 'manager'>) {
    return {
        belowTarget: !result.incomplete && result.total > 0 && result.margin < num(pricing.target),
        aboveAuthorization: result.total > num(pricing.authorization),
        aboveManagerReview: result.total > num(pricing.manager),
    };
}
