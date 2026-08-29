<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InventoryController extends Controller {
    public function index() { $inventory=Inventory::with('productVariant.product')->orderBy('id')->paginate(30); return view('admin.inventory.index',compact('inventory')); }
    public function adjust(Request $request, Inventory $inventory) {
        $data=$request->validate(['quantity'=>'required|integer','remarks'=>'nullable|string|max:500']);
        DB::transaction(function() use($inventory,$data) {
            $inventory->refresh();
            $inventory->quantity_on_hand += $data['quantity'];
            $inventory->save();
            $inventory->transactions()->create([
                'transaction_type'=>'adjustment','quantity'=>$data['quantity'],'reference_type'=>'admin_adjustment',
                'reference_id'=>$inventory->id,'balance_after'=>$inventory->quantity_on_hand-$inventory->quantity_reserved,
                'remarks'=>$data['remarks']??'Admin inventory adjustment','created_by'=>auth()->id(),'created_at'=>now()
            ]);
        });
        return back()->with('success','Inventory adjusted.');
    }
}
