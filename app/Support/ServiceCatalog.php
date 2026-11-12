<?php

namespace App\Support;

use App\Models\ServiceGroup;
use Illuminate\Support\Collection;

/**
 * The trades and services the desk offers, read from the database once per request.
 */
class ServiceCatalog
{
    /** @var Collection<int, ServiceGroup>|null */
    private ?Collection $groups = null;

    public function refresh(): static
    {
        $this->groups = null;

        return $this;
    }

    /** @return Collection<int, ServiceGroup> */
    private function load(): Collection
    {
        return $this->groups ??= ServiceGroup::query()->with('services')->orderBy('sort')->orderBy('id')->get();
    }

    /**
     * Service names in a trade, in catalog order.
     *
     * @return list<string>
     */
    private function names(ServiceGroup $group, bool $activeOnly): array
    {
        $names = [];
        foreach ($group->services as $service) {
            if (! $activeOnly || $service->is_active) {
                $names[] = $service->name;
            }
        }

        return $names;
    }

    /**
     * Active service names in catalog order.
     *
     * @return list<string>
     */
    public function active(): array
    {
        $names = [];
        foreach ($this->load() as $group) {
            array_push($names, ...$this->names($group, true));
        }

        return $names;
    }

    /**
     * Every service name, including retired ones.
     *
     * @return list<string>
     */
    public function all(): array
    {
        $names = [];
        foreach ($this->load() as $group) {
            array_push($names, ...$this->names($group, false));
        }

        return $names;
    }

    /** Icon of the trade a service belongs to (for companies without a logo). */
    public function iconFor(?string $service): string
    {
        foreach ($this->load() as $group) {
            if ($service !== null && in_array($service, $this->names($group, false), true)) {
                return $group->icon;
            }
        }

        return 'wrench';
    }

    /**
     * Trade name => its active services, skipping trades with none.
     *
     * @return array<string, list<string>>
     */
    public function groups(): array
    {
        $out = [];
        foreach ($this->groupCards() as $card) {
            $out[$card['name']] = $card['services'];
        }

        return $out;
    }

    /**
     * Trades that have active services, with their display icon and colour.
     *
     * @return list<array{name:string,icon:string,color:string,services:list<string>}>
     */
    public function groupCards(): array
    {
        $cards = [];
        foreach ($this->load() as $group) {
            $names = $this->names($group, true);
            if ($names !== []) {
                $cards[] = ['name' => $group->name, 'icon' => $group->icon, 'color' => $group->color, 'services' => $names];
            }
        }

        return $cards;
    }
}
