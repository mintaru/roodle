<!-- MAIN -->
<main class="main">
    <div class="courses-header">
        <div>
            <h1 class="section-title">Наши курсы</h1>
        </div>
        @if (auth()->user()->hasRole('teacher') || auth()->user()->hasRole('admin'))
            <a href="{{ route('courses.create') }}" class="btn btn-primary">
                Создать курс
            </a>
        @endif
    </div>

    <div class="courses-search">
        <h2 class="section-subtitle">Поиск</h2>
        <div class="search-input-wrapper">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
            <input type="text" wire:model.live.debounce-300ms="search"
                placeholder="Введите название курса или имя автора..." class="search-input">
        </div>
    </div>

    <div class="courses-grid" x-data="{ openMenuId: null }">
        @forelse($courses as $course)
            @php
                $canEdit =
                    auth()->user()->hasRole('admin') ||
                    $course->user_id === auth()->id() ||
                    $course
                        ->teacherPermissions()
                        ->where('user_id', auth()->id())
                        ->where('can_edit', true)
                        ->exists();

                $canDelete =
                    auth()->user()->hasRole('admin') ||
                    $course->user_id === auth()->id() ||
                    $course
                        ->teacherPermissions()
                        ->where('user_id', auth()->id())
                        ->where('can_delete', true)
                        ->exists();
            @endphp

            <div class="course-card" @click="window.location.href = '{{ route('courses.show', $course) }}';"
                @mouseleave="openMenuId = null" style="cursor: pointer;">
                @if (auth()->user()->hasRole('admin') || $course->user_id === auth()->id() || $canEdit || $canDelete)
                    <button @click.stop="openMenuId = openMenuId === {{ $course->id }} ? null : {{ $course->id }}"
                        class="course-card-menu-btn" title="Меню"
                        :class="{ 'course-card-menu-btn--visible': openMenuId === {{ $course->id }} }">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="5" r="2" />
                            <circle cx="12" cy="12" r="2" />
                            <circle cx="12" cy="19" r="2" />
                        </svg>
                    </button>

                    <div x-show="openMenuId === {{ $course->id }}" @click.away="openMenuId = null" x-transition
                        class="course-card-menu" style="display:none">
                        @if (auth()->user()->hasRole('admin') || $course->user_id === auth()->id())
                            <form action="{{ route('courses.archive', $course) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="color:rgb(171, 135, 67)" class="btn course-card-menu-item">
                                    Архивировать
                                </button>
                            </form>
                        @endif

                        @if ($canEdit)
                            <a href="{{ route('courses.edit', $course) }}" @click.stop class="btn course-card-menu-item"
                                title="Редактировать"
                                style="color:#667eea">
                                Редактировать
                            </a>
                        @endif

                        @if ($canDelete)
                            <form action="{{ route('courses.destroy', $course) }}" method="POST"
                                style="display: inline;" @click.stop>
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color:red" class="btn course-card-menu-item" title="Удалить"
                                    onclick="return confirm('Вы уверены, что хотите удалить этот курс?')">
                                    Удалить
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if ($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}"
                        class="course-card__image">
                @else
                    <div class="course-card__image course-card__image--placeholder"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                @endif

                <div class="course-card__body">

                    <h2 class="course-card__title">{{ $course->title }}</h2>
                    @if ($course->author)
                        <p class="course-card__teacher"
                            style="font-size:0.9rem;color:var(--muted-color);margin-top:6px;">
                            {{ $course->author->name }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="courses-empty">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
                <p>Курсов пока нет</p>
                @if (auth()->user()->hasRole('teacher'))
                    <a href="{{ route('courses.create') }}" class="btn btn-primary" style="margin-top: 16px;">
                        Создать первый курс
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</main>
</div>
