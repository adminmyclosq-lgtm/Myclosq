<div>
    @php
        $html = is_string($section->content) ? (json_decode($section->content, true)['html'] ?? '') : ($section->content['html'] ?? '');
    @endphp
    
    {!! $html !!}
</div>
