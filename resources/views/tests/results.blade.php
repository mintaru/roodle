@extends('layout')

@section('head')
<style>
    body {
        background: var(--color-bg);
    }

    .form-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .form-section {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--color-border);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section__title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: var(--color-text-muted);
        margin-bottom: 1.25rem;
    }

    .form-section__title-icon {
        width: 28px;
        height: 28px;
        border-radius: var(--r-sm);
        background: var(--teal-50);
        color: var(--teal-600);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .admin-stats {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .admin-stats__stat {
        display: flex;
        flex-direction: column;
    }

    .admin-stats__stat-value {
        font-family: var(--font-display);
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1.2;
        color: var(--gray-800);
    }

    .admin-stats__stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--color-text-muted);
    }

    .admin-stats__divider {
        width: 1px;
        height: 40px;
        background: var(--color-border);
        flex-shrink: 0;
    }

    .q-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--r-xl);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        overflow: hidden;
        margin-bottom: 1rem;
        transition: box-shadow var(--transition);
    }

    .q-card:hover {
        box-shadow: var(--shadow-md);
    }

    .q-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--color-border);
        background: var(--gray-50);
    }

    .q-card__header-left {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .q-badge {
        width: 32px;
        height: 32px;
        border-radius: var(--r-md);
        background: var(--teal-50);
        color: var(--teal-600);
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .q-card__meta {
        flex: 1;
        min-width: 0;
    }

    .q-card__text {
        font-size: 15px;
        font-weight: 600;
        color: var(--gray-800);
        line-height: 1.5;
    }

    .q-card__body {
        padding: 1.25rem 1.5rem;
    }

    .student-main-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-main-row .expand-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
        margin: 0;
        color: var(--color-text-muted);
        transition: color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: var(--r-sm);
    }

    .student-main-row .expand-btn:hover {
        color: var(--teal-600);
        background: var(--teal-50);
    }

    .student-main-row .student-info {
        display: flex;
        flex-direction: column;
    }

    .student-main-row .student-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--gray-800);
    }

    .student-main-row .student-username {
        font-size: 12px;
        color: var(--color-text-muted);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: var(--r-full);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .status-badge.completed {
        background: var(--green-50);
        color: var(--green-600);
        border: 1.5px solid var(--green-100);
    }

    .status-badge.progress {
        background: #fff8e1;
        color: #e65100;
        border: 1.5px solid #ffecb3;
    }

    .status-badge.not-started {
        background: var(--gray-100);
        color: var(--gray-500);
        border: 1.5px solid var(--gray-200);
    }

    .attempt-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: var(--r-full);
        font-size: 12px;
        font-weight: 600;
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .score {
        font-weight: 700;
        font-size: 15px;
    }

    .score.high {
        color: var(--green-600);
    }

    .score.medium {
        color: #e65100;
    }

    .score.low {
        color: #c62828;
    }

    .dash {
        color: var(--color-text-muted);
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: var(--r-full);
        font-size: 12px;
        font-weight: 600;
        font-family: var(--font-body);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn-primary {
        background: var(--teal-500);
        color: white;
    }

    .action-btn-primary:hover {
        background: var(--teal-600);
        transform: translateY(-1px);
    }

    .action-btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .action-btn-secondary:hover {
        background: var(--gray-300);
    }

    .action-btn-outline {
        background: transparent;
        color: var(--teal-600);
        border: 1.5px solid var(--teal-300);
    }

    .action-btn-outline:hover {
        background: var(--teal-50);
        border-color: var(--teal-400);
    }

    .action-btn-blue {
        background: #2b6cb0;
        color: white;
    }

    .action-btn-blue:hover {
        background: #1a56a0;
        transform: translateY(-1px);
    }

    .attempts-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .attempt-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border: 1px solid var(--color-border);
        border-radius: var(--r-lg);
        background: var(--color-surface);
        transition: border-color 0.2s;
    }

    .attempt-item:hover {
        border-color: var(--teal-300);
    }

    .attempt-item__info {
        min-width: 0;
    }

    .attempt-item__title {
        font-weight: 600;
        font-size: 13px;
        color: var(--gray-800);
    }

    .attempt-item__date {
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: 2px;
    }

    .attempt-item__actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-shrink: 0;
    }

    .attempt-item__score {
        font-weight: 700;
        font-size: 14px;
        color: var(--gray-800);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--color-text-muted);
        font-size: 14px;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: var(--color-surface);
        border-radius: var(--r-xl);
        padding: 2rem;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 1rem;
    }

    .modal-input {
        width: 100%;
        padding: 10px;
        border: 1.5px solid var(--color-border);
        border-radius: var(--r-md);
        font-size: 14px;
        margin-bottom: 1.5rem;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .modal-input:focus {
        outline: none;
        border-color: var(--teal-400);
    }

    .modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .modal-btn {
        padding: 8px 18px;
        border-radius: var(--r-full);
        font-size: 13px;
        font-weight: 600;
        font-family: var(--font-body);
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-btn-primary {
        background: var(--teal-500);
        color: white;
        border: none;
    }

    .modal-btn-primary:hover {
        background: var(--teal-600);
    }

    .modal-btn-secondary {
        background: transparent;
        color: var(--gray-600);
        border: 1.5px solid var(--color-border);
    }

    .modal-btn-secondary:hover {
        background: var(--gray-50);
    }

    .micro-badge {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: var(--r-full);
        font-size: 11px;
        font-weight: 700;
    }

    .micro-badge.attempt {
        background: var(--gray-100);
        color: var(--gray-600);
    }

    .current-q-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: var(--r-full);
        font-size: 12px;
        font-weight: 600;
        background: var(--sky-50);
        color: var(--sky-700);
    }

    .group-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }

    .group-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid var(--teal-400);
        background: var(--teal-50);
        font-size: 12px;
        color: var(--teal-700);
    }

    .filter-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: var(--r-full);
        font-size: 12px;
        font-weight: 600;
        font-family: var(--font-body);
        border: 1.5px solid var(--color-border);
        background: var(--color-surface);
        color: var(--color-text-secondary);
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }

    .filter-pill:hover {
        border-color: var(--teal-300);
        color: var(--teal-700);
        background: var(--teal-50);
    }

    .filter-pill.active {
        border-color: var(--teal-400);
        background: var(--teal-50);
        color: var(--teal-700);
    }

    .filter-pill .pill-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .filter-pill .pill-dot.dot-all { background: var(--gray-400); }
    .filter-pill .pill-dot.dot-completed { background: var(--green-500); }
    .filter-pill .pill-dot.dot-progress { background: #e65100; }
    .filter-pill .pill-dot.dot-has { background: var(--teal-500); }
    .filter-pill .pill-dot.dot-none { background: var(--gray-300); }

    @media (max-width: 640px) {
        .form-section {
            padding: 1.25rem;
        }

        .admin-stats {
            gap: 1rem;
        }

        .admin-stats__stat-value {
            font-size: 22px;
        }

        .q-card__header {
            flex-direction: column;
        }

        .status-badge {
            align-self: flex-start;
        }

        .attempt-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .attempt-item__actions {
            align-self: flex-end;
        }
    }
</style>
@endsection

@section('content')
<div class="layout" x-data="{
    showModal: false,
    selectedUserId: null,
    selectedUserName: '',
    extraAttempts: '',
    filter: 'all',
    selectedGroupIds: [],
    tempGroupIds: [],
    groupModalOpen: false,
    groupSearch: '',
    openGroupModal() {
        this.tempGroupIds = [...this.selectedGroupIds];
        this.groupSearch = '';
        this.groupModalOpen = true;
    },
    applyGroupFilter() {
        this.selectedGroupIds = [...this.tempGroupIds];
        this.groupModalOpen = false;
    },
    clearGroupFilter() {
        this.selectedGroupIds = [];
        this.groupModalOpen = false;
    },
}">
    <aside class="sidebar">
        <p class="sidebar-section-title">Навигация</p>
        @if($course)
            <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                {{ $course->title }}
            </a>
        @endif
        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Курс</p>
        <div style="padding: 0 0.75rem;">
            <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">
                {{ optional($course)->title ?? 'Без курса' }}
            </p>
        </div>
    </aside>

    <main class="main">
        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            @if(optional($course)->title)
                <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            @endif
            <span style="color: var(--gray-600); font-weight: 500;">{{ $test->title }}</span>
        </nav>

        <div class="page-header">
            <h1 class="page-header__title">{{ $test->title }}</h1>
        </div>

        @php
            $total = count($studentsData);
            $completed = collect($studentsData)->where('status','завершили')->count();
            $inProgress = collect($studentsData)->where('status','в процессе')->count();
        @endphp

        <div class="form-card" style="max-width: 900px;">

            {{-- Section: stats --}}
            <div class="form-section">
                <div class="admin-stats">
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value">{{ $total }}</div>
                        <div class="admin-stats__stat-label">Студентов</div>
                    </div>
                    <div class="admin-stats__divider"></div>
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value" style="color: var(--green-600);">{{ $completed }}</div>
                        <div class="admin-stats__stat-label">Завершили</div>
                    </div>
                    <div class="admin-stats__divider"></div>
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value" style="color: #e65100;">{{ $inProgress }}</div>
                        <div class="admin-stats__stat-label">В процессе</div>
                    </div>
                </div>
            </div>

            {{-- Section: students list --}}
            <div class="form-section">
                <div class="form-section__title">
                    <div class="form-section__title-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    Студенты
                </div>

                <div class="filter-pills">
                    <button type="button" class="filter-pill" :class="{ active: filter === 'all' }" @click="filter = 'all'">
                        <span class="pill-dot dot-all"></span>
                        Все
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'completed' }" @click="filter = 'completed'">
                        <span class="pill-dot dot-completed"></span>
                        Завершили
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'in_progress' }" @click="filter = 'in_progress'">
                        <span class="pill-dot dot-progress"></span>
                        В процессе
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'has_attempts' }" @click="filter = 'has_attempts'">
                        <span class="pill-dot dot-has"></span>
                        Есть попытки
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'no_attempts' }" @click="filter = 'no_attempts'">
                        <span class="pill-dot dot-none"></span>
                        Нет попыток
                    </button>
                    @can('edit courses')
                    <button type="button" class="filter-pill" :class="{ active: selectedGroupIds.length > 0 }" @click="openGroupModal()">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        <span x-text="selectedGroupIds.length > 0 ? selectedGroupIds.length + ' групп' : 'Группы'"></span>
                        <template x-if="selectedGroupIds.length > 0">
                            <span style="margin-left:2px;cursor:pointer;opacity:0.6;" @click.stop="selectedGroupIds = []">✕</span>
                        </template>
                    </button>
                    @endcan
                </div>

                @if($total > 0)
                    @foreach($studentsData as $data)
                        <div class="q-card" x-data="{ expanded: false, studentGroupIds: {{ Js::from($data['group_ids']) }} }" x-show="(filter === 'all' || (filter === 'completed' && '{{ $data['status'] }}' === 'завершили') || (filter === 'in_progress' && '{{ $data['status'] }}' === 'в процессе') || (filter === 'has_attempts' && {{ !$data['attempts']->isEmpty() ? 'true' : 'false' }}) || (filter === 'no_attempts' && {{ $data['attempts']->isEmpty() ? 'true' : 'false' }})) && (selectedGroupIds.length === 0 || selectedGroupIds.some(id => studentGroupIds.includes(id)))" x-cloak>
                            <div class="q-card__header">
                                <div class="q-card__header-left">
                                    <div class="student-name">{{ $data['user']->name }}</div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex-shrink: 0;">
                                    <span class="status-badge {{ strtolower(str_replace(' ', '-', $data['status'])) }}">
                                        {{ $data['status'] }}
                                    </span>

                                    <span class="micro-badge attempt">
                                        Попытка {{ $data['current_attempt_number'] ?? '—' }}
                                    </span>

                                    @if($data['status'] === 'в процессе')
                                        <span class="current-q-pill">
                                            {{ $data['current_question'] ?? 1 }}/{{ $data['totalQuestions'] }}
                                        </span>
                                    @endif

                                    @if($data['lastCompletedAttempt'])
                                        <span class="score {{ $data['lastCompletedAttempt']->score >= 75 ? 'high' : ($data['lastCompletedAttempt']->score >= 50 ? 'medium' : 'low') }}">
                                            {{ round($data['lastCompletedAttempt']->score) }}%
                                        </span>
                                    @else
                                        <span class="dash">—</span>
                                    @endif

                                    @can('edit courses')
                                        <button
                                            type="button"
                                            class="action-btn action-btn-primary"
                                            @click="showModal = true; selectedUserId = {{ $data['user']->id }}; selectedUserName = '{{ $data['user']->name }}'; extraAttempts = '';"
                                        >
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                            Попытка
                                        </button>
                                    @endcan

                                    <button type="button" class="action-btn action-btn-outline" @click="expanded = !expanded">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Попытки
                                    </button>
                                </div>
                            </div>

                            <div x-show="expanded" x-cloak>
                                <div class="q-card__body" style="background: var(--gray-50);">
                                    @if($data['attempts']->isEmpty())
                                        <div class="empty-state">У студента ещё нет попыток</div>
                                    @else
                                        <div class="attempts-list">
                                            @foreach($data['attempts'] as $attempt)
                                                <div class="attempt-item">
                                                    <div class="attempt-item__info">
                                                        <div class="attempt-item__title">
                                                            Попытка #{{ $attempt->attempt_number }}
                                                            @if($attempt->ended_at)
                                                                <span style="font-weight:400;color:var(--green-600);font-size:12px;margin-left:6px;">Завершена</span>
                                                            @else
                                                                <span style="font-weight:400;color:#e65100;font-size:12px;margin-left:6px;">В процессе</span>
                                                            @endif
                                                        </div>
                                                        <div class="attempt-item__date">
                                                            {{ $attempt->started_at ? $attempt->started_at->format('d.m.Y H:i') : '—' }}
                                                            @if($attempt->ended_at)
                                                                — {{ $attempt->ended_at->format('d.m.Y H:i') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="attempt-item__actions">
                                                        <span class="attempt-item__score">{{ $attempt->score !== null ? round($attempt->score) . '%' : '—' }}</span>
                                                        @if($attempt->ended_at)
                                                            <a href="{{ route('test-attempts.details', $attempt) }}" class="action-btn action-btn-blue">Открыть</a>
                                                        @else
                                                            <span class="micro-badge attempt">В процессе</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px;color:var(--color-text-muted);display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                        Нет студентов
                    </div>
                @endif
            </div>

        </div>
        @can('edit courses')
        <div
            x-show="showModal"
            x-cloak
            class="modal-overlay"
            @click.self="showModal = false"
        >
            <div class="modal-content">
                <div class="modal-title">Дополнительные попытки</div>
                <p style="font-size: 14px; color: var(--color-text-secondary); margin-bottom: 1rem;">
                    Студент: <strong x-text="selectedUserName"></strong>
                </p>

                <form
                    :action="`{{ route('test-attempts.grant-attempts', ['test' => $test->id, 'user' => '__USER_ID__']) }}`.replace('__USER_ID__', selectedUserId)"
                    method="POST"
                >
                    @csrf
                    <label style="font-size: 13px; color: var(--color-text-secondary); display: block; margin-bottom: 6px;">
                        Количество попыток
                    </label>
                    <input
                        type="number"
                        name="extra_attempts"
                        class="modal-input"
                        min="1"
                        max="100"
                        x-model="extraAttempts"
                        placeholder="Например: 1"
                        required
                    >
                    <div class="modal-buttons">
                        <button type="button" class="modal-btn modal-btn-secondary" @click="showModal = false">
                            Отмена
                        </button>
                        <button type="submit" class="modal-btn modal-btn-primary">
                            Выдать
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
        {{-- Group filter modal --}}
        <div
            x-show="groupModalOpen"
            x-cloak
            class="modal-overlay"
            @click.self="groupModalOpen = false"
        >
            <div class="modal-content" style="max-width: 460px; max-height: 80vh; display: flex; flex-direction: column; overflow: hidden;">
                <div class="modal-title" style="margin-bottom: 0.5rem;">Выбор групп</div>
                <p style="font-size: 13px; color: var(--color-text-secondary); margin-bottom: 1rem;">
                    Отметьте группы, студентов которых хотите видеть
                </p>

                <div style="position: relative; margin-bottom: 1rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--color-text-muted);">
                        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input
                        type="text"
                        x-model="groupSearch"
                        placeholder="Поиск группы..."
                        style="width:100%;padding:10px 12px 10px 36px;border:1.5px solid var(--color-border);border-radius:var(--r-md);font-size:14px;font-family:var(--font-body);box-sizing:border-box;"
                    >
                </div>

                <div style="overflow-y: auto; flex: 1; margin: 0 -2rem; padding: 0 2rem;">
                    @foreach($groups as $group)
                        <div
                            x-show="!groupSearch || '{{ $group->name }}'.toLowerCase().includes(groupSearch.toLowerCase())"
                            style="display:flex;align-items:center;gap:10px;padding:9px 1.25rem;border-bottom:1px solid var(--color-border);cursor:pointer;transition:background 0.15s;margin:0 -2rem;"
                            :style="tempGroupIds.includes({{ $group->id }}) ? 'background:var(--teal-50);' : ''"
                            @click="
                                const idx = tempGroupIds.indexOf({{ $group->id }});
                                if (idx === -1) { tempGroupIds.push({{ $group->id }}); }
                                else { tempGroupIds.splice(idx, 1); }
                            "
                        >
                            <span style="font-size:14px;flex:1;color:var(--color-text-primary);user-select:none;">{{ $group->name }}</span>
                            <span x-show="tempGroupIds.includes({{ $group->id }})" style="color:var(--teal-600);font-weight:700;font-size:15px;flex-shrink:0;">✓</span>
                        </div>
                    @endforeach
                    @if($groups->isEmpty())
                        <div style="text-align:center;padding:1.5rem;font-size:13px;color:var(--color-text-muted);">Нет групп</div>
                    @endif
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:1.25rem;margin-top:0.5rem;border-top:1px solid var(--color-border);flex-shrink:0;">
                    <button type="button" class="modal-btn modal-btn-secondary" @click="groupModalOpen = false">
                        Отмена
                    </button>
                    <button type="button" class="modal-btn modal-btn-primary" @click="applyGroupFilter()">
                        Применить
                    </button>
                </div>
            </div>
        </div>

    </main>
</div>

@endsection
