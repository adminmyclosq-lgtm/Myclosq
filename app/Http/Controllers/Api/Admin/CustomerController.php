<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
class CustomerController extends Controller {
    public function index(Request $request) {
        return User::with('customerProfile')->whereHas('roles',fn($q)=>$q->where('code','CUSTOMER'))
            ->when($request->search,fn($q,$v)=>$q->where(fn($x)=>$x->where('email','like',"%{$v}%")->orWhere('mobile','like',"%{$v}%")))
            ->latest('id')->paginate(30);
    }
    public function show(User $user) { return response()->json($user->load('customerProfile','addresses','orders')); }
}
