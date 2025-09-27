<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание нового теста</title>
    {{-- Меню: ПРАВИЛЬНОЕ МЕСТО (сразу после открытия <body>) --}}
    @include('components.menu')
    <!-- Подключение CSS -->
    <link rel="stylesheet" href="{{ asset('css/test-create-form.css') }}">
</head>
<body>
<div class="max-w-2xl mx-auto">
    <div class="card">
        <h2 class="text-2xl font-bold mb-6 text-center">Создание нового теста</h2>
        <form action="{{ route('tests.store', $course) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-semibold mb-2">Название теста</label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Описание</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>


            <div class="mb-6">
                <label for="max_attempts" class="block text-gray-700 font-semibold mb-2">Максимальное количество попыток</label>
                <input type="number" id="max_attempts" name="max_attempts" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" min="1" value="1">
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="unlimited_attempts" name="unlimited_attempts" value="1" class="mr-2">
                        Неограниченное количество попыток
                    </label>
                </div>
            </div>
            <script>
                const checkbox = document.getElementById('unlimited_attempts');
                const numberInput = document.getElementById('max_attempts');
            
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        numberInput.disabled = true;
                        numberInput.value = 0; // 0 будет означать неограниченно
                    } else {
                        numberInput.disabled = false;
                        numberInput.value = 1;
                    }
                });
            </script>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Создать и добавить вопросы</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
