<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\AdminExcelExportService;
use App\Services\CouponAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function __construct(
        protected CouponAnalyticsService $couponAnalyticsService,
        protected AdminExcelExportService $excelExportService
    ) {}

    /**
     * Enterprise Coupons & Promotions Dashboard & Master Table.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        if (empty($filters['date_preset'])) {
            $filters['date_preset'] = 'last_30_days';
        }

        $kpis = $this->couponAnalyticsService->getKpis($filters);
        $charts = $this->couponAnalyticsService->getAnalyticsCharts($filters);
        $filterOptions = $this->couponAnalyticsService->getFilterOptions();

        $query = $this->couponAnalyticsService->getFilteredCouponsQuery($filters);
        $coupons = $query->paginate(15)->withQueryString();

        return view('admin.coupons.index', compact(
            'coupons',
            'kpis',
            'charts',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * Dedicated Create Coupon Page (No modals, full enterprise wizard).
     */
    public function create()
    {
        $products = Product::where('status', 'active')->orderBy('name')->get(['id', 'name', 'base_sku']);
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $vipCustomers = User::with('customerProfile')->whereNotNull('email')->take(30)->get();

        return view('admin.coupons.create', compact('products', 'categories', 'vipCustomers'));
    }

    /**
     * Store new promotion in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:80|unique:coupons,code',
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|string|in:percentage,fixed,free_shipping,bogo,bundle,first_order,referral,birthday,whatsapp,membership,cart_value',
            'discount_value' => 'required|numeric|min:0',
            'minimum_cart_value' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|string|in:active,scheduled,paused,expired,draft',
            'customer_eligibility' => 'required|string|in:all,new,returning,vip,specific',
            'customer_ids' => 'nullable|array',
            'eligible_categories' => 'nullable|array',
            'free_shipping' => 'nullable|boolean',
            'allow_stacking' => 'nullable|boolean',
            'campaign_name' => 'nullable|string|max:120',
            'internal_notes' => 'nullable|string|max:1000',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['minimum_cart_value'] = $validated['minimum_cart_value'] ?? 0;
        $validated['free_shipping'] = $request->boolean('free_shipping') || $validated['discount_type'] === 'free_shipping';
        $validated['allow_stacking'] = $request->boolean('allow_stacking');
        $validated['is_active'] = in_array($validated['status'], ['active', 'scheduled']);
        $validated['created_by'] = auth()->id();

        $productIds = $request->input('product_ids', []);
        unset($validated['product_ids']);

        $coupon = Coupon::create($validated);

        if (!empty($productIds)) {
            $coupon->products()->sync($productIds);
        }

        return redirect()->route('admin.coupons.show', $coupon)
            ->with('success', "Coupon '{$coupon->code}' successfully created and published.");
    }

    /**
     * Dedicated Coupon Details 360-degree Page (6 Tabs).
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['products', 'creator', 'usages.order', 'usages.user', 'orders.user']);
        $analytics = $this->couponAnalyticsService->getCouponDetailAnalytics($coupon);

        return view('admin.coupons.show', compact('coupon', 'analytics'));
    }

    /**
     * Dedicated Edit Coupon Page.
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::where('status', 'active')->orderBy('name')->get(['id', 'name', 'base_sku']);
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $vipCustomers = User::with('customerProfile')->whereNotNull('email')->take(30)->get();
        $selectedProductIds = $coupon->products->pluck('id')->toArray();

        return view('admin.coupons.edit', compact('coupon', 'products', 'categories', 'vipCustomers', 'selectedProductIds'));
    }

    /**
     * Update coupon settings.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:80', Rule::unique('coupons')->ignore($coupon->id)],
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|string|in:percentage,fixed,free_shipping,bogo,bundle,first_order,referral,birthday,whatsapp,membership,cart_value',
            'discount_value' => 'required|numeric|min:0',
            'minimum_cart_value' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|string|in:active,scheduled,paused,expired,draft',
            'customer_eligibility' => 'required|string|in:all,new,returning,vip,specific',
            'customer_ids' => 'nullable|array',
            'eligible_categories' => 'nullable|array',
            'free_shipping' => 'nullable|boolean',
            'allow_stacking' => 'nullable|boolean',
            'campaign_name' => 'nullable|string|max:120',
            'internal_notes' => 'nullable|string|max:1000',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['minimum_cart_value'] = $validated['minimum_cart_value'] ?? 0;
        $validated['free_shipping'] = $request->boolean('free_shipping') || $validated['discount_type'] === 'free_shipping';
        $validated['allow_stacking'] = $request->boolean('allow_stacking');
        $validated['is_active'] = in_array($validated['status'], ['active', 'scheduled']);

        $productIds = $request->input('product_ids', []);
        unset($validated['product_ids']);

        $coupon->update($validated);

        $coupon->products()->sync($productIds);

        return redirect()->route('admin.coupons.show', $coupon)
            ->with('success', "Coupon '{$coupon->code}' updated successfully.");
    }

    /**
     * Delete coupon.
     */
    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->products()->detach();
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon '{$code}' deleted successfully.");
    }

    /**
     * Quick toggle status between active and paused.
     */
    public function toggleStatus(Coupon $coupon)
    {
        $newStatus = $coupon->status === 'active' ? 'paused' : 'active';
        $coupon->update([
            'status' => $newStatus,
            'is_active' => $newStatus === 'active',
        ]);

        return back()->with('success', "Coupon '{$coupon->code}' status updated to {$newStatus}.");
    }

    /**
     * Bulk actions handler (Bulk Activate, Bulk Pause, Bulk Delete).
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $ids = $request->input('selected_ids', []);

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Please select at least one coupon.');
        }

        if ($action === 'bulk_activate') {
            Coupon::whereIn('id', $ids)->update(['status' => 'active', 'is_active' => true]);
            $msg = count($ids) . ' coupon(s) marked as Active.';
        } elseif ($action === 'bulk_pause') {
            Coupon::whereIn('id', $ids)->update(['status' => 'paused', 'is_active' => false]);
            $msg = count($ids) . ' coupon(s) paused.';
        } elseif ($action === 'bulk_delete') {
            DB::table('coupon_products')->whereIn('coupon_id', $ids)->delete();
            Coupon::whereIn('id', $ids)->delete();
            $msg = count($ids) . ' coupon(s) deleted permanently.';
        } else {
            return back()->with('error', 'Invalid bulk action specified.');
        }

        return back()->with('success', $msg);
    }

    /**
     * Export Coupons dataset to CSV / Excel.
     */
    public function export(Request $request)
    {
        $filters = $request->all();
        $query = $this->couponAnalyticsService->getFilteredCouponsQuery($filters);
        $coupons = $query->get();
        $format = strtolower($request->get('format', 'csv'));

        $headings = [
            'Coupon Code',
            'Name',
            'Type',
            'Discount Value',
            'Min Order (INR)',
            'Max Discount (INR)',
            'Total Redemptions',
            'Revenue Generated (INR)',
            'Discount Given (INR)',
            'Start Date',
            'End Date',
            'Status',
            'Campaign Name',
        ];

        $rows = $coupons->map(function ($c) {
            return [
                $c->code,
                $c->name,
                ucfirst(str_replace('_', ' ', $c->discount_type)),
                $c->formatted_discount,
                $c->minimum_cart_value,
                $c->maximum_discount ?: 'None',
                $c->usages_count,
                round((float) ($c->orders_sum_grand_total ?? 0), 2),
                round((float) ($c->usages_sum_discount_amount ?? 0), 2),
                $c->starts_at ? $c->starts_at->format('Y-m-d H:i') : 'Immediate',
                $c->expires_at ? $c->expires_at->format('Y-m-d H:i') : 'Never',
                ucfirst($c->calculated_status),
                $c->campaign_name ?: '-',
            ];
        })->toArray();

        $filename = 'coupons_export_' . now()->format('Y_m_d_His');

        if ($format === 'excel' || $format === 'xls') {
            return $this->excelExportService->download($filename, $headings, $rows);
        }

        return $this->excelExportService->downloadCsv($filename, $headings, $rows);
    }
}
