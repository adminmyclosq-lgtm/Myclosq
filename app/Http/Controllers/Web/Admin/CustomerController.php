<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminExcelExportService;
use Illuminate\Http\Request;
class CustomerController extends Controller {
    public function index(Request $request) {
        $customers=User::with('customerProfile')->whereHas('roles',fn($q)=>$q->where('code','CUSTOMER'))
            ->when($request->search,fn($q,$v)=>$q->where(fn($x)=>$x->where('email','like',"%{$v}%")->orWhere('mobile','like',"%{$v}%")))
            ->latest('id')->paginate(25);
        return view('admin.customers.index',compact('customers'));
    }
    public function export(Request $request, AdminExcelExportService $exporter) {
        $customers=User::with('customerProfile')->whereHas('roles',fn($q)=>$q->where('code','CUSTOMER'))
            ->when($request->search,fn($q,$v)=>$q->where(fn($x)=>$x->where('email','like',"%{$v}%")->orWhere('mobile','like',"%{$v}%")))
            ->latest('id')->get();
        return $exporter->download('customers-'.now()->format('Y-m-d'), ['Customer', 'Email', 'Mobile', 'Status', 'WhatsApp'], $customers->map(fn(User $customer)=>[
            $customer->customerProfile?->display_name ?? $customer->email, $customer->email, $customer->mobile, $customer->status, $customer->customerProfile?->whatsapp_opt_in ? 'Opted in' : 'No',
        ]));
    }
    public function show(User $user) {
        $user->load('customerProfile','addresses','orders');
        return view('admin.customers.show',compact('user'));
    }
}
