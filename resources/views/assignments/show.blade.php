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

    .status-badge.submitted {
        background: var(--green-50);
        color: var(--green-600);
        border: 1.5px solid var(--green-100);
    }

    .status-badge.graded {
        background: var(--sky-50);
        color: var(--sky-700);
        border: 1.5px solid var(--sky-100);
    }

    .status-badge.pending {
        background: #fff8e1;
        color: #e65100;
        border: 1.5px solid #ffecb3;
    }

    .status-badge.not-submitted {
        background: var(--gray-100);
        color: var(--gray-500);
        border: 1.5px solid var(--gray-200);
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
        text-decoration: none;
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

    .action-btn-orange {
        background: #e65100;
        color: white;
    }

    .action-btn-orange:hover {
        background: #bf360c;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--color-text-muted);
        font-size: 14px;
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
    .filter-pill .pill-dot.dot-submitted { background: var(--green-500); }
    .filter-pill .pill-dot.dot-graded { background: var(--sky-500); }
    .filter-pill .pill-dot.dot-pending { background: #e65100; }
    .filter-pill .pill-dot.dot-not { background: var(--gray-300); }

    .submission-detail-section {
        margin-bottom: 1.25rem;
    }

    .submission-detail-section:last-child {
        margin-bottom: 0;
    }

    .submission-detail-section__title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
    }

    .submission-text {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--r-lg);
        padding: 1rem;
        font-size: 14px;
        line-height: 1.6;
        color: var(--gray-800);
        white-space: pre-wrap;
    }

    .file-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border: 1px solid var(--color-border);
        border-radius: var(--r-lg);
        background: var(--color-surface);
        transition: border-color 0.2s;
    }

    .file-item:hover {
        border-color: var(--teal-300);
    }

    .file-item__info {
        min-width: 0;
    }

    .file-item__name {
        font-weight: 600;
        font-size: 13px;
        color: var(--gray-800);
    }

    .file-item__meta {
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: 2px;
    }

    .grade-form {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .grade-form-row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .grade-form-row .form-group {
        flex: 1;
        min-width: 120px;
    }

    .grade-form label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--color-text-secondary);
        margin-bottom: 4px;
    }

    .grade-form input,
    .grade-form textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1.5px solid var(--color-border);
        border-radius: var(--r-md);
        font-size: 13px;
        font-family: var(--font-body);
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .grade-form input:focus,
    .grade-form textarea:focus {
        outline: none;
        border-color: var(--teal-400);
    }

    .grade-form textarea {
        resize: vertical;
        min-height: 60px;
    }

    .graded-display {
        background: var(--green-50);
        border: 1px solid var(--green-100);
        border-radius: var(--r-lg);
        padding: 1rem;
    }

    .graded-display__score {
        font-family: var(--font-display);
        font-size: 24px;
        font-weight: 800;
        color: var(--green-600);
    }

    .graded-display__comment {
        margin-top: 8px;
        font-size: 13px;
        color: var(--gray-700);
        line-height: 1.5;
    }

    .graded-display__meta {
        font-size: 11px;
        color: var(--color-text-muted);
        margin-top: 6px;
    }

    .micro-badge {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: var(--r-full);
        font-size: 11px;
        font-weight: 700;
    }

    .micro-badge.files {
        background: var(--teal-50);
        color: var(--teal-600);
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

    .success-flash {
        background: var(--green-50);
        border: 1px solid var(--green-100);
        color: var(--green-700);
        padding: 12px 16px;
        border-radius: var(--r-lg);
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 1.25rem;
    }

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
    }
</style>
@endsection

@section('content')
<div class="layout" x-data="{
    filter: 'all',
    search: '',
    groupFilter: 'all',
    editingGrade: null,
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
            <span style="color: var(--gray-600); font-weight: 500;">{{ $assignment->title }}</span>
        </nav>

        <div class="page-header">
            <h1 class="page-header__title">{{ $assignment->title }}</h1>
        </div>

        @php
            $submitted = $submissions->filter(function($s) { return $s->submitted_at && !$s->isGraded(); });
            $graded = $submissions->filter(function($s) { return $s->isGraded(); });
            $notSubmitted = $submissions->filter(function($s) { return !$s->submitted_at; });
        @endphp

        @if(session('success'))
            <div class="success-flash">{{ session('success') }}</div>
        @endif

        <div class="form-card" style="max-width: 900px;">

            {{-- Section: stats --}}
            <div class="form-section">
                <div class="admin-stats">
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value">{{ $submissions->count() }}</div>
                        <div class="admin-stats__stat-label">Студентов</div>
                    </div>
                    <div class="admin-stats__divider"></div>
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value" style="color: var(--green-600);">{{ $graded->count() }}</div>
                        <div class="admin-stats__stat-label">Оценено</div>
                    </div>
                    <div class="admin-stats__divider"></div>
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value" style="color: #e65100;">{{ $submitted->count() }}</div>
                        <div class="admin-stats__stat-label">Ожидает</div>
                    </div>
                    <div class="admin-stats__divider"></div>
                    <div class="admin-stats__stat">
                        <div class="admin-stats__stat-value" style="color: var(--gray-500);">{{ $notSubmitted->count() }}</div>
                        <div class="admin-stats__stat-label">Не сдано</div>
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
                    <button type="button" class="filter-pill" :class="{ active: filter === 'submitted' }" @click="filter = 'submitted'">
                        <span class="pill-dot dot-submitted"></span>
                        Сдано
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'graded' }" @click="filter = 'graded'">
                        <span class="pill-dot dot-graded"></span>
                        Оценено
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'pending' }" @click="filter = 'pending'">
                        <span class="pill-dot dot-pending"></span>
                        Ожидает
                    </button>
                    <button type="button" class="filter-pill" :class="{ active: filter === 'not_submitted' }" @click="filter = 'not_submitted'">
                        <span class="pill-dot dot-not"></span>
                        Не сдано
                    </button>
                </div>

                <div class="filter-pills" style="margin-top: 0.75rem;">
                    <input type="text" x-model="search" placeholder="Поиск по имени..." style="flex: 1; min-width: 180px; padding: 8px 12px; border: 1.5px solid var(--color-border); border-radius: var(--r-full); font-size: 13px; font-family: var(--font-body); outline: none;" @focus="$el.style.borderColor='var(--teal-400)'" @blur="$el.style.borderColor='var(--color-border)'">
                </div>

                @if($groups->count() > 0)
                    <div class="filter-pills" style="margin-top: 0.5rem;">
                        <button type="button" class="filter-pill" :class="{ active: groupFilter === 'all' }" @click="groupFilter = 'all'">
                            Все группы
                        </button>
                        @foreach($groups as $group)
                            <button type="button" class="filter-pill" :class="{ active: groupFilter === '{{ $group->id }}' }" @click="groupFilter = '{{ $group->id }}'">
                                {{ $group->name }}
                            </button>
                        @endforeach
                    </div>
                @endif

                @if($submissions->count() > 0)
                    @foreach($submissions as $submission)
                        @php
                            if ($submission->isGraded()) {
                                $statusText = 'Оценено';
                                $statusClass = 'graded';
                            } elseif ($submission->submitted_at) {
                                $statusText = 'Сдано';
                                $statusClass = 'submitted';
                            } else {
                                $statusText = 'Не сдано';
                                $statusClass = 'not-submitted';
                            }

                            $hasFiles = $submission->files->count() > 0;
                        @endphp
                        @php
                            $userGroupIds = $submission->user->groups->pluck('id')->implode(',');
                            $userNameLower = strtolower($submission->user->name);
                        @endphp
                        <div class="q-card" x-data="{ expanded: false }" data-groups="{{ $userGroupIds }}" x-show="(filter === 'all' || (filter === 'submitted' && {{ $submission->submitted_at && !$submission->isGraded() ? 'true' : 'false' }}) || (filter === 'graded' && {{ $submission->isGraded() ? 'true' : 'false' }}) || (filter === 'pending' && {{ $submission->submitted_at && !$submission->isGraded() ? 'true' : 'false' }}) || (filter === 'not_submitted' && {{ !$submission->submitted_at ? 'true' : 'false' }})) && (search === '' || '{{ $userNameLower }}'.includes(search.toLowerCase())) && (groupFilter === 'all' || '{{ $userGroupIds }}'.split(',').includes(groupFilter))" x-cloak>
                            <div class="q-card__header">
                                <div class="q-card__header-left">
                                    <div class="q-card__meta">
                                        <div class="q-card__text">{{ $submission->user->name }}</div>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex-shrink: 0;">
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $statusText }}
                                        @if($submission->submitted_at && !$submission->isGraded())
                                            <span style="font-weight: 400; font-size: 11px; margin-left: 4px;">{{ $submission->submitted_at->format('d.m H:i') }}</span>
                                        @endif
                                    </span>

                                    @if($submission->isGraded())
                                        <span class="score {{ $submission->score >= 75 ? 'high' : ($submission->score >= 50 ? 'medium' : 'low') }}">
                                            {{ $submission->score }}
                                        </span>
                                    @else
                                        <span class="dash">—</span>
                                    @endif

                                    @if($hasFiles)
                                        <span class="micro-badge files">
                                            {{ $submission->files->count() }} файл{{ $submission->files->count() > 1 ? 'а' : '' }}
                                        </span>
                                    @endif

                                    <button type="button" class="action-btn action-btn-outline" @click="expanded = !expanded">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Детали
                                    </button>
                                </div>
                            </div>

                            <div x-show="expanded" x-cloak>
                                <div class="q-card__body" style="background: var(--gray-50);">
                                    @if(!$submission->submitted_at)
                                        <div class="empty-state">Студент ещё не сдал задание</div>
                                    @else
                                        {{-- Answer text --}}
                                        @if($submission->answer_text)
                                            <div class="submission-detail-section">
                                                <div class="submission-detail-section__title">Текст ответа</div>
                                                <div class="submission-text">{!! nl2br(e($submission->answer_text)) !!}</div>
                                            </div>
                                        @endif

                                        {{-- Files --}}
                                        @if($hasFiles)
                                            <div class="submission-detail-section">
                                                <div class="submission-detail-section__title">Загруженные файлы</div>
                                                <div class="file-list">
                                                    @foreach($submission->files as $file)
                                                        <div class="file-item">
                                                            <div class="file-item__info">
                                                                <div class="file-item__name">{{ $file->file_name }}</div>
                                                                <div class="file-item__meta">{{ number_format($file->file_size / 1024, 1) }} KB • {{ $file->created_at->format('d.m.Y H:i') }}</div>
                                                            </div>
                                                            <a href="{{ route('assignments.download-submission-file', [$course, $assignment, $submission, $file]) }}" class="action-btn action-btn-blue">Скачать</a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Grade form --}}
                                        <div class="submission-detail-section">
                                            @if($submission->isGraded())
                                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                                                    <div class="submission-detail-section__title" style="margin-bottom: 0;">Оценка</div>
                                                    <button type="button" class="action-btn action-btn-secondary" @click="editingGrade = editingGrade === {{ $submission->id }} ? null : {{ $submission->id }}">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        Редактировать
                                                    </button>
                                                </div>
                                                <div x-show="editingGrade !== {{ $submission->id }}">
                                                    <div class="graded-display">
                                                        <div class="graded-display__score">{{ $submission->score }}</div>
                                                        @if($submission->teacher_comment)
                                                            <div class="graded-display__comment">{!! nl2br(e($submission->teacher_comment)) !!}</div>
                                                        @endif
                                                        <div class="graded-display__meta">Оценено {{ $submission->graded_at->format('d.m.Y H:i') }}</div>
                                                    </div>
                                                </div>
                                                <div x-show="editingGrade === {{ $submission->id }}" x-cloak>
                                                    <form action="{{ route('assignments.grade', [$course, $assignment, $submission]) }}" method="POST" class="grade-form">
                                                        @csrf
                                                        <div class="grade-form-row">
                                                            <div class="form-group">
                                                                <label for="score-{{ $submission->id }}">Оценка</label>
                                                                <input type="number" id="score-{{ $submission->id }}" name="score" step="0.01" min="2" max="5" value="{{ $submission->score }}" required>
                                                            </div>
                                                            <div style="display: flex; align-items: flex-end; gap: 8px;">
                                                                <button type="submit" class="action-btn action-btn-orange">
                                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                                                    Сохранить
                                                                </button>
                                                                <button type="button" class="action-btn action-btn-secondary" @click="editingGrade = null">Отмена</button>
                                                            </div>
                                                        </div>
                                                        <div class="form-group" style="margin-bottom: 0;">
                                                            <label for="comment-{{ $submission->id }}">Комментарий</label>
                                                            <textarea id="comment-{{ $submission->id }}" name="teacher_comment">{{ $submission->teacher_comment ?? '' }}</textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="submission-detail-section__title">Выставить оценку</div>
                                                <form action="{{ route('assignments.grade', [$course, $assignment, $submission]) }}" method="POST" class="grade-form">
                                                    @csrf
                                                    <div class="grade-form-row">
                                                        <div class="form-group">
                                                            <label for="score-{{ $submission->id }}">Оценка</label>
                                                            <input type="number" id="score-{{ $submission->id }}" name="score" step="0.01" min="2" max="5" value="{{ $submission->score ?? '' }}" required>
                                                        </div>
                                                        <div style="display: flex; align-items: flex-end;">
                                                            <button type="submit" class="action-btn action-btn-orange">
                                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                                                Выставить
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 0;">
                                                        <label for="comment-{{ $submission->id }}">Комментарий</label>
                                                        <textarea id="comment-{{ $submission->id }}" name="teacher_comment">{{ $submission->teacher_comment ?? '' }}</textarea>
                                                    </div>
                                                </form>
                                            @endif
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
    </main>
</div>

@endsection
