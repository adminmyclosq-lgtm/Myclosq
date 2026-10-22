<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Services\AdminExcelExportService;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Inventory Dashboard & Stock Levels Table with 6 KPI Cards.
     */
    public function index(Request $request)
    {
        $data = $this->inventoryService->getInventoryDashboard($request);
        $categories = Category::pluck('name', 'id');

        return view('admin.inventory.index', [
            'kpis' => $data['kpis'],
            'items' => $data['items'],
            'warehouses' => $data['warehouses'],
            'categories' => $categories,
            'filters' => $data['filters'],
        ]);
    }

    /**
     * 6-Tab Product Inventory Details Hub.
     */
    public function show(Inventory $inventory)
    {
        $details = $this->inventoryService->getInventoryDetails($inventory);

        return view('admin.inventory.show', $details);
    }

    /**
     * Dedicated Global Stock Movements / Audit History Feed.
     */
    public function history(Request $request)
    {
        $movements = $this->inventoryService->getMovementsHistory($request);

        return view('admin.inventory.history', compact('movements'));
    }

    /**
     * Process Manual Stock Adjustment with logging.
     */
    public function adjust(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'adjustment_mode' => 'required|string|in:add,remove,set',
            'reason' => 'required|string|in:manual_adjustment,purchase,damage,refund,transfer,recount',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $this->inventoryService->adjustStock($inventory, $validated, auth()->id());

        return back()->with('success', 'Stock adjustment recorded successfully.');
    }

    /**
     * Export Inventory Stock Levels to CSV.
     */
    public function export(Request $request, AdminExcelExportService $exporter)
    {
        $dataset = $this->inventoryService->getExportDataset($request);
        $filename = 'inventory-stock-levels-' . now()->format('Y-m-d-His');

        return $exporter->download($filename, $dataset['headers'], $dataset['rows']);
    }
}
