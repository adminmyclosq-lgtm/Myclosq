@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="badge">WhatsApp Business</span>
            <h1 class="serif mt-1 text-3xl md:text-4xl font-bold text-stone-900">Message Templates</h1>
            <p class="text-sm text-stone-500 mt-1">Configure Meta-approved WhatsApp Cloud API templates for transactional notifications and marketing.</p>
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

    <div class="grid gap-6 lg:grid-cols-12 items-start">
        <!-- Add Template Card -->
        <div class="lg:col-span-5 card p-6">
            <h2 class="text-base font-semibold text-stone-900 mb-1">Add New Template</h2>
            <p class="text-xs text-stone-500 mb-4">Register a template definition matching your Meta WhatsApp Manager.</p>
            
            <form method="POST" action="{{ route('admin.whatsapp.templates.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Internal Display Name</label>
                    <input type="text" name="name" required placeholder="e.g. Order Delivered Alert"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Meta Template Name (snake_case)</label>
                    <input type="text" name="meta_template_name" required placeholder="e.g. order_delivered_v2"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm font-mono text-xs focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Language</label>
                        <input type="text" name="language_code" value="en" required
                               class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm bg-white focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                            <option value="UTILITY">UTILITY</option>
                            <option value="MARKETING">MARKETING</option>
                            <option value="AUTHENTICATION">AUTHENTICATION</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Body Preview</label>
                    <textarea name="body" rows="4" placeholder="Hello {{1}}, your order #{{2}} has been delivered..."
                              class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm bg-white focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                        <option value="approved">Approved</option>
                        <option value="draft">Draft</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary w-full justify-center py-2.5">
                    Save Template
                </button>
            </form>
        </div>

        <!-- Configured Templates List -->
        <div class="lg:col-span-7 card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-stone-900">Configured Templates ({{ $templates->total() }})</h2>
            </div>

            @if($templates->count() > 0)
                <div class="space-y-4 divide-y divide-stone-100">
                    @foreach($templates as $t)
                        <div class="pt-4 first:pt-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-semibold text-stone-900">{{ $t->name }}</h3>
                                        @php
                                            $statusColors = [
                                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200'
                                            ];
                                            $catColors = [
                                                'UTILITY' => 'bg-blue-50 text-blue-700',
                                                'MARKETING' => 'bg-purple-50 text-purple-700',
                                                'AUTHENTICATION' => 'bg-stone-100 text-stone-700'
                                            ];
                                        @endphp
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full border {{ $statusColors[$t->status] ?? 'bg-stone-50 text-stone-600' }}">
                                            {{ $t->status }}
                                        </span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded {{ $catColors[$t->category] ?? 'bg-stone-50' }}">
                                            {{ $t->category }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-stone-500 font-mono mt-1">
                                        {{ $t->meta_template_name }} &bull; {{ $t->language_code }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            onclick="openEditTemplateModal({{ json_encode($t) }})"
                                            class="text-xs text-stone-600 hover:text-stone-900 font-medium px-2.5 py-1.5 rounded-lg border border-stone-200 hover:bg-stone-50">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.whatsapp.templates.destroy', $t->id) }}" onsubmit="return confirm('Are you sure you want to delete this template?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-medium px-2 py-1.5 rounded-lg hover:bg-rose-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($t->body)
                                <div class="mt-2.5 bg-stone-50/80 rounded-xl p-3 text-xs text-stone-700 font-mono whitespace-pre-wrap border border-stone-100">
                                    {{ $t->body }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">{{ $templates->links() }}</div>
            @else
                <div class="py-12 text-center text-stone-500">
                    <p class="font-medium text-stone-700">No WhatsApp templates registered yet</p>
                    <p class="text-xs text-stone-400 mt-1">Fill in the form on the left to add your first template.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div id="editTemplateModal" class="fixed inset-0 z-50 hidden bg-stone-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative border border-stone-200">
        <button onclick="closeEditTemplateModal()" class="absolute top-4 right-4 text-stone-400 hover:text-stone-600 text-xl font-bold">&times;</button>
        <h3 class="text-lg font-bold text-stone-900 mb-1">Edit WhatsApp Template</h3>
        <p class="text-xs text-stone-500 mb-4">Update template synchronization parameters.</p>

        <form id="editTemplateForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Internal Display Name</label>
                <input type="text" id="edit_name" name="name" required
                       class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Meta Template Name</label>
                <input type="text" id="edit_meta_template_name" name="meta_template_name" required
                       class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm font-mono text-xs focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Language</label>
                    <input type="text" id="edit_language_code" name="language_code" required
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Category</label>
                    <select id="edit_category" name="category" class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm bg-white focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                        <option value="UTILITY">UTILITY</option>
                        <option value="MARKETING">MARKETING</option>
                        <option value="AUTHENTICATION">AUTHENTICATION</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Body Preview</label>
                <textarea id="edit_body" name="body" rows="4"
                          class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider mb-1.5">Status</label>
                <select id="edit_status" name="status" class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm bg-white focus:border-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900">
                    <option value="approved">Approved</option>
                    <option value="draft">Draft</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditTemplateModal()" class="px-4 py-2.5 rounded-xl border border-stone-200 text-sm font-medium text-stone-600 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="btn-primary py-2.5">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditTemplateModal(template) {
    const form = document.getElementById('editTemplateForm');
    form.action = '{{ url("admin/whatsapp/templates") }}/' + template.id;
    document.getElementById('edit_name').value = template.name || '';
    document.getElementById('edit_meta_template_name').value = template.meta_template_name || '';
    document.getElementById('edit_language_code').value = template.language_code || 'en';
    document.getElementById('edit_category').value = template.category || 'UTILITY';
    document.getElementById('edit_body').value = template.body || '';
    document.getElementById('edit_status').value = template.status || 'draft';
    document.getElementById('editTemplateModal').classList.remove('hidden');
}

function closeEditTemplateModal() {
    document.getElementById('editTemplateModal').classList.add('hidden');
}
</script>
@endsection

