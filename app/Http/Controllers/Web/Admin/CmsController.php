<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
class CmsController extends Controller {
    public function index() { $pages=CmsPage::with('sections')->latest('id')->paginate(25); return view('admin.cms.index',compact('pages')); }
    public function update(Request $request, CmsPage $page) {
        $page->update($request->validate(['title'=>'required|max:255','meta_title'=>'nullable|max:255','meta_description'=>'nullable|max:500','status'=>'required|max:30','published_at'=>'nullable|date']));
        return back()->with('success','CMS page updated.');
    }
}
