@props(['page' => null, 'isPreview' => false, 'products' => null])

@if(isset($page) && $page->sections && $page->sections->count() > 0)
    @foreach($page->sections->sortBy('sort_order') as $section)
        @php
            // Skip inactive sections on live storefront, but show on preview with a badge
            if (!$isPreview && isset($section->is_active) && !$section->is_active) {
                continue;
            }

            $contentData = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
            if (!is_array($contentData)) $contentData = [];
            $settings = $contentData['settings'] ?? [];

            // If hidden on all viewports on live, skip
            $isAllHidden = (!empty($settings['hidden_desktop']) && $settings['hidden_desktop'] != '0')
                && (!empty($settings['hidden_tablet']) && $settings['hidden_tablet'] != '0')
                && (!empty($settings['hidden_mobile']) && $settings['hidden_mobile'] != '0');

            if (!$isPreview && $isAllHidden) {
                continue;
            }
        @endphp

        <div class="relative group/cms-section">
            @if($isPreview && isset($section->is_active) && !$section->is_active)
                <div class="absolute top-2 right-4 z-40 bg-stone-900/90 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full shadow-md backdrop-blur">
                    Draft / Inactive Section
                </div>
            @endif

            @if($section->section_type === 'hero')
                <x-sections.hero :section="$section" />
            @elseif($section->section_type === 'problem')
                <x-sections.problem :section="$section" />
            @elseif($section->section_type === 'features')
                <x-sections.features :section="$section" />
            @elseif($section->section_type === 'standards')
                <x-sections.standards :section="$section" />
            @elseif($section->section_type === 'showcase')
                <x-sections.showcase :section="$section" />
            @elseif($section->section_type === 'testimonials')
                <x-sections.testimonials :section="$section" />
            @elseif($section->section_type === 'feature_cards')
                <x-sections.feature_cards :section="$section" />
            @elseif($section->section_type === 'learn')
                <x-sections.learn :section="$section" />
            @elseif($section->section_type === 'faq')
                <x-sections.faq :section="$section" />
            @elseif($section->section_type === 'cta')
                <x-sections.cta :section="$section" />
            @elseif($section->section_type === 'video')
                <x-sections.video :section="$section" />
            @elseif($section->section_type === 'gallery')
                <x-sections.gallery :section="$section" />
            @elseif($section->section_type === 'statistics')
                <x-sections.statistics :section="$section" />
            @elseif($section->section_type === 'spacer')
                <x-sections.spacer :section="$section" />
            @elseif($section->section_type === 'custom_html')
                <x-sections.custom_html :section="$section" />
            @else
                {{-- Fallback for any custom or legacy section --}}
                <x-sections.custom_html :section="$section" />
            @endif
        </div>
    @endforeach
@else
    <div class="section py-24 text-center">
        <h2 class="display-serif text-3xl text-stone-700">No sections added yet.</h2>
        <p class="text-sm text-stone-500 mt-2">Add sections from the CMS Section Builder to display content here.</p>
    </div>
@endif
