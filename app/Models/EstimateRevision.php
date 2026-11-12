<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $estimate_id
 * @property int $user_id
 * @property array<string, mixed> $snapshot
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read User $user
 */
#[Fillable(['estimate_id', 'user_id', 'snapshot'])]
class EstimateRevision extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'created_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
