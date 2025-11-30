<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Банк вопросов</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Банк вопросов</h1>

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search Form -->
    <div class="mb-6 p-4 bg-gray-50 rounded border">
        <form method="GET" action="{{ route('admin.question-bank.index') }}" class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Искать по колонке:</label>
                <select name="search_column" id="search_column" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="question_text" {{ $searchColumn === 'question_text' ? 'selected' : '' }}>Текст вопроса</option>
                    <option value="id" {{ $searchColumn === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="question_type" {{ $searchColumn === 'question_type' ? 'selected' : '' }}>Тип вопроса</option>
                </select>
            </div>
            <div class="flex-1 min-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Поисковый запрос:</label>
                <input type="text" name="search_value" placeholder="Введите текст для поиска..." value="{{ $searchValue }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Поиск</button>
            <a href="{{ route('admin.question-bank.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Очистить</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border">
            <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Текст вопроса</th>
                <th class="p-2 border">Тип</th>
                <th class="p-2 border">Ответы</th>
                <th class="p-2 border">В тестах</th>
                <th class="p-2 border">Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($questions as $question)
                <tr>
                    <td class="p-2 border">{{ $question->id }}</td>
                    <td class="p-2 border">
                        <div class="max-w-xs truncate" title="{{ $question->question_text }}">
                            {{ substr($question->question_text, 0, 50) }}{{ strlen($question->question_text) > 50 ? '...' : '' }}
                        </div>
                    </td>
                    <td class="p-2 border text-sm">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</td>
                    <td class="p-2 border text-center">{{ $question->options->count() }}</td>
                    <td class="p-2 border text-center">{{ $question->tests->count() }}</td>
                    <td class="p-2 border">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.question-bank.edit', $question) }}" class="text-blue-600 hover:underline">Редактировать</a>
                            <form action="{{ route('admin.question-bank.destroy', $question) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Вы уверены, что хотите удалить этот вопрос? Он будет удалён из всех тестов.')">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-2 border text-center text-gray-500">Вопросы не найдены</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($questions->hasPages())
        <div class="mt-6">
            {{ $questions->links() }}
        </div>
    @endif
</div>

</body>
</html>
