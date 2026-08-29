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

    public function sections(CmsPage $page) {
        $page->load('sections');
        return view('admin.cms.sections', compact('page'));
    }

    public function storeSection(Request $request, CmsPage $page) {
        $rules = [
            'section_type' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'sort_order' => 'required|integer',
            'image' => 'nullable|image|max:5120'
        ];
        $data = $request->validate($rules);
        
        $content = $request->input('content');
        if (is_array($content)) {
            $data['content'] = json_encode($content);
        } else {
            $data['content'] = $content;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cms', 'public');
            $media = \App\Models\Media::create([
                'file_name' => $request->file('image')->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $request->file('image')->getMimeType(),
                'file_size' => $request->file('image')->getSize(),
                'uploaded_by' => auth()->id()
            ]);
            $data['media_id'] = $media->id;
        }

        unset($data['image']);
        $page->sections()->create($data);
        return back()->with('success', 'Section added successfully.');
    }

    public function updateSection(Request $request, \App\Models\CmsSection $section) {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'sort_order' => 'required|integer',
            'image' => 'nullable|image|max:5120'
        ];
        $data = $request->validate($rules);
        
        $content = $request->input('content');
        if (is_array($content)) {
            $data['content'] = json_encode($content);
        } else {
            $data['content'] = $content;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cms', 'public');
            $media = \App\Models\Media::create([
                'file_name' => $request->file('image')->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $request->file('image')->getMimeType(),
                'file_size' => $request->file('image')->getSize(),
                'uploaded_by' => auth()->id()
            ]);
            $data['media_id'] = $media->id;
        }

        unset($data['image']);
        $section->update($data);
        return back()->with('success', 'Section updated successfully.');
    }

    public function reorderSections(Request $request, CmsPage $page) {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:cms_sections,id'
        ]);

        foreach ($data['order'] as $index => $id) {
            $page->sections()->where('id', $id)->update(['sort_order' => $index * 10]);
        }

        return response()->json(['success' => true]);
    }

    public function duplicateSection(\App\Models\CmsSection $section) {
        $newSection = $section->replicate();
        $newSection->sort_order = $section->sort_order + 5; 
        $newSection->push();
        return back()->with('success', 'Section duplicated successfully.');
    }

    public function destroySection(\App\Models\CmsSection $section) {
        $section->delete();
        return back()->with('success', 'Section deleted successfully.');
    }
}
