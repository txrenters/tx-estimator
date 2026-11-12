<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * Customer share links for an estimate's proposal. One active link per estimate;
 * creating a new one retires the old link.
 */
class EstimateShareController extends Controller
{
    public const EXPIRY_OPTIONS = [7, 30, 90];

    public function store(Request $request, Estimate $estimate): RedirectResponse
    {
        Gate::authorize('share', $estimate);

        $data = $request->validate([
            'expires_in_days' => ['nullable', 'integer', 'in:'.implode(',', self::EXPIRY_OPTIONS)],
        ]);

        $estimate->forceFill([
            'share_token' => self::newToken(),
            'share_expires_at' => isset($data['expires_in_days']) ? now()->addDays((int) $data['expires_in_days']) : null,
            'shared_at' => now(),
            'share_views' => 0,
            'share_viewed_at' => null,
        ])->saveQuietly();

        $estimate->logActivity($request->user(), 'shared', $estimate->status, $estimate->status);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Share link ready. Copy it and send it to your customer.']);

        return back();
    }

    /** Short, unguessable link code: 12 letters/digits (~71 bits), retried on the rare clash. */
    private static function newToken(): string
    {
        do {
            $token = Str::random(12);
        } while (Estimate::where('share_token', $token)->exists());

        return $token;
    }

    public function destroy(Request $request, Estimate $estimate): RedirectResponse
    {
        Gate::authorize('share', $estimate);

        $estimate->forceFill(['share_token' => null, 'share_expires_at' => null])->saveQuietly();

        $estimate->logActivity($request->user(), 'unshared', $estimate->status, $estimate->status);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Share link turned off.']);

        return back();
    }
}
