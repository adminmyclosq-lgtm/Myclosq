<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\Media;
use Illuminate\Http\Request;
class CmsController extends Controller {
    public function index() { $pages=CmsPage::with('sections')->latest('id')->paginate(25); return view('admin.cms.index',compact('pages')); }
    public function update(Request $request, CmsPage $page) {
        $page->update($request->validate(['title'=>'required|max:255','meta_title'=>'nullable|max:255','meta_description'=>'nullable|max:500','status'=>'required|max:30','published_at'=>'nullable|date']));
        return back()->with('success','CMS page updated.');
    }

    public function global() {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'global'],
            ['title' => 'Global Components', 'status' => 'published', 'page_type' => 'custom']
        );
        return redirect()->route('admin.cms.sections', $page);
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
            'image' => 'nullable|image|max:5120',
            'header_logo' => 'nullable|image|max:2048'
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

        if ($request->hasFile('header_logo')) {
            $contentData = is_string($data['content'] ?? null) ? json_decode($data['content'], true) : [];
            $contentData = is_array($contentData) ? $contentData : [];
            $contentData['header_logo_media_id'] = $this->storeMedia($request->file('header_logo'))->id;
            $data['content'] = json_encode($contentData);
        }

        unset($data['image'], $data['header_logo']);
        $page->sections()->create($data);
        return back()->with('success', 'Section added successfully.');
    }

    public function updateSection(Request $request, \App\Models\CmsSection $section) {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'sort_order' => 'required|integer',
            'image' => 'nullable|image|max:5120',
            'header_logo' => 'nullable|image|max:2048'
        ];
        $data = $request->validate($rules);
        
        $content = $request->input('content');
        if (is_array($content)) {
            $existingContent = is_string($section->content) ? json_decode($section->content, true) : [];
            if (!is_array($existingContent)) $existingContent = [];
            
            if ($request->has('_is_settings_form')) {
                $existingContent['settings'] = $content['settings'] ?? [];
                $data['content'] = json_encode($existingContent);
            } else {
                $content['settings'] = $existingContent['settings'] ?? [];
                $data['content'] = json_encode($content);
            }
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

        if ($request->hasFile('header_logo')) {
            $contentData = is_string($data['content'] ?? null) ? json_decode($data['content'], true) : [];
            $contentData = is_array($contentData) ? $contentData : [];
            $contentData['header_logo_media_id'] = $this->storeMedia($request->file('header_logo'))->id;
            $data['content'] = json_encode($contentData);
        }

        unset($data['image'], $data['header_logo']);
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

    private function storeMedia($file): Media
    {
        $path = $file->store('cms', 'public');

        return Media::create([
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
