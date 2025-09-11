<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Доступные тесты</h2>
    <a href="/tests/create" class="btn btn-primary">Создать новый тест</a>
</div>
<div class="space-y-4">
    @forelse ($tests as $test)
        <div class="card flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $test->title }}</h3>
                <p class="text-gray-600 mt-1">{{ $test->description }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/tests/{{ $test->id }}/attempt" class="btn btn-green">Пройти тест</a>
                <a href="/tests/{{ $test->id }}" class="btn btn-secondary">Редактировать</a>
            </div>
        </div>
    @empty
        <div class="card text-center">
            <p>Тесты еще не созданы. <a href="/tests/create" class="text-blue-600 hover:underline">Создать первый тест?</a></p>
        </div>
    @endforelse
</div>
