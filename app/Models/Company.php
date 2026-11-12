<?php

namespace App\Models;

use App\Support\ServiceCatalog;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $short_name
 * @property string $location
 * @property string $currency
 * @property string $color
 * @property string $tone
 * @property string|null $logo_path
 * @property string|null $default_scope
 * @property string|null $default_terms
 * @property array<string, int|float> $pricing_defaults
 * @property list<string>|null $service_types
 */
#[Fillable(['name', 'slug', 'short_name', 'location', 'currency', 'color', 'tone', 'logo_path', 'default_scope', 'default_terms', 'pricing_defaults', 'service_types'])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pricing_defaults' => 'array',
            'service_types' => 'array',
        ];
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Active services this company offers, in catalog order. Null means every active service.
     *
     * @return list<string>
     */
    public function serviceTypes(): array
    {
        $all = app(ServiceCatalog::class)->active();

        return $this->service_types === null
            ? $all
            : array_values(array_intersect($all, $this->service_types));
    }

    /** @return HasMany<ServiceTemplate, $this> */
    public function templates(): HasMany
    {
        return $this->hasMany(ServiceTemplate::class);
    }

    /** @return HasMany<Estimate, $this> */
    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    /** Short name as a link-friendly word, used in customer share links (e.g. "pest-control"). */
    public function linkSlug(): string
    {
        return Str::slug($this->short_name) ?: 'company';
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    /**
     * @return array{id:int,name:string,short:string,color:string,tone:string,location:string,logoUrl:?string,services:list<string>,icon:string}
     */
    public function summary(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short' => $this->short_name,
            'color' => $this->color,
            'tone' => $this->tone,
            'location' => $this->location,
            'logoUrl' => $this->logoUrl(),
            'services' => $services = $this->serviceTypes(),
            'icon' => app(ServiceCatalog::class)->iconFor($services[0] ?? null),
        ];
    }
}
