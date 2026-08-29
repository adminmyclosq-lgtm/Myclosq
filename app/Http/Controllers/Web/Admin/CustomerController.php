<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
class CustomerController extends Controller {
    public function index(Request $request) {
        $customers=User::with('customerProfile')->whereHas('roles',fn($q)=>$q->where('code','CUSTOMER'))
            ->when($request->search,fn($q,$v)=>$q->where(fn($x)=>$x->where('email','like',"%{$v}%")->orWhere('mobile','like',"%{$v}%")))
            ->latest('id')->paginate(25);
        return view('admin.customers.index',compact('customers'));
    }
    public function show(User $user) {
        $user->load('customerProfile','addresses','orders');
        return view('admin.customers.show',compact('user'));
    }
}
