<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A service estimates and templates can be filed under. Retired (inactive) services
 * stay on old estimates but can't be chosen for new work.
 *
 * @property int $id
 * @property int $service_group_id
 * @property string $name
 * @property int $sort
 * @property bool $is_active
 * @property-read ServiceGroup $group
 */
#[Fillable(['service_group_id', 'name', 'sort', 'is_active'])]
class ServiceType extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** @return BelongsTo<ServiceGroup, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class, 'service_group_id');
    }
}
