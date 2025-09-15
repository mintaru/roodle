<div class="max-w-2xl mx-auto">
    <div class="card">
        <h2 class="text-2xl font-bold mb-6 text-center">Создание нового теста</h2>
        <form action="{{ route('tests.store', $course) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-semibold mb-2">Название теста</label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Описание</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Создать и добавить вопросы</button>
            </div>
        </form>
    </div>
</div>
