<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $estimate_id
 * @property int $company_id
 * @property int|null $user_id
 * @property string|null $actor_name
 * @property string $action submitted|withdrawn|approved|declined|reverted_to_draft|shared|unshared|customer_authorized|authorization_cleared
 * @property string|null $from_status
 * @property string $to_status
 * @property CarbonImmutable $created_at
 * @property-read Estimate $estimate
 * @property-read User|null $user
 * @property-read Company $company
 */
#[Fillable(['estimate_id', 'company_id', 'user_id', 'actor_name', 'action', 'from_status', 'to_status'])]
class EstimateActivity extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    /** @return BelongsTo<Estimate, $this> */
    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
