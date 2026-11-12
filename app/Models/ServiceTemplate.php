<?php

namespace App\Models;

use Database\Factories\ServiceTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $company_id
 * @property string|null $key
 * @property string $title
 * @property string $service_type
 * @property string|null $scope
 * @property string|null $terms
 * @property list<array<string, mixed>> $lines
 * @property-read Company $company
 */
#[Fillable(['company_id', 'key', 'title', 'service_type', 'scope', 'terms', 'lines'])]
class ServiceTemplate extends Model
{
    /** @use HasFactory<ServiceTemplateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['lines' => 'array'];
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param  Builder<self>  $query
     * @param  list<int>|null  $companyIds  null = every company
     */
    public function scopeInCompanies(Builder $query, ?array $companyIds): void
    {
        if ($companyIds !== null) {
            $query->whereIn('company_id', $companyIds);
        }
    }
}
