<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список курсов</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <div class="mb-4">
        @if(auth()->user()->hasRole('admin'))
            <x-back-button :url="route('admin.dashboard')" text="В админ-панель" />
        @else
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                ← На главную
            </a>
        @endif
    </div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">
                @if(auth()->user()->hasRole('admin'))
                    Администрирование курсов
                @else
                    Мои курсы
                @endif
            </h1>
        </div>
        @if(auth()->user()->hasRole('teacher') || auth()->user()->hasRole('admin'))
            <a href="{{ route('courses.create') }}" 
                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-semibold">
                ➕ Создать курс
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Form -->
    <div class="mb-6 p-4 bg-gray-50 rounded border">
        <form method="GET" action="{{ route('home') }}" class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Искать по колонке:</label>
                <select name="search_column" id="search_column" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="title" {{ $searchColumn === 'title' ? 'selected' : '' }}>Название</option>
                    <option value="id" {{ $searchColumn === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="author" {{ $searchColumn === 'author' ? 'selected' : '' }}>Автор</option>
                    <option value="description" {{ $searchColumn === 'description' ? 'selected' : '' }}>Описание</option>
                </select>
            </div>
            <div class="flex-1 min-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Поисковый запрос:</label>
                <input type="text" name="search_value" placeholder="Введите текст для поиска..." value="{{ $searchValue }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Поиск</button>
            <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Очистить</a>
        </form>
    </div>

    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($courses as $course)
                <div class="border border-gray-300 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                    @if($course->image_path)
                        <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500">Нет изображения</span>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $course->title }}</h3>
                        <p class="text-xs text-gray-500 mb-4">Автор: <strong>{{ $course->author->name ?? 'Неизвестен' }}</strong></p>
                        
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('courses.show', $course) }}" 
                                class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 text-center">
                                Открыть
                            </a>
                            
                            @php
                                // Проверяем права редактирования
                                $canEdit = auth()->user()->hasRole('admin') || 
                                           $course->user_id === auth()->id() || 
                                           $course->teacherPermissions()
                                               ->where('user_id', auth()->id())
                                               ->where('can_edit', true)
                                               ->exists();
                                
                                $canDelete = auth()->user()->hasRole('admin') || 
                                            $course->user_id === auth()->id() || 
                                            $course->teacherPermissions()
                                                ->where('user_id', auth()->id())
                                                ->where('can_delete', true)
                                                ->exists();
                            @endphp
                            
                            @if($canEdit)
                                <a href="{{ route('courses.edit', $course) }}" 
                                    class="px-3 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                    ✏️
                                </a>
                            @endif
                            
                            @if($canDelete)
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700" 
                                        onclick="return confirm('Вы уверены?')">
                                        🗑️
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg mb-4">Курсы не найдены</p>
            @if(auth()->user()->hasRole('teacher') || auth()->user()->hasRole('admin'))
                <a href="{{ route('courses.create') }}" class="inline-block px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700">
                    Создать первый курс
                </a>
            @endif
        </div>
    @endif
</div>

</body>
</html>
