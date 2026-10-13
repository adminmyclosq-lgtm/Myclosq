<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAddress;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AddressAnalyticsService
{
    /**
     * Orders count and share grouped by Country.
     */
    public function getOrdersByCountry(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 20): Collection
    {
        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.country, ''), 'India') as location"),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(orders.grand_total) as total_revenue'),
                DB::raw('AVG(orders.grand_total) as average_order_value')
            );

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        $results = $query->groupBy('location')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get();

        $totalOrders = $results->sum('order_count');

        return $results->map(function ($row) use ($totalOrders) {
            $row->order_share_pct = $totalOrders > 0 ? round(($row->order_count / $totalOrders) * 100, 2) : 0;
            $row->total_revenue = round((float)$row->total_revenue, 2);
            $row->average_order_value = round((float)$row->average_order_value, 2);
            return $row;
        });
    }

    /**
     * Orders count and share grouped by State / Province.
     */
    public function getOrdersByState(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $country = null, int $limit = 20): Collection
    {
        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.state, ''), 'Unknown State') as state"),
                DB::raw("COALESCE(NULLIF(order_addresses.country, ''), 'India') as country"),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(orders.grand_total) as total_revenue'),
                DB::raw('AVG(orders.grand_total) as average_order_value')
            );

        if ($country) {
            $query->where('order_addresses.country', $country);
        }

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        $results = $query->groupBy('state', 'country')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get();

        $totalOrders = $results->sum('order_count');

        return $results->map(function ($row) use ($totalOrders) {
            $row->order_share_pct = $totalOrders > 0 ? round(($row->order_count / $totalOrders) * 100, 2) : 0;
            $row->total_revenue = round((float)$row->total_revenue, 2);
            $row->average_order_value = round((float)$row->average_order_value, 2);
            return $row;
        });
    }

    /**
     * Orders count and share grouped by City.
     */
    public function getOrdersByCity(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $state = null, int $limit = 20): Collection
    {
        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.city, ''), 'Unknown City') as city"),
                DB::raw("COALESCE(NULLIF(order_addresses.state, ''), 'Unknown State') as state"),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(orders.grand_total) as total_revenue'),
                DB::raw('AVG(orders.grand_total) as average_order_value')
            );

        if ($state) {
            $query->where('order_addresses.state', $state);
        }

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        $results = $query->groupBy('city', 'state')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get();

        $totalOrders = $results->sum('order_count');

        return $results->map(function ($row) use ($totalOrders) {
            $row->order_share_pct = $totalOrders > 0 ? round(($row->order_count / $totalOrders) * 100, 2) : 0;
            $row->total_revenue = round((float)$row->total_revenue, 2);
            $row->average_order_value = round((float)$row->average_order_value, 2);
            return $row;
        });
    }

    /**
     * Top Revenue generating Cities.
     */
    public function getRevenueByCity(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 20): Collection
    {
        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.city, ''), 'Unknown City') as city"),
                DB::raw("COALESCE(NULLIF(order_addresses.state, ''), 'Unknown State') as state"),
                DB::raw('SUM(orders.grand_total) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(orders.grand_total) as average_order_value'),
                DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as realized_revenue")
            );

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        return $query->groupBy('city', 'state')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->revenue = round((float)$row->revenue, 2);
                $row->realized_revenue = round((float)$row->realized_revenue, 2);
                $row->average_order_value = round((float)$row->average_order_value, 2);
                return $row;
            });
    }

    /**
     * Top Revenue generating States.
     */
    public function getRevenueByState(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 20): Collection
    {
        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.state, ''), 'Unknown State') as state"),
                DB::raw('SUM(orders.grand_total) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(orders.grand_total) as average_order_value'),
                DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as realized_revenue")
            );

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        return $query->groupBy('state')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->revenue = round((float)$row->revenue, 2);
                $row->realized_revenue = round((float)$row->realized_revenue, 2);
                $row->average_order_value = round((float)$row->average_order_value, 2);
                return $row;
            });
    }

    /**
     * Top Delivery Areas and Localities.
     */
    public function getTopDeliveryAreas(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 20): Collection
    {
        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.area, ''), NULLIF(order_addresses.postal_code, ''), order_addresses.city) as delivery_area"),
                'order_addresses.city',
                'order_addresses.state',
                'order_addresses.postal_code',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(orders.grand_total) as total_revenue')
            );

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        return $query->groupBy('delivery_area', 'order_addresses.city', 'order_addresses.state', 'order_addresses.postal_code')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->total_revenue = round((float)$row->total_revenue, 2);
                return $row;
            });
    }

    /**
     * Average Delivery Fulfillment Time By Location.
     * Calculates duration in days/hours from order dispatch to confirmed delivery.
     */
    public function getAverageDeliveryTimeByLocation(string $groupBy = 'state', ?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 20): Collection
    {
        $column = in_array(strtolower($groupBy), ['city', 'district', 'country']) ? strtolower($groupBy) : 'state';

        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->join('shipments', 'shipments.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->whereNotNull('shipments.delivered_at')
            ->select(
                "order_addresses.{$column} as location",
                DB::raw('COUNT(DISTINCT orders.id) as delivered_shipments_count'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, COALESCE(shipments.dispatch_date, shipments.created_at), shipments.delivered_at)) as avg_transit_hours'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, orders.created_at, shipments.delivered_at)) as avg_order_to_door_hours'),
                DB::raw('MIN(TIMESTAMPDIFF(HOUR, COALESCE(shipments.dispatch_date, shipments.created_at), shipments.delivered_at)) as min_transit_hours'),
                DB::raw('MAX(TIMESTAMPDIFF(HOUR, COALESCE(shipments.dispatch_date, shipments.created_at), shipments.delivered_at)) as max_transit_hours')
            );

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        return $query->groupBy("order_addresses.{$column}")
            ->orderBy('avg_transit_hours')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->avg_transit_hours = round((float)$row->avg_transit_hours, 1);
                $row->avg_transit_days = round($row->avg_transit_hours / 24, 1);
                $row->avg_order_to_door_days = round(((float)$row->avg_order_to_door_hours) / 24, 1);
                return $row;
            });
    }

    /**
     * Refund Rate By Location (Order count & value percentage).
     */
    public function getRefundRateByLocation(string $groupBy = 'state', ?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 20): Collection
    {
        $column = in_array(strtolower($groupBy), ['city', 'country']) ? strtolower($groupBy) : 'state';

        $query = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->select(
                "order_addresses.{$column} as location",
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw("COUNT(DISTINCT CASE WHEN orders.refund_status IN ('partially_refunded', 'refunded') OR orders.refund_amount > 0 THEN orders.id ELSE NULL END) as refunded_orders_count"),
                DB::raw('SUM(orders.grand_total) as total_order_value'),
                DB::raw('SUM(COALESCE(orders.refund_amount, 0)) as total_refunded_value')
            );

        $this->applyDateFilters($query, 'orders.created_at', $startDate, $endDate);

        return $query->groupBy("order_addresses.{$column}")
            ->orderByDesc('total_orders')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->order_refund_rate_pct = $row->total_orders > 0 
                    ? round(($row->refunded_orders_count / $row->total_orders) * 100, 2) 
                    : 0;

                $totalVal = (float)$row->total_order_value;
                $refVal = (float)$row->total_refunded_value;
                $row->value_refund_rate_pct = $totalVal > 0 
                    ? round(($refVal / $totalVal) * 100, 2) 
                    : 0;

                $row->total_order_value = round($totalVal, 2);
                $row->total_refunded_value = round($refVal, 2);
                return $row;
            });
    }

    /**
     * Coordinate Heatmap Data for interactive map overlays.
     */
    public function getGeoHeatmapData(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Reference coordinates for standard Indian cities when address latitude/longitude are not yet captured
        $cityCentroids = [
            'hyderabad' => [17.3850, 78.4867],
            'secunderabad' => [17.4399, 78.4983],
            'bengaluru' => [12.9716, 77.5946],
            'bangalore' => [12.9716, 77.5946],
            'mumbai' => [19.0760, 72.8777],
            'pune' => [18.5204, 73.8567],
            'delhi' => [28.6139, 77.2090],
            'new delhi' => [28.6139, 77.2090],
            'chennai' => [13.0827, 80.2707],
            'kolkata' => [22.5726, 88.3639],
            'ahmedabad' => [23.0225, 72.5714],
            'jaipur' => [26.9124, 75.7873],
            'lucknow' => [26.8467, 80.9462],
            'chandigarh' => [30.7333, 76.7794],
            'kochi' => [9.9312, 76.2673],
        ];

        $query = OrderAddress::where('type', 'shipping')
            ->with(['order:id,order_number,grand_total,created_at,payment_status']);

        $addresses = $query->get();

        $points = [];
        foreach ($addresses as $addr) {
            $lat = $addr->latitude ? (float)$addr->latitude : null;
            $lng = $addr->longitude ? (float)$addr->longitude : null;

            if (is_null($lat) || is_null($lng)) {
                $cityKey = strtolower(trim($addr->city));
                if (isset($cityCentroids[$cityKey])) {
                    $lat = $cityCentroids[$cityKey][0];
                    $lng = $cityCentroids[$cityKey][1];
                }
            }

            if (!is_null($lat) && !is_null($lng)) {
                $points[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'city' => $addr->city,
                    'state' => $addr->state,
                    'order_number' => $addr->order?->order_number ?? 'Order #' . $addr->order_id,
                    'revenue' => (float)($addr->order?->grand_total ?? 0),
                    'payment_status' => $addr->order?->payment_status ?? 'unknown',
                    'weight' => max(1, (float)($addr->order?->grand_total ?? 1)),
                ];
            }
        }

        return $points;
    }

    /**
     * Standard RFC 7946 GeoJSON FeatureCollection for map visualizers.
     */
    public function toGeoJsonCollection(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $heatmap = $this->getGeoHeatmapData($startDate, $endDate);

        $features = array_map(function ($point) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$point['lng'], $point['lat']],
                ],
                'properties' => [
                    'city' => $point['city'],
                    'state' => $point['state'],
                    'order_number' => $point['order_number'],
                    'revenue' => $point['revenue'],
                    'payment_status' => $point['payment_status'],
                ],
            ];
        }, $heatmap);

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    /**
     * Helper to apply optional date bounds.
     */
    protected function applyDateFilters($query, string $column, ?Carbon $startDate, ?Carbon $endDate): void
    {
        if ($startDate) {
            $query->where($column, '>=', $startDate->startOfDay());
        }
        if ($endDate) {
            $query->where($column, '<=', $endDate->endOfDay());
        }
    }
}
