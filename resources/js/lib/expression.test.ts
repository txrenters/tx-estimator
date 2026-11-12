import { describe, expect, it } from 'vitest';
import { evaluate, isExpression } from './expression';

describe('evaluate', () => {
    it('reads plain numbers', () => {
        expect(evaluate('24')).toBe(24);
        expect(evaluate('112.5')).toBe(112.5);
        expect(evaluate('.5')).toBe(0.5);
    });

    it('adds and subtracts left to right', () => {
        expect(evaluate('12+8.5+4')).toBe(24.5);
        expect(evaluate('10-2-3')).toBe(5);
    });

    it('multiplies and divides before adding', () => {
        expect(evaluate('2+3×4')).toBe(14);
        expect(evaluate('12×10+6×8')).toBe(168);
        expect(evaluate('9÷3×2')).toBe(6);
    });

    it('accepts keyboard operators and spaces', () => {
        expect(evaluate(' 2 * 3 / 2 ')).toBe(3);
        expect(evaluate('5 − 2')).toBe(3);
    });

    it('ignores a trailing operator while typing', () => {
        expect(evaluate('12+')).toBe(12);
        expect(evaluate('12×')).toBe(12);
    });

    it('returns null for things it cannot read', () => {
        expect(evaluate('')).toBeNull();
        expect(evaluate('abc')).toBeNull();
        expect(evaluate('1..2')).toBeNull();
        expect(evaluate('alert(1)')).toBeNull();
    });

    it('returns null when dividing by zero', () => {
        expect(evaluate('5÷0')).toBeNull();
    });

    it('never goes below zero', () => {
        expect(evaluate('2-5')).toBe(0);
    });
});

describe('isExpression', () => {
    it('is true only when an operator is present', () => {
        expect(isExpression('24')).toBe(false);
        expect(isExpression('12+4')).toBe(true);
        expect(isExpression('3×4')).toBe(true);
    });
});
