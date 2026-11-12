<?php

namespace App\Support;

/**
 * Estimate pricing, ported exactly from the original Crystal Creek desk
 * (functions N, P and F in docs/superpowers/reference/crystal-creek/page-1XroWPtX.js).
 * Mirrors resources/js/lib/pricing.ts; both are pinned by tests/fixtures/pricing-cases.json.
 */
class Pricing
{
    /** JS Math.round((x + 2**-52) * 100) / 100 — Math.round(v) is floor(v + 0.5). */
    public static function round2(float $value): float
    {
        return floor(($value + PHP_FLOAT_EPSILON) * 100 + 0.5) / 100;
    }

    /**
     * @param  array<string, mixed>  $line
     */
    public static function lineSell(array $line, float $markup): float
    {
        $line = self::normalize($line);
        $unit = $line['fixed'] ?? ($line['labor'] + $line['materials']) * (1 + $markup / 100);

        return self::round2($line['qty'] * $unit);
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     * @param  array<string, int|float>  $pricing
     * @return array{cost:float,subtotal:float,discount:float,total:float,incomplete:bool,margin:float}
     */
    public static function calculate(array $lines, array $pricing): array
    {
        $markup = (float) ($pricing['markup'] ?? 0);

        $cost = 0.0;
        $subtotal = 0.0;
        $incomplete = false;

        foreach ($lines as $line) {
            $line = self::normalize($line);
            $unitCost = $line['labor'] + $line['materials'];
            $cost += self::round2($line['qty'] * $unitCost);
            $subtotal += self::lineSell($line, $markup);

            if ($line['qty'] > 0 && $unitCost == 0) {
                $incomplete = true;
            }
        }

        $cost = self::round2($cost);
        $subtotal = self::round2($subtotal);
        $discount = self::round2($subtotal * (float) ($pricing['discount'] ?? 0) / 100);
        $total = self::round2($subtotal - $discount + (float) ($pricing['trip'] ?? 0));

        return [
            'cost' => $cost,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'incomplete' => $incomplete,
            'margin' => $total > 0 ? ($total - $cost) / $total * 100 : 0.0,
        ];
    }

    /**
     * @param  array{total:float,incomplete:bool,margin:float}  $result
     * @param  array<string, int|float>  $pricing
     * @return array{belowTarget:bool,aboveAuthorization:bool,aboveManagerReview:bool}
     */
    public static function flags(array $result, array $pricing): array
    {
        return [
            'belowTarget' => ! $result['incomplete'] && $result['total'] > 0 && $result['margin'] < (float) $pricing['target'],
            'aboveAuthorization' => $result['total'] > (float) $pricing['authorization'],
            'aboveManagerReview' => $result['total'] > (float) $pricing['manager'],
        ];
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array{qty:float,labor:float,materials:float,fixed:float|null}
     */
    private static function normalize(array $line): array
    {
        return [
            'qty' => (float) ($line['qty'] ?? 0),
            'labor' => (float) ($line['labor'] ?? 0),
            'materials' => (float) ($line['materials'] ?? 0),
            'fixed' => isset($line['fixed']) && $line['fixed'] !== '' ? (float) $line['fixed'] : null,
        ];
    }
}
