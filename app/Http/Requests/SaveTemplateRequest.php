<?php

namespace App\Http\Requests;

use App\Support\EstimateRules;
use Illuminate\Foundation\Http\FormRequest;

class SaveTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in the controller via ServiceTemplatePolicy.
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'service_type' => EstimateRules::serviceType(),
            'scope' => ['nullable', 'string', 'max:10000'],
            'terms' => ['nullable', 'string', 'max:10000'],
            'company_id' => ['nullable', 'integer'],
            ...EstimateRules::lines(),
        ];
    }
}
