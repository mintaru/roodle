<header class="header" x-data="{}">
  <div class="header__inner">
    <a class="logo" href="/" onclick="window.location.href='/'; return false;">
      <img src="{{ asset('images/roodle.png') }}" alt="Roodle" width="32" height="32">
      <span class="logo__name">Roodle</span>
    </a>

    <nav class="header__nav">
      @role('admin')
        <a class="nav-link" href="{{ route('admin.dashboard') }}" id="nav-admin" style="color: #e74c3c;">Админ-панель</a>
      @endrole
    </nav>

    <div class="header__actions">


      <div class="avatar" title="{{ auth()->user()->name ?? 'User' }}">
        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}{{ substr(auth()->user()->name ?? 'U', strpos(auth()->user()->name ?? 'U', ' ') + 1, 1) ?? '' }}
      </div>

      <div class="profile-menu" x-data="{ open: false }">
        <button @click="open = !open" class="profile-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="1"></circle>
            <circle cx="19" cy="12" r="1"></circle>
            <circle cx="5" cy="12" r="1"></circle>
          </svg>
        </button>
        <div x-show="open" @click.away="open = false" x-transition style="display: none;" class="profile-dropdown">
          <a href="/profile-edit" class="profile-dropdown-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Профиль
          </a>
          <a href="#" class="profile-dropdown-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            Настройки
          </a>
          <hr style="margin: 8px 0; border: none; border-top: 1px solid #e0e0e0;">
          <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="profile-dropdown-item" style="color: #e74c3c;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Выход
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</header>
