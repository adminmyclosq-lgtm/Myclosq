@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="badge">CMS</span>
            <h1 class="serif mt-1 text-3xl md:text-4xl font-bold text-stone-900">Content Management & Pages</h1>
            <p class="text-sm text-stone-500 mt-1">Design, construct, and manage marketing landing pages, policy documents, and content sections.</p>
        </div>
        <div>
            <button type="button" onclick="openCreatePageModal()" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Page
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-stone-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-600">
                <thead class="bg-stone-50/75 text-xs font-semibold uppercase tracking-wider text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="px-6 py-4">Page Title & Type</th>
                        <th class="px-6 py-4">URL Route</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Sections</th>
                        <th class="px-6 py-4">Last Updated</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pages as $page)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-stone-900">{{ $page->title }}</div>
                                <div class="text-xs text-stone-400 capitalize mt-0.5">{{ $page->page_type ?? 'Standard Page' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ url($page->slug == 'home' ? '/' : $page->slug) }}" target="_blank"
                                   class="text-xs font-mono text-stone-500 hover:text-stone-900 underline decoration-stone-300 underline-offset-4 flex items-center gap-1.5">
                                    <span>/{{ $page->slug == 'home' ? '' : $page->slug }}</span>
                                    <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = [
                                        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'archived' => 'bg-stone-100 text-stone-600 border-stone-200',
                                    ][$page->status] ?? 'bg-stone-50 text-stone-600 border-stone-200';
                                @endphp
                                <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border {{ $statusClass }}">
                                    {{ $page->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-stone-700">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-stone-100 text-stone-700">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    {{ $page->sections->count() }} {{ Str::plural('section', $page->sections->count()) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-stone-500">
                                {{ $page->updated_at ? $page->updated_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.cms.sections', $page) }}"
                                       class="inline-flex items-center gap-1 rounded-xl bg-stone-900 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-stone-800 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Builder
                                    </a>

                                    @if($page->slug !== 'home')
                                        <form method="POST" action="{{ route('admin.cms.destroy', $page->id) }}" onsubmit="return confirm('Are you sure you want to delete this page and all its sections?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-stone-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Delete Page">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-stone-500">
                                <p class="font-medium text-stone-700">No CMS pages found</p>
                                <p class="text-xs text-stone-400 mt-1">Click "Create New Page" above to add your first landing page or legal policy.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">{{ $pages->links() }}</div>
</div>

<!-- Create Page Modal -->
<div id="createPageModal" class="fixed inset-0 z-50 hidden bg-stone-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative border border-stone-200">
        <button onclick="closeCreatePageModal()" class="absolute top-4 right-4 text-stone-400 hover:text-stone-600 text-xl font-bold">&times;</button>
        <h3 class="text-lg font-bold text-stone-900 mb-1">Create New CMS Page</h3>
        <p class="text-xs text-stone-500 mb-4">Add a new routable landing page or policy document to Gut Reset.</p>

        <form method="POST" action="{{ route('admin.cms.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Page Title</label>
                <input type="text" id="new_title" name="title" required placeholder="e.g. Science & Gut Health"
                       oninput="autoGenerateSlug(this.value)"
                       class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">URL Slug</label>
                    <input type="text" id="new_slug" name="slug" required placeholder="science-gut-health"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm font-mono text-xs focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Page Type</label>
                    <select name="page_type" class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm bg-white focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                        <option value="landing">Landing Page</option>
                        <option value="policy">Policy / Legal</option>
                        <option value="article">Editorial Article</option>
                        <option value="general">General Page</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Publication Status</label>
                <select name="status" class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm bg-white focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                    <option value="draft">Draft (Private)</option>
                    <option value="published">Published (Live immediately)</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">SEO Meta Title (Optional)</label>
                <input type="text" name="meta_title" placeholder="e.g. Clinical Research on Gut Microbiome | Guided Wellness"
                       class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">SEO Meta Description (Optional)</label>
                <textarea name="meta_description" rows="2" placeholder="Brief summary for Google search engine results..."
                          class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeCreatePageModal()" class="px-4 py-2.5 rounded-xl border border-stone-200 text-sm font-medium text-stone-600 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="btn-primary py-2.5">
                    Create Page
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreatePageModal() {
    document.getElementById('createPageModal').classList.remove('hidden');
}

function closeCreatePageModal() {
    document.getElementById('createPageModal').classList.add('hidden');
}

function autoGenerateSlug(value) {
    const slug = value.toLowerCase()
                      .trim()
                      .replace(/[^a-z0-9\s-]/g, '')
                      .replace(/[\s-]+/g, '-');
    document.getElementById('new_slug').value = slug;
}
</script>
@endsection

