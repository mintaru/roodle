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

<div class="layout" x-data="{}">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <p class="sidebar-section-title">Главное</p>

    <a class="sidebar-link" href="#" onclick="window.location.href='/'; return false;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      Недавние курсы
    </a>
    <a class="sidebar-link" href="{{ route('home') }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
      Все курсы
    </a>

    <a class="sidebar-link" href="#">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
      Оценки
    </a>

    <a class="sidebar-link" href="/profile-edit">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Профиль
    </a>
    <a class="sidebar-link" href="#">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
      Настройки
    </a>
  </aside>

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
