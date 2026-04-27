<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Roodle</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
<link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

</head>
<body>

<!-- ====== HEADER ====== -->
<header class="header">
  <div class="header__inner">
    <a class="logo" href="#" onclick="showPage('dashboard'); return false;">
      <img src="{{ asset('images/roodle.png') }}" alt="Roodle" width="32" height="32">
      <span class="logo__name">Roodle</span>
    </a>

    <nav class="header__nav">
      <a class="nav-link" id="nav-catalog" href="#" onclick="showPage('catalog'); setActiveNav('nav-catalog'); return false;">Личный кабинет</a>
    </nav>

    <div class="header__actions">
      <div class="search-box">
        <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        Поиск курсов…
      </div>

      <div class="avatar">АС</div>
    </div>
  </div>
</header>

<!-- ====== LAYOUT ====== -->
<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <p class="sidebar-section-title">Главное</p>

    <a class="sidebar-link" href="#" onclick="showPage('catalog'); return false;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      Недавние курсы
    </a>
    <a class="sidebar-link" href="#" onclick="showPage('course'); return false;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
      Все курсы
    </a>



    <p class="sidebar-section-title">Обучение</p>

    <a class="sidebar-link" href="#">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
      Оценки
    </a>


    <a class="sidebar-link" href="#">
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



   

  </main>
</div>

<script>
function showPage(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById('page-' + id).classList.add('active');
  document.querySelectorAll('.sidebar-link').forEach(l => {
    l.classList.remove('active');
    if (l.getAttribute('onclick') && l.getAttribute('onclick').includes("'" + id + "'")) l.classList.add('active');
  });
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function setActiveNav(id) {
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

document.querySelectorAll('.tab').forEach(tab => {
  tab.addEventListener('click', function() {
    this.closest('.tabs').querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
  });
});

document.querySelectorAll('.filter-chip').forEach(chip => {
  chip.addEventListener('click', function() {
    this.closest('.catalog-filter').querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
  });
});
</script>
</body>
</html>