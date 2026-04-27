<div class="layout" x-data="{}">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <p class="sidebar-section-title">Главное</p>

    <a class="sidebar-link" href="#" onclick="window.location.href='/'; return false;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      Недавние курсы
    </a>
    <a class="sidebar-link active" href="{{ route('home') }}">
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
        <h1 class="section-title">Наши курсы</h1>
      </div>
      @if(auth()->user()->hasRole('teacher'))
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
          ➕ Создать курс
        </a>
      @endif
    </div>

    <div class="courses-search">
      <h2 class="section-subtitle">Поиск</h2>
      <div class="search-input-wrapper">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" wire:model.live.debounce-300ms="search" placeholder="Введите название курса..." class="search-input">
      </div>
    </div>

    <div class="courses-grid">
      @forelse($courses as $course)
        @php
          $canEdit = auth()->user()->hasRole('admin') || 
                     $course->user_id === auth()->id() || 
                     $course->teacherPermissions()
                         ->where('user_id', auth()->id())
                         ->where('can_edit', true)
                         ->exists();
          
          $canDelete = auth()->user()->hasRole('admin') || 
                      $course->user_id === auth()->id() || 
                      $course->teacherPermissions()
                          ->where('user_id', auth()->id())
                          ->where('can_delete', true)
                          ->exists();
        @endphp

        <div class="course-card" x-data="{ open: false }">
          @if(auth()->user()->hasRole('admin') || $course->user_id === auth()->id())
            <button @click="open = !open" class="course-card-menu-btn" title="Меню">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="5" r="2" />
                <circle cx="12" cy="12" r="2" />
                <circle cx="12" cy="19" r="2" />
              </svg>
            </button>

            <div x-show="open" @click.away="open = false" x-transition class="course-card-menu">
              <form action="{{ route('courses.archive', $course) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="course-card-menu-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                  Архивировать
                </button>
              </form>
            </div>
          @endif

          @if ($course->image_path)
            <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="course-card__image">
          @else
            <div class="course-card__image course-card__image--placeholder" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
          @endif

          <div class="course-card__body">
            <h2 class="course-card__title">{{ $course->title }}</h2>
            


            <div class="course-card__actions">
              <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-block">
                Перейти в курс
              </a>
              
              @if($canEdit)
                <a href="{{ route('courses.edit', $course) }}" class="btn btn-secondary" title="Редактировать">
                  ✏️
                </a>
              @endif
              
              @if($canDelete)
                <form action="{{ route('courses.destroy', $course) }}" method="POST" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger" title="Удалить" onclick="return confirm('Вы уверены, что хотите удалить этот курс?')">
                    🗑️
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="courses-empty">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
          <p>Курсов пока нет</p>
          @if(auth()->user()->hasRole('teacher'))
            <a href="{{ route('courses.create') }}" class="btn btn-primary" style="margin-top: 16px;">
              Создать первый курс
            </a>
          @endif
        </div>
      @endforelse
    </div>
  </main>
</div>
