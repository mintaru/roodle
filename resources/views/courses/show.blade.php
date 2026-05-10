<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Roodle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body>
@include('components.menu')


  <!-- MAIN -->
  <main class="main">
    <div class="courses-header">
      <div>
        <h1 class="section-title">{{ $course->title }}</h1>
      </div>
      @hasanyrole('teacher|admin')
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          <a href="{{ route('tests.create', $course) }}" class="btn btn-primary" style="font-size: 13px; padding: 6px 14px;">
            ➕ Тест
          </a>
          <a href="{{ route('lectures.create', $course) }}" class="btn btn-primary" style="font-size: 13px; padding: 6px 14px;">
            ➕ Лекция
          </a>
          <a href="{{ route('materials.create', $course) }}" class="btn btn-primary" style="font-size: 13px; padding: 6px 14px;">
            ➕ Материал
          </a>
          <a href="{{ route('assignments.create', $course) }}" class="btn btn-primary" style="font-size: 13px; padding: 6px 14px;">
            ➕ Задание
          </a>
          <a href="{{ route('courses.grades', $course) }}" class="btn btn-primary" style="font-size: 13px; padding: 6px 14px;">
            📊 Оценки
          </a>
        </div>
      @endhasanyrole
    </div>

    <livewire:course-manager :course="$course" />
  </main>
</div>
</body>
</html>
