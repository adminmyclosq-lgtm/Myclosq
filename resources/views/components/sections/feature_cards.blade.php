@props(['section' => null])
@php
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    $bgColor = $settings['bg_color'] ?? 'bg-[var(--cream)]';
    $items = isset($contentData['items']) && is_array($contentData['items']) ? $contentData['items'] : [];
@endphp
<section class="{{ $bgColor }}" style="padding-top: {{ $topSpace }}; padding-bottom: {{ $bottomSpace }};" id="section-{{ $section->id ?? 'new' }}">
    <div class="section">
        @if($contentData['eyebrow'] ?? '')
            <span class="badge">{{ $contentData['eyebrow'] }}</span>
        @else
            <span class="badge">Check your fit</span>
        @endif
        <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }}" @endif>{{ $section->title ?? 'Is this likely to be right for you?' }}</h2>
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            @if(count($items) > 0)
                @foreach($items as $item)
                    <div class="card">
                        <h3 class="font-bold">{{ $item['title'] ?? '' }}</h3>
                        @php $lines = explode("\n", $item['description'] ?? ''); @endphp
                        <ul class="mt-4 space-y-3 text-stone-600">
                            @foreach($lines as $line)
                                @if(trim($line)) <li>· {{ $line }}</li> @endif
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <h3 class="font-bold">May be relevant</h3>
                    <ul class="mt-4 space-y-3 text-stone-600">
                        <li>· Seeking structured gut-health support</li>
                        <li>· Struggle with supplement consistency</li>
                        <li>· Want a more informed read</li>
                        <li>· Value data-driven personal insight</li>
                    </ul>
                </div>
                <div class="card">
                    <h3 class="font-bold">Speak to a doctor first</h3>
                    <ul class="mt-4 space-y-3 text-stone-600">
                        <li>· Pregnancy or breast-feeding</li>
                        <li>· Diagnosed gastrointestinal condition</li>
                        <li>· Currently taking prescription medication</li>
                        <li>· Severe, sudden or new abdominal symptoms</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</section>
