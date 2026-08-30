@extends('layouts.app', ['title' => 'How It Works - Guided Wellness'])

@section('content')
@if(isset($page) && $page->sections && $page->sections->count() > 0)
    <!-- DYNAMIC CMS SECTION RENDERER -->
    @foreach($page->sections->sortBy('sort_order') as $section)
        @php
            $settings = is_string($section->content) ? (json_decode($section->content, true)['settings'] ?? []) : [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
        @endphp
        
        @if($section->section_type === 'custom_html')
            <x-sections.custom_html :section="$section" />
        @elseif($section->section_type === 'hero')
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
        @endif
    @endforeach
@endif
@endsection
