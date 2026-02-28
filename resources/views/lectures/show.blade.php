@include('components.menu')
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $lecture->course)" text="К курсу" />
    </div>
    <h1>{{ $lecture->title }}</h1>
    <link rel="stylesheet" href="{{ asset('css/lecture-show.css') }}">
    <p>Курс: {{ $lecture->course->title }}</p>
    @if($lecture->pdf_path)
        <p><a href="{{ Storage::url($lecture->pdf_path) }}" target="_blank">Скачать PDF</a></p>
    @endif
    <div class="lecture-content {{ ($lecture->content_type ?? 'text') === 'html' ? 'trix-content' : 'lecture-content-text' }}">
        @if(($lecture->content_type ?? 'text') === 'html')
            {!! $lecture->content !!}
        @else
            {!! nl2br(e($lecture->content ?? '')) !!}
        @endif
    </div>

