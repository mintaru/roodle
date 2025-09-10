<div class="max-w-2xl mx-auto">
    <div class="card text-center">
        <h2 class="text-2xl font-bold mb-2">Результаты теста: "{{ $test->title }}"</h2>
        <div class="my-8">
            <p class="text-lg text-gray-700">Ваш результат:</p>
            <p class="text-6xl font-bold my-2 {{ $score >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $score }}%</p>
            <p class="text-gray-600">Правильных ответов: {{ $correctAnswers }} из {{ $totalQuestions }}</p>
        </div>
        <a href="/" class="btn btn-secondary">Вернуться к списку тестов</a>
    </div>
</div>
