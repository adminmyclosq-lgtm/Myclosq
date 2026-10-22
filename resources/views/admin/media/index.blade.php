@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="badge">CMS</span>
            <h1 class="serif mt-1 text-3xl md:text-4xl font-bold text-stone-900">Media Library</h1>
            <p class="text-sm text-stone-500 mt-1">Upload and manage visual assets, product imagery, and CMS attachments.</p>
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

    <!-- Upload Card -->
    <div class="card p-6">
        <h2 class="text-base font-semibold text-stone-900 mb-4">Upload New Asset</h2>
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="grid sm:grid-cols-12 gap-4 items-end">
            @csrf
            <div class="sm:col-span-5">
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-2">Choose File (Max 5MB)</label>
                <input type="file" name="file" required accept="image/jpeg,image/png,image/webp,image/svg+xml"
                       class="block w-full text-sm text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer">
            </div>
            <div class="sm:col-span-5">
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-2">Alt Text (Accessibility & SEO)</label>
                <input type="text" name="alt_text" placeholder="e.g. Gut Reset Morning Elixir bottle"
                       class="w-full rounded-xl border border-stone-200 px-3.5 py-2 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full btn-primary justify-center py-2.5">
                    Upload Asset
                </button>
            </div>
        </form>
    </div>

    <!-- Media Grid -->
    @if($media->count() > 0)
        <div class="grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach($media as $m)
                <div class="card group overflow-hidden flex flex-col justify-between border border-stone-200/70 hover:shadow-md transition">
                    <div class="relative aspect-square w-full bg-stone-100 flex items-center justify-center overflow-hidden">
                        <img src="{{ $m->url }}" alt="{{ $m->alt_text ?? $m->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @if($m->width && $m->height)
                            <span class="absolute bottom-2 left-2 bg-stone-900/75 backdrop-blur text-white text-[10px] font-mono px-1.5 py-0.5 rounded">
                                {{ $m->width }}&times;{{ $m->height }}
                            </span>
                        @endif
                    </div>
                    <div class="p-3.5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="text-xs font-semibold text-stone-900 truncate" title="{{ $m->file_name }}">{{ $m->file_name }}</div>
                            <div class="text-[11px] text-stone-500 mt-0.5 flex items-center justify-between">
                                <span>{{ strtoupper(explode('/', $m->mime_type)[1] ?? 'IMG') }}</span>
                                <span>{{ $m->file_size ? number_format($m->file_size / 1024, 1) . ' KB' : '-' }}</span>
                            </div>
                            @if($m->alt_text)
                                <div class="text-[11px] text-stone-400 truncate mt-1 italic" title="{{ $m->alt_text }}">"{{ $m->alt_text }}"</div>
                            @endif
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between gap-2">
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $m->url }}'); alert('Image URL copied to clipboard!');"
                                    class="text-xs text-stone-600 hover:text-stone-900 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Copy URL
                            </button>
                            <form method="POST" action="{{ route('admin.media.destroy', $m->id) }}" onsubmit="return confirm('Are you sure you want to permanently delete this media file?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $media->links() }}</div>
    @else
        <div class="card p-12 text-center text-stone-500">
            <svg class="w-12 h-12 mx-auto text-stone-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="font-medium text-stone-700">No media assets found</p>
            <p class="text-xs text-stone-400 mt-1">Upload an image above to populate the media library.</p>
        </div>
    @endif
</div>
@endsection

