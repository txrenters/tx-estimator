<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Validation rules shared by templates, estimates and pricing defaults.
 */
class EstimateRules
{
    public const PRICING_KEYS = ['markup', 'discount', 'trip', 'authorization', 'manager', 'target'];

    /** @return array<string, mixed> */
    public static function lines(): array
    {
        return [
            'lines' => ['required', 'array', 'min:1', 'max:200'],
            'lines.*.name' => ['present', 'nullable', 'string', 'max:200'],
            'lines.*.qty' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'lines.*.unit' => ['required', 'string', 'max:20'],
            'lines.*.labor' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'lines.*.materials' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'lines.*.fixed' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
        ];
    }

    /** @return array<string, mixed> */
    public static function pricing(string $prefix = ''): array
    {
        return [
            $prefix.'markup' => ['required', 'numeric', 'min:0', 'max:1000'],
            $prefix.'discount' => ['required', 'numeric', 'min:0', 'max:100'],
            $prefix.'trip' => ['required', 'numeric', 'min:0', 'max:1000000'],
            $prefix.'authorization' => ['required', 'numeric', 'min:0', 'max:100000000'],
            $prefix.'manager' => ['required', 'numeric', 'min:0', 'max:100000000'],
            $prefix.'target' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * The service must be one the company offers. A record that already uses a service
     * (even one since retired) may keep it.
     */
    public static function ensureServiceFor(Company $company, string $serviceType, ?string $current = null): void
    {
        if ($serviceType !== $current && ! in_array($serviceType, $company->serviceTypes(), true)) {
            throw ValidationException::withMessages([
                'service_type' => "{$company->name} doesn't offer {$serviceType}.",
            ]);
        }
    }

    /** @return array<int, mixed> */
    public static function serviceType(): array
    {
        return ['required', 'string', 'max:120', Rule::in(app(ServiceCatalog::class)->all())];
    }

    /**
     * Cast validated lines to clean numeric arrays.
     *
     * @param  list<array<string, mixed>>  $lines
     * @return list<array{name:string,qty:float,unit:string,labor:float,materials:float,fixed:float|null}>
     */
    public static function normalizeLines(array $lines): array
    {
        return array_map(fn (array $l) => [
            'name' => (string) ($l['name'] ?? ''),
            'qty' => (float) $l['qty'],
            'unit' => (string) $l['unit'],
            'labor' => (float) $l['labor'],
            'materials' => (float) $l['materials'],
            'fixed' => isset($l['fixed']) && $l['fixed'] !== '' ? (float) $l['fixed'] : null,
        ], $lines);
    }

    /**
     * @param  array<string, mixed>  $pricing
     * @return array<string, float|int>
     */
    public static function normalizePricing(array $pricing): array
    {
        $out = [];
        foreach (self::PRICING_KEYS as $key) {
            $value = (float) ($pricing[$key] ?? 0);
            $out[$key] = floor($value) == $value ? (int) $value : $value;
        }

        return $out;
    }
}
