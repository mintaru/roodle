@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Roodle</title>
    <style>
        body{
            margin:0;
            padding:0;
        }
    </style>
</head>
<body>


<div class="layout">

    {{-- Sidebar --}}
    <aside class="sidebar">

        <p class="sidebar-section-title">Навигация</p>

        @if($test->course)
            <a href="{{ route('courses.show', $test->course) }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            К курсу
            </a>
        @endif

        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            Все курсы
        </a>

        <p class="sidebar-section-title" style="margin-top: 2rem;">Курс</p>

        <div style="padding: 0 0.75rem;">
            @if($test->course)
                <p style="font-size: 13px; font-weight: 600; color: var(--gray-800); line-height: 1.4;">
                    {{ $test->course->title }}
                </p>
            @endif
        </div>

    </aside>

    {{-- Main --}}
    <main class="main">

        {{-- Breadcrumb --}}
        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}"
               style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;"
               onmouseover="this.style.color='var(--teal-600)'"
               onmouseout="this.style.color='var(--color-text-muted)'">
                Курсы
            </a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            @if($test->course)
                <a href="{{ route('courses.show', $test->course) }}"
                   style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;"
                   onmouseover="this.style.color='var(--teal-600)'"
                   onmouseout="this.style.color='var(--color-text-muted)'>
                    {{ $test->course->title }}
                </a>
            @endif
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span style="color: var(--gray-600); font-weight: 500;">{{ $test->title }}</span>
        </nav>

        {{-- Page header --}}
        <div class="page-header">
            <h1 class="page-header__title">{{ $test->title }}</h1>
            @if ($test->description)
                <p style="font-size: 14px; color: var(--color-text-muted); margin-top: 0.5rem;">
                    {{ $test->description }}
                </p>
            @endif
        </div>

        <div style="max-width: 760px;">



            @php $displayMode = $test->display_mode ?? 'per_question'; @endphp

            {{-- Stats panel --}}
            <div class="panel" style="padding: 1.5rem 2rem; margin-bottom: 1.25rem;">

                <div style="display: flex; gap: 2rem; flex-wrap: wrap; justify-content:center;">

                    <div style="text-align: center;">
                        <p style="font-size: 24px; font-weight: 700; color: var(--gray-800);">
                            {{ $isUnlimited ? '∞' : $maxAttemptsForUser }}
                        </p>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Всего попыток</p>
                    </div>

                    <div style="text-align: center;">
                        <p style="font-size: 24px; font-weight: 700; color: var(--gray-800);">
                            {{ $userAttemptsCount }}
                        </p>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Использовано</p>
                    </div>

                    <div style="text-align: center;">
                        <p style="font-size: 24px; font-weight: 700; color: var(--teal-600);">
                            {{ $isUnlimited ? '∞' : $remaining }}
                        </p>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Осталось</p>
                    </div>

                    @if ($test->time_limit > 0)
                        <div style="text-align: center;">
                            <p style="font-size: 24px; font-weight: 700; color: var(--gray-800);">
                                {{ $test->time_limit }}
                            </p>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Минут на тест</p>
                        </div>
                    @endif

                </div>

            </div>

            {{-- Active attempt banner --}}
            @if ($hasActiveAttempt)
                <div style="
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    margin-bottom: 1.25rem;
                    padding: 12px 16px;
                    background: var(--sky-50);
                    border-radius: var(--r-md);
                    border: 1px solid var(--sky-100);
                ">
                    <svg width="16" height="16" fill="none" stroke="var(--sky-500)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 1px;">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: var(--sky-700);">У вас есть активная попытка</p>
                        <p style="font-size: 13px; color: var(--sky-600); margin-top: 2px;">Вы можете продолжить или начать новую попытку</p>
                    </div>
                </div>
            @endif

            {{-- Attempt history --}}
            @if ($userAttempts->count() > 0)
                <div class="panel" style="padding: 1.5rem 2rem; margin-bottom: 1.25rem;">

                    <p style="font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 1rem;">
                        История попыток
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($userAttempts as $attempt)
                            <div style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                padding: 12px 14px;
                                background: var(--gray-50);
                                border-radius: var(--r-md);
                                border: 1px solid var(--color-border);
                            ">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; font-weight: 600; color: var(--gray-700);">
                                        Попытка #{{ $attempt->attempt_number }}
                                    </span>
                                    @if ($attempt->started_at)
                                        <span style="font-size: 12px; color: var(--color-text-muted);">
                                            {{ $attempt->started_at->format('d.m.Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <span style="font-size: 16px; font-weight: 700; color: var(--gray-800);">
                                        {{ $attempt->score }}%
                                    </span>
                                    @if ($test->is_details_available || auth()->user()->hasAnyRole(['admin','teacher']))
                                        <a href="{{ route('test-attempts.details', $attempt) }}"
                                           style="font-size: 13px; color: var(--teal-600); text-decoration: none; font-weight: 500;"
                                           onmouseover="this.style.textDecoration='underline'"
                                           onmouseout="this.style.textDecoration='none'">
                                            Обзор
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif

            {{-- Actions --}}
            <div class="panel" style="padding: 1.5rem 2rem;">

                @if ($isUnlimited || $userAttemptsCount < $maxAttemptsForUser)

                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;  justify-content: center;">

                        @if ($hasActiveAttempt)
                            @if ($displayMode === 'single_page')
                                <a href="{{ route('tests.attempt', $test) }}" class="btn btn-primary" style="padding: 10px 24px;">
                            @else
                                <a href="{{ route('tests.attempt.page', [$test->id, 1]) }}" class="btn btn-primary" style="padding: 10px 24px;">
                            @endif
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polygon points="5 3 19 12 5 21 5 3"/>
                                </svg>
                                Продолжить попытку
                            </a>
                        @endif

                        @if ($hasActiveAttempt)
                            <form action="{{ route('tests.attempt.force-new', $test) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-ghost" style="padding: 10px 24px; cursor: pointer; border: none; font-size: inherit;"
                                        onclick="return confirm('Это начнёт новую попытку. Продолжить?')">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 5v14"/><path d="M5 12h14"/>
                                    </svg>
                                    Начать новую попытку
                                </button>
                            </form>
                        @else
                            <a href="{{ $displayMode === 'single_page' ? route('tests.attempt', $test) : route('tests.attempt.page', [$test->id, 1]) }}"
                               class="btn btn-primary"
                               style="padding: 10px 24px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14"/><path d="M5 12h14"/>
                                </svg>
                                Пройти тест
                            </a>
                        @endif

                    </div>

                @else

                    <div style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        padding: 12px 16px;
                        background: #ffebee;
                        border-radius: var(--r-md);
                        border: 1px solid #ffcdd2;
                    ">
                        <svg width="16" height="16" fill="none" stroke="var(--red-500)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <p style="font-size: 13px; font-weight: 600; color: var(--red-500);">Попытки закончились</p>
                    </div>

                @endif

            </div>

        </div>

    </main>

</div>

@endsection
</body>
</html>
