@extends('layout')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="card">
            <h2 class="text-2xl font-bold text-center">{{ $test->title }}</h2>
            <p class="text-center text-gray-600 mt-2 mb-4">{{ $test->description }}</p>

            <div class="text-center mb-6">
                <p>Всего попыток: {{ $test->max_attempts == 0 ? '∞' : $test->max_attempts }}</p>
                <p>Использовано: {{ $userAttemptsCount }}</p>
                <p>Осталось: {{ $remaining }}</p>
                
                @foreach ($userAttempts as $attempt)
                <p>Попытка #{{ $attempt->attempt_number }}: {{ $attempt->score }}</p>
            @endforeach
            
            
                
            </div>

            <div class="text-center">
                @if ($test->max_attempts == 0 || $userAttemptsCount < $test->max_attempts)

                    <a href="{{ route('tests.attempt', $test) }}" class="btn btn-primary">
                        Пройти тест
                    </a>
                @else
                    <p class="text-red-500 font-semibold">Попытки закончились</p>
                @endif
            </div>
        </div>
    </div>
@endsection
