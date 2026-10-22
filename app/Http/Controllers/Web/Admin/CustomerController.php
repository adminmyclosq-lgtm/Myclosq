<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerNote;
use App\Models\User;
use App\Services\AdminExcelExportService;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display Customer List with 6 KPI Cards, search, advanced filters, and data table.
     */
    public function index(Request $request)
    {
        $data = $this->customerService->getCustomerList($request);

        return view('admin.customers.index', [
            'customers' => $data['customers'],
            'kpis' => $data['kpis'],
            'filterOptions' => $data['filterOptions'],
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Display 360-degree Customer Details Hub with 8 Live Tabs.
     */
    public function show(User $user, Request $request)
    {
        $details = $this->customerService->getCustomerDetails($user);

        return view('admin.customers.show', $details);
    }

    /**
     * Export customer dataset based on active filters.
     */
    public function export(Request $request, AdminExcelExportService $exporter)
    {
        $dataset = $this->customerService->getExportDataset($request);
        $filename = 'customers-crm-' . now()->format('Y-m-d-His');

        return $exporter->download($filename, $dataset['headers'], $dataset['rows']);
    }

    /**
     * Handle bulk operations on customers (tags, status, VIP).
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:status,add_tag,mark_vip',
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'integer|exists:users,id',
            'payload' => 'nullable|string',
        ]);

        $count = $this->customerService->bulkAction(
            $validated['action'],
            $validated['customer_ids'],
            $validated['payload'] ?? null,
            auth()->id()
        );

        return redirect()->back()->with('success', "Bulk action applied successfully to {$count} customer(s).");
    }

    /**
     * Store new CRM Note.
     */
    public function storeNote(Request $request, User $user)
    {
        $validated = $request->validate([
            'note' => 'required|string|min:2|max:3000',
            'category' => 'nullable|string|in:general,call,email,support,follow_up,urgent',
            'is_pinned' => 'nullable|boolean',
        ]);

        $this->customerService->storeNote($user, $validated, auth()->id());

        return redirect()->to(route('admin.customers.show', $user->id) . '#tab-notes')
            ->with('success', 'CRM Note recorded successfully.');
    }

    /**
     * Delete CRM Note.
     */
    public function destroyNote(User $user, CustomerNote $note)
    {
        $this->customerService->deleteNote($user, $note);

        return redirect()->to(route('admin.customers.show', $user->id) . '#tab-notes')
            ->with('success', 'Note removed.');
    }

    /**
     * Update customer tags.
     */
    public function updateTags(Request $request, User $user)
    {
        $validated = $request->validate([
            'tags' => 'nullable|string|max:1000',
        ]);

        $this->customerService->updateTags($user, $validated['tags'] ?? '');

        return redirect()->back()->with('success', 'Customer tags updated.');
    }

    /**
     * Update customer account status.
     */
    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:active,inactive,suspended',
        ]);

        $user->status = $validated['status'];
        $user->save();

        return redirect()->back()->with('success', "Customer status updated to {$user->status}.");
    }
}
