@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10"><span class="badge">CMS</span><h1 class="serif mt-3 text-4xl">Media library</h1>
@if(session('success'))<div class="mt-5 rounded-xl bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
<div class="card mt-8"><form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="flex flex-wrap gap-4 items-end">@csrf<div><label class="text-sm">Image</label><input type="file" name="file" required class="mt-2"></div><div><label class="text-sm">Alt text</label><input name="alt_text" class="mt-2 rounded-xl border p-3"></div><button class="btn-primary">Upload</button></form></div>
<div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach($media as $m)<div class="card p-3"><img src="{{ $m->url }}" class="aspect-square w-full rounded-2xl object-cover"><div class="mt-3 text-sm font-semibold">{{ $m->file_name }}</div><div class="text-xs text-stone-500">{{ $m->mime_type }}</div></div>@endforeach</div><div class="mt-6">{{ $media->links() }}</div></div>
@endsection
