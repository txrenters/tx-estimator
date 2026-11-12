<?php

namespace App\Http\Requests;

use App\Support\ServiceCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class CompanySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in the controller via CompanyPolicy.
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'short_name' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:120'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            // No SVG: an SVG served from our origin can carry script.
            'logo' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])->max(2 * 1024)],
            'remove_logo' => ['nullable', 'boolean'],
            'default_scope' => ['nullable', 'string', 'max:10000'],
            'default_terms' => ['nullable', 'string', 'max:10000'],
            'services' => ['sometimes', 'array', 'min:1'],
            'services.*' => ['string', Rule::in(app(ServiceCatalog::class)->active())],
        ];
    }
}
