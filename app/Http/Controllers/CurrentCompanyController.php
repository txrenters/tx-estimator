<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\CurrentCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrentCompanyController extends Controller
{
    public function update(Request $request, CurrentCompany $current): RedirectResponse
    {
        abort_unless($request->user()->isGlobal(), 403);

        $data = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ]);

        $current->set(isset($data['company_id']) ? Company::query()->whereKey($data['company_id'])->firstOrFail() : null);

        return back();
    }
}
