<?php

namespace App\Http\Requests;

use App\Support\EstimateRules;
use Illuminate\Foundation\Http\FormRequest;

class PricingDefaultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canApprove();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return EstimateRules::pricing();
    }
}
