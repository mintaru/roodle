<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }}</title>
</head>
<body>
<h1>{{ $course->title }}</h1>
<p>{{ $course->description }}</p>

<a href="{{ route('tests.create', $course) }}" class="btn btn-primary">
    Создать тест для курса
</a>

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
</body>
</html>
