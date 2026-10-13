<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\AddressAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressAnalyticsController extends Controller
{
    public function __construct(
        protected AddressAnalyticsService $analytics
    ) {}

    /**
     * Aggregated Location & Shipping Analytics API.
     */
    public function index(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'orders_by_country' => $this->analytics->getOrdersByCountry($startDate, $endDate),
                'orders_by_state' => $this->analytics->getOrdersByState($startDate, $endDate),
                'orders_by_city' => $this->analytics->getOrdersByCity($startDate, $endDate),
                'revenue_by_city' => $this->analytics->getRevenueByCity($startDate, $endDate),
                'revenue_by_state' => $this->analytics->getRevenueByState($startDate, $endDate),
                'top_delivery_areas' => $this->analytics->getTopDeliveryAreas($startDate, $endDate),
                'delivery_time_by_state' => $this->analytics->getAverageDeliveryTimeByLocation('state', $startDate, $endDate),
                'refund_rate_by_state' => $this->analytics->getRefundRateByLocation('state', $startDate, $endDate),
            ],
        ]);
    }

    /**
     * Map Heatmap Points API for interactive maps.
     */
    public function heatmap(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

        return response()->json([
            'success' => true,
            'points' => $this->analytics->getGeoHeatmapData($startDate, $endDate),
        ]);
    }

    /**
     * GeoJSON FeatureCollection endpoint.
     */
    public function geojson(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : null;

        return response()->json(
            $this->analytics->toGeoJsonCollection($startDate, $endDate)
        );
    }
}
