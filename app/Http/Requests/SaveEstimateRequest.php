<?php

namespace App\Http\Requests;

use App\Support\EstimateRules;
use Illuminate\Foundation\Http\FormRequest;

class SaveEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in the controller via EstimatePolicy.
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'service_type' => EstimateRules::serviceType(),
            'client' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string', 'max:10000'],
            'terms' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'company_id' => ['nullable', 'integer'],
            'pricing' => ['required', 'array'],
            ...EstimateRules::pricing('pricing.'),
            ...EstimateRules::lines(),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['title.required' => 'Enter an estimate title before saving.'];
    }
}
