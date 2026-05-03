<!--
  Компонент Sidebar - переиспользуемый сайдбар для всех страниц

  Использование:
  <x-sidebar :activeLink="'home'" />

  Props:
  - activeLink: текущий активный пункт меню (home, all-courses, grades, profile, settings)
-->

<aside class="sidebar">
  <p class="sidebar-section-title">Главное</p>

  <a class="sidebar-link {{ $activeLink === 'recent' ? 'active' : '' }}"
     href="#" onclick="window.location.href='/'; return false;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
      <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
    </svg>
    Недавние курсы
  </a>

  <a class="sidebar-link {{ $activeLink === 'all-courses' ? 'active' : '' }}"
     href="{{ route('home') }}">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polygon points="23 7 16 12 23 17 23 7"/>
      <rect x="1" y="5" width="15" height="14" rx="2"/>
    </svg>
    Все курсы
  </a>

  <a class="sidebar-link {{ $activeLink === 'grades' ? 'active' : '' }}" href="#">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M12 20h9"/>
      <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
    </svg>
    Оценки
  </a>

  <a class="sidebar-link {{ $activeLink === 'profile' ? 'active' : '' }}"
     href="/profile-edit">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
      <circle cx="12" cy="7" r="4"/>
    </svg>
    Профиль
  </a>

  <a class="sidebar-link {{ $activeLink === 'settings' ? 'active' : '' }}" href="#">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="3"/>
      <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
    </svg>
    Настройки
  </a>
</aside>
