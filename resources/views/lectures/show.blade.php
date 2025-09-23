    <h1>{{ $lecture->title }}</h1>
    <link rel="stylesheet" href="{{ asset('css/lecture-show.css') }}">
    <p>Курс: {{ $lecture->course->title }}</p>
    <p><a href="{{ Storage::url($lecture->pdf_path) }}" target="_blank">Скачать PDF</a></p>
    <div style="white-space: pre-line;">{{ $lecture->content }}</div>

