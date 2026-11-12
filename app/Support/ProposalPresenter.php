<?php

namespace App\Support;

use App\Models\Estimate;

/**
 * The customer-facing view of an estimate, used by both the signed-in proposal page and
 * the public share link. Never includes labor/material costs, margin or internal notes.
 */
class ProposalPresenter
{
    /**
     * The parts of the proposal a customer reads and signs (no date or branding),
     * used to tell whether an edit changed what they authorized.
     *
     * @return array<string, mixed>
     */
    public static function customerFacing(Estimate $estimate): array
    {
        $view = self::present($estimate);
        unset($view['date'], $view['company']);
        $view['lines'] = $view['lines']->all();

        return $view;
    }

    /** @return array<string, mixed> */
    public static function present(Estimate $estimate): array
    {
        $markup = (float) $estimate->pricing['markup'];
        $totals = Pricing::calculate($estimate->lines, $estimate->pricing);

        $lines = collect($estimate->lines)
            ->filter(fn (array $l) => (float) $l['qty'] > 0)
            ->map(fn (array $l) => [
                'name' => $l['name'] ?: 'Service item',
                'qty' => $l['qty'],
                'unit' => $l['unit'],
                'amount' => Pricing::lineSell($l, $markup),
            ])->values();

        return [
            'number' => $estimate->number,
            'title' => $estimate->title,
            'client' => $estimate->client,
            'address' => $estimate->address,
            'service' => $estimate->service_type,
            'scope' => $estimate->scope,
            'terms' => $estimate->terms,
            'date' => $estimate->updated_at?->toIso8601String(),
            'company' => $estimate->company->summary(),
            'lines' => $lines,
            'discount' => $totals['discount'],
            'trip' => (float) $estimate->pricing['trip'],
            'total' => $totals['total'],
        ];
    }
}
