<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }}</title>
</head>
<body>
<h1>{{ $course->title }}</h1>
<p>{{ $course->description }}</p>

{{-- Кнопка создать тест --}}
<a href="{{ route('tests.create', $course) }}" class="btn btn-primary">
    Создать тест для курса
</a>

{{-- Кнопка создать лекцию --}}
<a href="{{ route('lectures.create', $course) }}" class="btn btn-success">
    Создать лекцию для курса
</a>

<hr>

<h2>Тесты курса</h2>
<ul>
    @forelse($course->tests as $test)
        <li>
            <a href="{{ route('tests.show', $test) }}">{{ $test->title }}</a>
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

</body>
</html>
