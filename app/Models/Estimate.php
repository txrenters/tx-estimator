<?php

namespace App\Models;

use App\Enums\EstimateStatus;
use App\Support\EstimateNumber;
use App\Support\Pricing;
use Carbon\CarbonImmutable;
use Database\Factories\EstimateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property string $number
 * @property string $title
 * @property EstimateStatus $status
 * @property string $service_type
 * @property string|null $client
 * @property string|null $address
 * @property list<array<string, mixed>> $lines
 * @property array<string, int|float> $pricing
 * @property string|null $scope
 * @property string|null $terms
 * @property string|null $notes
 * @property string $cost
 * @property string $subtotal
 * @property string $discount
 * @property string $total
 * @property string|null $margin
 * @property bool $incomplete
 * @property int|null $approved_by
 * @property CarbonImmutable|null $approved_at
 * @property string|null $share_token
 * @property CarbonImmutable|null $share_expires_at
 * @property CarbonImmutable|null $shared_at
 * @property int $share_views
 * @property CarbonImmutable|null $share_viewed_at
 * @property string|null $authorized_name
 * @property CarbonImmutable|null $authorized_at
 * @property string|null $authorized_ip
 * @property-read Company $company
 * @property-read User $creator
 * @property-read User|null $approver
 */
#[Fillable(['company_id', 'user_id', 'number', 'title', 'status', 'service_type', 'client', 'address', 'lines', 'pricing', 'scope', 'terms', 'notes'])]
class Estimate extends Model
{
    /** @use HasFactory<EstimateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => EstimateStatus::class,
            'lines' => 'array',
            'pricing' => 'array',
            'incomplete' => 'boolean',
            'approved_at' => 'datetime',
            'share_expires_at' => 'datetime',
            'shared_at' => 'datetime',
            'share_viewed_at' => 'datetime',
            'authorized_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Estimate $estimate) {
            $estimate->number ??= EstimateNumber::generate();
        });
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    /** Recompute stored totals from lines and pricing (server is authoritative). */
    public function applyTotals(): void
    {
        $result = Pricing::calculate($this->lines ?? [], $this->pricing ?? []);

        $this->cost = (string) $result['cost'];
        $this->subtotal = (string) $result['subtotal'];
        $this->discount = (string) $result['discount'];
        $this->total = (string) $result['total'];
        $this->incomplete = $result['incomplete'];
        $this->margin = ($result['incomplete'] || $result['total'] <= 0) ? null : (string) round($result['margin'], 4);
    }

    /** Customer share link, e.g. https://…/p/air/m2suBSN5jBb6 (null when not shared). */
    public function shareUrl(): ?string
    {
        return $this->share_token
            ? route('proposal.public', ['company' => $this->company->linkSlug(), 'token' => $this->share_token])
            : null;
    }

    /** @return array{name:string,at:string}|null */
    public function authorization(): ?array
    {
        return $this->authorized_at
            ? ['name' => (string) $this->authorized_name, 'at' => $this->authorized_at->toIso8601String()]
            : null;
    }

    /** @return array{belowTarget:bool,aboveAuthorization:bool,aboveManagerReview:bool} */
    public function flags(): array
    {
        return Pricing::flags([
            'total' => (float) $this->total,
            'incomplete' => $this->incomplete,
            'margin' => (float) $this->margin,
        ], $this->pricing);
    }

    /** @return HasMany<EstimateActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(EstimateActivity::class);
    }

    /** @param  User|null  $by  null for customer actions, named by $actorName */
    public function logActivity(?User $by, string $action, ?EstimateStatus $from, EstimateStatus $to, ?string $actorName = null): EstimateActivity
    {
        return $this->activities()->create([
            'company_id' => $this->company_id,
            'user_id' => $by?->id,
            'actor_name' => $actorName,
            'action' => $action,
            'from_status' => $from?->value,
            'to_status' => $to->value,
        ]);
    }

    /** @return HasMany<EstimateRevision, $this> */
    public function revisions(): HasMany
    {
        return $this->hasMany(EstimateRevision::class);
    }

    public function recordRevision(User $by): EstimateRevision
    {
        return $this->revisions()->create([
            'user_id' => $by->id,
            'snapshot' => [
                'title' => $this->title,
                'status' => $this->status->value,
                'service_type' => $this->service_type,
                'client' => $this->client,
                'address' => $this->address,
                'lines' => $this->lines,
                'pricing' => $this->pricing,
                'scope' => $this->scope,
                'terms' => $this->terms,
                'notes' => $this->notes,
                'total' => (float) $this->total,
            ],
        ]);
    }
}
