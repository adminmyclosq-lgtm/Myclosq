<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
        return CmsPage::with('sections')->latest('id')->paginate(30);
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $cmsPage->update($request->validate([
            'title' => ['sometimes','string','max:255'],
            'slug' => ['sometimes','string','max:255'],
            'status' => ['sometimes','string','max:30'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:500'],
        ]));

        return response()->json($cmsPage->fresh('sections'));
    }
}
