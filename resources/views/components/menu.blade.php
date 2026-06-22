<header class="header" x-data="{}">
    <div class="header__inner">
        <a class="logo" href="/" onclick="window.location.href='/'; return false;">
            <img src="{{ asset('images/roodle.png') }}" alt="Roodle" width="32" height="32">
            <span class="logo__name">oodle</span>
        </a>

        <nav class="header__nav">
            <a class="sidebar-link" href="{{ route('home') }}">
                Все курсы
            </a>
            @role('admin')
                <a class="nav-link" href="{{ route('admin.dashboard') }}" id="nav-admin"
                    style="color: #e74c3c;">Админ-панель</a>
            @endrole

        </nav>


        <div class="header__actions">


            <div onclick="window.location='/profile-edit';" class="avatar overflow-hidden" title="{{ auth()->user()->name ?? 'User' }}">
                @if (auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                         alt="{{ auth()->user()->name }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;" />
                @else
                    <img src="{{ asset('images/profile.png') }}"
                         alt="{{ auth()->user()->name }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;" />
                @endif
            </div>

            <div class="profile-menu" x-data="{ open: false, isDark: document.documentElement.classList.contains('dark') }">
                <button @click="open = !open" class="profile-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition style="display: none;"
                    class="profile-dropdown">
                    <a href="/profile-edit" class="profile-dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Профиль
                    </a>
                    <a class="profile-dropdown-item" href="#"
                        @click.prevent="
                            isDark = !isDark;
                            document.documentElement.classList.toggle('dark');
                            localStorage.setItem('dark-mode', isDark);
                        ">
                        <template x-if="!isDark">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </template>
                        <template x-if="isDark">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"/>
                                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                            </svg>
                        </template>
                        <span x-text="isDark ? 'Светлая тема' : 'Тёмная тема'"></span>
                    </a>
                    <hr style="margin: 8px 0; border: none; border-top: 1px solid var(--color-border, #e0e0e0);">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="profile-dropdown-item" style="color: #e74c3c;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Выход
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
