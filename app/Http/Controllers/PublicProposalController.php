<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Support\ProposalPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * The proposal a customer opens from a share link — no login.
 */
class PublicProposalController extends Controller
{
    public function __invoke(Request $request, string $company, string $token): Response
    {
        $estimate = $this->find($token);

        // The code identifies the estimate; keep the company part current (e.g. after a rename).
        if ($company !== $estimate->company->linkSlug()) {
            return redirect()->to((string) $estimate->shareUrl(), 301);
        }

        // Count the view without touching the estimate's updated_at.
        Estimate::query()->whereKey($estimate->id)->toBase()->update([
            'share_views' => DB::raw('share_views + 1'),
            'share_viewed_at' => now(),
        ]);

        $response = Inertia::render('public/Proposal', [
            'proposal' => ProposalPresenter::present($estimate),
            'authorization' => $estimate->authorization(),
            'authorizeUrl' => $estimate->shareUrl().'/authorize',
        ])->toResponse($request);

        // Keep shared proposals out of search engines and don't leak the link via Referer.
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }

    /** The property owner authorizes the proposal by typing their full name. */
    /** Links sent before the company name was added: /p/{code}. */
    public function legacy(string $token): RedirectResponse
    {
        return redirect()->to((string) $this->find($token)->shareUrl(), 301);
    }

    public function sign(Request $request, string $company, string $token): RedirectResponse
    {
        $estimate = $this->find($token);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'agree' => ['accepted'],
        ], [
            'name.required' => 'Type your full name to authorize.',
            'agree.accepted' => 'Tick the box to confirm you authorize this work.',
        ]);

        if ($estimate->authorized_at !== null) {
            throw ValidationException::withMessages(['name' => 'This proposal has already been authorized.']);
        }

        $name = trim(preg_replace('/\s+/', ' ', $data['name']) ?? '');

        $estimate->forceFill([
            'authorized_name' => $name,
            'authorized_at' => now(),
            'authorized_ip' => $request->ip(),
        ])->saveQuietly();

        $estimate->logActivity(null, 'customer_authorized', $estimate->status, $estimate->status, $name);

        return redirect()->to((string) $estimate->shareUrl());
    }

    private function find(string $token): Estimate
    {
        $estimate = Estimate::query()
            ->with('company')
            ->where('share_token', $token)
            ->where(fn ($q) => $q->whereNull('share_expires_at')->orWhere('share_expires_at', '>', now()))
            ->first();

        abort_if($estimate === null, 404);

        return $estimate;
    }
}
