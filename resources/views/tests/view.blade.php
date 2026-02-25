@extends('layout')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="card">
            <h2 class="text-2xl font-bold text-center">{{ $test->title }}</h2>
            <p class="text-center text-gray-600 mt-2 mb-4">{{ $test->description }}</p>
            @hasanyrole('admin|teacher')
            <a href="{{ route('admin.tests.attempts', ['id' => $test->id]) }}">
                Показать попытки
            </a>
            @endhasanyrole
            <div class="text-center mb-6">
                <p class="font-semibold">Всего попыток: {{ $isUnlimited ? '∞' : $maxAttemptsForUser }}</p>
                <p>Использовано: {{ $userAttemptsCount }}</p>
                <p>Осталось: {{ $remaining }}</p>

                @if ($hasActiveAttempt)
                    <div class="mt-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-left">
                        <p class="text-blue-800 font-semibold">⏳ У вас есть активная попытка прохождения теста</p>
                        <p class="text-blue-700 text-sm">Вы можете продолжить или начать новую попытку</p>
                    </div>
                @endif

                @if ($userAttempts->count() > 0)
                    <div class="mt-6">
                        <h3 class="font-semibold mb-3">История попыток:</h3>
                        <div class="space-y-2">
                            @foreach ($userAttempts as $attempt)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <span>Попытка #{{ $attempt->attempt_number }}</span>
                                    <span class="font-bold text-lg">{{ $attempt->score }}%</span>
                                    @if ($attempt->started_at)
                                        <span
                                            class="text-xs text-gray-500">{{ $attempt->started_at->format('d.m.Y H:i') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-center space-y-3">
                @if ($isUnlimited || $userAttemptsCount < $maxAttemptsForUser)
                    @if ($hasActiveAttempt)
                        <a href="{{ route('tests.attempt', $test) }}" class="btn btn-primary">
                            Продолжить попытку
                        </a>
                    @endif
                    <a href="{{ route('tests.attempt', $test) }}" class="btn btn-secondary"
                        onclick="if(confirm('Это начнет новую попытку. Продолжить?')) { return true; } return false;">
                        {{ $hasActiveAttempt ? 'Начать новую попытку' : 'Пройти тест' }}
                    </a>
                @else
                    <p class="text-red-500 font-semibold">Попытки закончились</p>
                @endif
            </div>
        </div>
    </div>
@endsection
