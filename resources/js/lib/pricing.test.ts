import { describe, expect, it } from 'vitest';
import cases from '../../../tests/fixtures/pricing-cases.json';
import { calculate, flags } from './pricing';

describe('pricing matches the original calculation', () => {
    for (const c of cases) {
        it(c.name, () => {
            const result = calculate(c.lines, c.pricing);

            expect(result.cost).toBeCloseTo(c.expected.cost, 7);
            expect(result.subtotal).toBeCloseTo(c.expected.subtotal, 7);
            expect(result.discount).toBeCloseTo(c.expected.discount, 7);
            expect(result.total).toBeCloseTo(c.expected.total, 7);
            expect(result.incomplete).toBe(c.expected.incomplete);
            expect(result.margin).toBeCloseTo(c.expected.margin, 6);
            expect(flags(result, c.pricing)).toEqual(c.flags);
        });
    }
});
