<?php

namespace App\Services;

use App\Models\OrderAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderLocationFilterService
{
    /**
     * Apply geographic/location filters to an Orders Eloquent Builder or query.
     * Uses optimized WHERE EXISTS with MySQL covering indexes.
     */
    public function applyOrderFilters(Builder $query, array $filters, string $addressType = 'shipping'): Builder
    {
        $country = !empty($filters['country']) ? trim($filters['country']) : null;
        $state = !empty($filters['state']) ? trim($filters['state']) : null;
        $city = !empty($filters['city']) ? trim($filters['city']) : null;
        $area = !empty($filters['area']) ? trim($filters['area']) : null;
        $postalCode = !empty($filters['postal_code']) ? trim($filters['postal_code']) : null;

        if (!$country && !$state && !$city && !$area && !$postalCode) {
            return $query;
        }

        return $query->whereExists(function ($sub) use ($country, $state, $city, $area, $postalCode, $addressType) {
            $sub->select(DB::raw(1))
                ->from('order_addresses')
                ->whereColumn('order_addresses.order_id', 'orders.id')
                ->where('order_addresses.type', $addressType);

            if ($country) {
                $sub->where('order_addresses.country', $country);
            }
            if ($state) {
                $sub->where('order_addresses.state', $state);
            }
            if ($city) {
                $sub->where('order_addresses.city', $city);
            }
            if ($area) {
                $sub->where('order_addresses.area', $area);
            }
            if ($postalCode) {
                $sub->where('order_addresses.postal_code', $postalCode);
            }
        });
    }

    /**
     * Get cascading dynamic location options based on current filter selections.
     * Country -> States -> Cities -> Areas -> Postal Codes
     *
     * @param array $filters ['country' => ..., 'state' => ..., 'city' => ..., 'area' => ..., 'postal_code' => ...]
     * @param string $addressType Default 'shipping'
     * @return array
     */
    public function getCascadingOptions(array $filters = [], string $addressType = 'shipping'): array
    {
        $country = !empty($filters['country']) ? trim($filters['country']) : null;
        $state = !empty($filters['state']) ? trim($filters['state']) : null;
        $city = !empty($filters['city']) ? trim($filters['city']) : null;
        $area = !empty($filters['area']) ? trim($filters['area']) : null;

        $base = OrderAddress::query()->where('type', $addressType);

        // 1. Countries (always all distinct available countries)
        $countries = (clone $base)
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values()
            ->all();

        // 2. States (filtered by Country if selected)
        $stateQuery = (clone $base)
            ->whereNotNull('state')
            ->where('state', '!=', '');
        if ($country) {
            $stateQuery->where('country', $country);
        }
        $states = $stateQuery->distinct()->orderBy('state')->pluck('state')->values()->all();

        // 3. Cities (filtered by Country and State if selected)
        $cityQuery = (clone $base)
            ->whereNotNull('city')
            ->where('city', '!=', '');
        if ($country) {
            $cityQuery->where('country', $country);
        }
        if ($state) {
            $cityQuery->where('state', $state);
        }
        $cities = $cityQuery->distinct()->orderBy('city')->pluck('city')->values()->all();

        // 4. Areas (filtered by Country, State, City if selected)
        $areaQuery = (clone $base)
            ->whereNotNull('area')
            ->where('area', '!=', '');
        if ($country) {
            $areaQuery->where('country', $country);
        }
        if ($state) {
            $areaQuery->where('state', $state);
        }
        if ($city) {
            $areaQuery->where('city', $city);
        }
        $areas = $areaQuery->distinct()->orderBy('area')->pluck('area')->values()->all();

        // 5. Postal Codes (filtered by Country, State, City, Area if selected)
        $postalQuery = (clone $base)
            ->whereNotNull('postal_code')
            ->where('postal_code', '!=', '');
        if ($country) {
            $postalQuery->where('country', $country);
        }
        if ($state) {
            $postalQuery->where('state', $state);
        }
        if ($city) {
            $postalQuery->where('city', $city);
        }
        if ($area) {
            $postalQuery->where('area', $area);
        }
        $postalCodes = $postalQuery->distinct()->orderBy('postal_code')->pluck('postal_code')->values()->all();

        return [
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'areas' => $areas,
            'postal_codes' => $postalCodes,
        ];
    }

    /**
     * Reusable Geographic Summary for Future Analytics (e.g. Sales Analytics, Customer heatmaps)
     */
    public function getLocationSummary(array $filters = [], string $addressType = 'shipping'): array
    {
        $query = OrderAddress::query()
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', $addressType);

        if (!empty($filters['country'])) {
            $query->where('order_addresses.country', trim($filters['country']));
        }
        if (!empty($filters['state'])) {
            $query->where('order_addresses.state', trim($filters['state']));
        }
        if (!empty($filters['city'])) {
            $query->where('order_addresses.city', trim($filters['city']));
        }

        return [
            'top_states' => (clone $query)
                ->select('order_addresses.state', DB::raw('COUNT(DISTINCT orders.id) as orders_count'), DB::raw('SUM(orders.grand_total) as total_revenue'))
                ->groupBy('order_addresses.state')
                ->orderByDesc('orders_count')
                ->limit(10)
                ->get()
                ->toArray(),
            'top_cities' => (clone $query)
                ->select('order_addresses.city', 'order_addresses.state', DB::raw('COUNT(DISTINCT orders.id) as orders_count'), DB::raw('SUM(orders.grand_total) as total_revenue'))
                ->groupBy('order_addresses.city', 'order_addresses.state')
                ->orderByDesc('orders_count')
                ->limit(10)
                ->get()
                ->toArray(),
        ];
    }
}
