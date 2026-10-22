<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    public function index()
    {
        $pages = CmsPage::with('sections')->latest('id')->paginate(25);
        return view('admin.cms.index', compact('pages'));
    }

    public function storePage(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:220|unique:cms_pages,slug',
            'page_type' => 'required|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
        ]);

        $data['slug'] = Str::slug($data['slug']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $page = CmsPage::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'page' => $page]);
        }

        return back()->with('success', 'CMS page created successfully.');
    }

    public function update(Request $request, CmsPage $page)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'page_type' => 'nullable|string|max:50',
        ];

        // Only allow slug updates for non-home pages
        if ($page->slug !== 'home' && $request->has('slug')) {
            $rules['slug'] = 'required|string|max:220|unique:cms_pages,slug,' . $page->id;
        }

        $data = $request->validate($rules);
        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }
        $data['updated_by'] = auth()->id();

        if ($data['status'] === 'published' && empty($page->published_at) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $page->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'page' => $page,
                'message' => 'Page details saved successfully.'
            ]);
        }

        return back()->with('success', 'CMS page details updated successfully.');
    }

    public function destroyPage(CmsPage $page)
    {
        if ($page->slug === 'home') {
            return back()->withErrors(['error' => 'The Home page cannot be deleted.']);
        }
        $page->sections()->delete();
        $page->delete();
        return back()->with('success', 'CMS page and its sections deleted successfully.');
    }

    public function sections(CmsPage $page)
    {
        $page->load(['sections' => function ($q) {
            $q->with('media')->orderBy('sort_order', 'asc');
        }]);

        // Preload recent media for instant picker
        $recentMedia = Media::latest('id')->limit(24)->get();

        return view('admin.cms.sections', compact('page', 'recentMedia'));
    }

    public function preview(CmsPage $page)
    {
        $page->load(['sections' => function ($q) {
            $q->with('media')->orderBy('sort_order', 'asc');
        }]);

        $products = null;
        $headerLogo = null;

        if ($page->slug === 'home') {
            $products = Product::with(['productImages.media', 'variants.prices'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->limit(4)
                ->get();

            $heroContent = $page->sections->firstWhere('section_type', 'hero')?->content;
            $heroContent = is_string($heroContent) ? json_decode($heroContent, true) : [];
            if (!empty($heroContent['header_logo_media_id'])) {
                $headerLogo = Media::find($heroContent['header_logo_media_id']);
            }
        }

        return response()
            ->view('admin.cms.preview', compact('page', 'products', 'headerLogo'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function storeSection(Request $request, CmsPage $page)
    {
        $rules = [
            'section_type' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:10000',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
            'media_id' => 'nullable|integer|exists:media,id',
            'image_position' => 'nullable|string|in:left,right,background,none',
            'is_active' => 'nullable|boolean',
        ];

        $data = $request->validate($rules);

        // Sort order calculation
        if (empty($data['sort_order'])) {
            $maxOrder = $page->sections()->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 10;
        }

        $data['is_active'] = $request->boolean('is_active', true);
        if (empty($data['image_position'])) {
            $data['image_position'] = 'right';
        }

        // Process content
        $content = $request->input('content');
        if (is_array($content)) {
            $data['content'] = json_encode($content);
        } elseif (is_string($content) && !empty($content)) {
            $data['content'] = $content;
        } else {
            $data['content'] = json_encode([
                'settings' => [
                    'top_spacing' => '80px',
                    'bottom_spacing' => '80px',
                    'bg_color' => '',
                    'container_width' => 'standard',
                    'alignment' => 'left'
                ]
            ]);
        }

        if ($request->hasFile('image')) {
            $media = $this->storeMedia($request->file('image'));
            $data['media_id'] = $media->id;
        }

        unset($data['image']);

        $section = $page->sections()->create($data);
        $section->load('media');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'section' => $section,
                'message' => 'Section added successfully.'
            ]);
        }

        return back()->with('success', 'Section added successfully.');
    }

    public function updateSection(Request $request, CmsSection $section)
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:10000',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
            'media_id' => 'nullable|integer|exists:media,id',
            'image_position' => 'nullable|string|in:left,right,background,none',
            'is_active' => 'nullable|boolean',
        ];

        $data = $request->validate($rules);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $content = $request->input('content');
        if (is_array($content)) {
            $existingContent = is_string($section->content) ? json_decode($section->content, true) : (is_array($section->content) ? $section->content : []);
            if (!is_array($existingContent)) {
                $existingContent = [];
            }

            if (isset($content['settings']) && is_array($content['settings'])) {
                // Ensure checkbox flags are cleanly normalized (0 if not checked, 1 if checked)
                $content['settings']['hidden_desktop'] = !empty($content['settings']['hidden_desktop']) && $content['settings']['hidden_desktop'] != '0' ? '1' : '0';
                $content['settings']['hidden_tablet'] = !empty($content['settings']['hidden_tablet']) && $content['settings']['hidden_tablet'] != '0' ? '1' : '0';
                $content['settings']['hidden_mobile'] = !empty($content['settings']['hidden_mobile']) && $content['settings']['hidden_mobile'] != '0' ? '1' : '0';
            }

            if ($request->has('_is_settings_form')) {
                // Updating settings only
                $existingContent['settings'] = array_merge($existingContent['settings'] ?? [], $content['settings'] ?? []);
                $data['content'] = json_encode($existingContent);
            } else {
                // Updating content and preserving settings if not supplied
                $merged = $existingContent;
                foreach ($content as $key => $val) {
                    if ($key === 'settings' && is_array($val) && isset($existingContent['settings']) && is_array($existingContent['settings'])) {
                        $merged['settings'] = array_merge($existingContent['settings'], $val);
                    } else {
                        $merged[$key] = $val;
                    }
                }
                $data['content'] = json_encode($merged);
            }
        } elseif (is_string($content) && !empty($content)) {
            $data['content'] = $content;
        }

        if ($request->hasFile('image')) {
            $media = $this->storeMedia($request->file('image'));
            $data['media_id'] = $media->id;
        }

        unset($data['image'], $data['_is_settings_form']);

        $section->update($data);
        $section->load('media');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'section' => $section,
                'message' => 'Section updated successfully.'
            ]);
        }

        return back()->with('success', 'Section updated successfully.');
    }

    public function reorderSections(Request $request, CmsPage $page): JsonResponse
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:cms_sections,id'
        ]);

        foreach ($data['order'] as $index => $id) {
            $page->sections()->where('id', $id)->update(['sort_order' => ($index + 1) * 10]);
        }

        return response()->json(['success' => true, 'message' => 'Sections reordered.']);
    }

    public function toggleSectionActive(CmsSection $section): JsonResponse
    {
        $section->is_active = !$section->is_active;
        $section->save();

        return response()->json([
            'success' => true,
            'is_active' => $section->is_active,
            'message' => $section->is_active ? 'Section visible on storefront.' : 'Section hidden on storefront.'
        ]);
    }

    public function duplicateSection(CmsSection $section)
    {
        $newSection = $section->replicate();
        $maxOrder = CmsSection::where('page_id', $section->page_id)->max('sort_order') ?? 0;
        $newSection->sort_order = $maxOrder + 10;
        $newSection->push();
        $newSection->load('media');

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'section' => $newSection,
                'message' => 'Section duplicated successfully.'
            ]);
        }

        return back()->with('success', 'Section duplicated successfully.');
    }

    public function destroySection(CmsSection $section)
    {
        $section->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Section deleted.']);
        }

        return back()->with('success', 'Section deleted successfully.');
    }

    public function mediaPicker(Request $request): JsonResponse
    {
        $query = Media::latest('id');

        if ($search = $request->query('search')) {
            $query->where('file_name', 'like', "%{$search}%");
        }

        $media = $query->paginate(24);

        $items = $media->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'file_name' => $item->file_name,
                'url' => $item->storage_path ? asset('storage/' . $item->storage_path) : ($item->url ?? ''),
                'file_size' => $item->file_size ? number_format($item->file_size / 1024, 1) . ' KB' : '',
            ];
        });

        return response()->json([
            'data' => $items,
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
            'total' => $media->total(),
        ]);
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|max:10240',
        ]);

        $media = $this->storeMedia($request->file('file'));

        return response()->json([
            'success' => true,
            'media' => [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'url' => $media->storage_path ? asset('storage/' . $media->storage_path) : ($media->url ?? ''),
            ],
            'message' => 'Image uploaded successfully.'
        ]);
    }

    private function storeMedia($file): Media
    {
        $path = $file->store('cms', 'public');
        $imgSize = @getimagesize($file->getRealPath());

        return Media::create([
            'uuid' => (string) Str::uuid(),
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $imgSize[0] ?? null,
            'height' => $imgSize[1] ?? null,
            'uploaded_by' => auth()->id(),
        ]);
    }
}
