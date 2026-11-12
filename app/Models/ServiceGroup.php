<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A trade (e.g. HVAC, Roofing) grouping related service types in the catalog.
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property string $color
 * @property int $sort
 */
#[Fillable(['name', 'icon', 'color', 'sort'])]
class ServiceGroup extends Model
{
    public const ICONS = ['wind', 'bug', 'house', 'wrench', 'zap', 'droplet', 'leaf', 'paintbrush', 'hammer', 'sparkles', 'flame', 'trees'];

    /** @return HasMany<ServiceType, $this> */
    public function services(): HasMany
    {
        return $this->hasMany(ServiceType::class)->orderBy('sort')->orderBy('id');
    }
}
