<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Company::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'unique:companies,name'],
            'short_name' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:120'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            // Optionally start from an existing company.
            'copy_from' => ['nullable', 'integer', 'exists:companies,id'],
            'copy' => ['nullable', 'array'],
            'copy.*' => ['string', 'in:services,pricing,templates'],
        ];
    }
}
