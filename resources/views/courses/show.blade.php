<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/courses-show.css') }}">
    {{-- Если вам нужны другие CSS/JS, они должны быть здесь --}}
</head>
<body>
{{-- Меню: ПРАВИЛЬНОЕ МЕСТО (сразу после открытия <body>) --}}
@include('components.menu')

<div class="container">
    <h1>{{ $course->title }}</h1>
    <p>{{ $course->description }}</p>

    {{-- Кнопки --}}
    <a href="{{ route('tests.create', $course) }}" class="btn btn-primary">
        Создать тест для курса
    </a>
    <a href="{{ route('lectures.create', $course) }}" class="btn btn-success">
        Создать лекцию для курса
    </a>

    <hr>

    <h2>Тесты курса</h2>
    <ul>
        @forelse($course->tests as $test)
            <li>
                <a href="{{ route('tests.view', $test) }}">{{ $test->title }}</a><br>
                <a href="{{ route('tests.show', $test) }}">Редактировать тест</a><br>
                <a>
                    @php
                        $userAttempts = $test->attempts()->where('user_id', auth()->id())->count();
                        $remaining = $test->max_attempts == 0
                            ? '∞'
                            : max(0, $test->max_attempts - $userAttempts);
                    @endphp
                
                    попытки:{{ $remaining }}
                </a>
                <a href="{{ route('tests.attempt', $test) }}">пройти тест</a>
            </li>
        @empty
            <li>Тестов пока нет</li>
        @endforelse
    </ul>

    <h2>Лекции курса</h2>
    <ul>
        @forelse($course->lectures as $lecture)
            <li>
                <a href="{{ route('lectures.show', ['course' => $course, 'lecture' => $lecture]) }}">
                    {{ $lecture->title }}
                </a>
            </li>
        @empty
            <li>Лекций пока нет</li>
        @endforelse
    </ul>
</div>
</body>

</html>
