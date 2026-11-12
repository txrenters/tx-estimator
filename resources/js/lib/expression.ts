// Tiny arithmetic evaluator for the calculator keypad: numbers with + − × ÷.
// Hand-written (no eval); × and ÷ bind tighter than + and −.

const OPS = /[+\-−×*÷/]/;

export function isExpression(text: string): boolean {
    return OPS.test(text.trim().replace(/^-/, ''));
}

export function evaluate(text: string): number | null {
    const normalized = text
        .replace(/\s+/g, '')
        .replace(/−/g, '-')
        .replace(/\*/g, '×')
        .replace(/\//g, '÷')
        .replace(/[+\-×÷]+$/, ''); // ignore a trailing operator while typing

    if (normalized === '') {
        return null;
    }

    const tokens = normalized.match(/\d*\.?\d+|\d+\.|[+\-×÷]/g);

    if (!tokens || tokens.join('') !== normalized) {
        return null;
    }

    // Expect: number (op number)*
    const numbers: number[] = [];
    const ops: string[] = [];

    for (let i = 0; i < tokens.length; i++) {
        const token = tokens[i]!;
        const expectNumber = i % 2 === 0;

        if (expectNumber) {
            const n = Number(token);
            if (!/^\d*\.?\d*$/.test(token) || Number.isNaN(n)) {
                return null;
            }
            numbers.push(n);
        } else {
            if (!/^[+\-×÷]$/.test(token)) {
                return null;
            }
            ops.push(token);
        }
    }

    if (numbers.length !== ops.length + 1) {
        return null;
    }

    // First pass: × and ÷.
    const terms: number[] = [numbers[0]!];
    const addOps: string[] = [];

    for (let i = 0; i < ops.length; i++) {
        const op = ops[i]!;
        const next = numbers[i + 1]!;

        if (op === '×') {
            terms[terms.length - 1]! *= next;
        } else if (op === '÷') {
            if (next === 0) {
                return null;
            }
            terms[terms.length - 1]! /= next;
        } else {
            addOps.push(op);
            terms.push(next);
        }
    }

    // Second pass: + and −.
    let result = terms[0]!;
    for (let i = 0; i < addOps.length; i++) {
        result = addOps[i] === '+' ? result + terms[i + 1]! : result - terms[i + 1]!;
    }

    return Number.isFinite(result) ? Math.max(0, result) : null;
}
