<div class="py-10 px-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-bold">Наши курсы</h1>
        @if(auth()->user()->hasRole('teacher'))
            <a href="{{ route('courses.create') }}" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-semibold">
                ➕ Создать курс
            </a>
        @endif
    </div>

    <div class="p-6">
        <h1 class="text-xl font-bold mb-3">Поиск</h1>
        <input type="text" wire:model.live="search" placeholder="Введите текст..." class="border rounded p-2">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        @if (auth()->check())
            {{-- <div class="p-4 bg-gray-100 rounded mb-4">
        <strong>Ваши группы:</strong>
        @forelse(auth()->user()->groups as $group)
            <span class="inline-block bg-blue-200 text-blue-800 px-2 py-1 rounded mr-2">
                {{ $group->name }}
            </span>
        @empty
            <span class="text-gray-500">Вы пока не в группе</span>
        @endforelse
    </div> --}}
        @endif

        @forelse($courses as $course)
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
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 flex flex-col relative"
                x-data="{ open: false }">

                @if(auth()->user()->hasRole('admin') || $course->user_id === auth()->id())
                    <button @click="open = !open"
                        class="mt-4 inline-block text-center absolute top-4 right-4 hover:bg-gray-100 p-2 rounded">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="5" r="2" />
                            <circle cx="12" cy="12" r="2" />
                            <circle cx="12" cy="19" r="2" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition x-cloak
                        class="absolute right-0 top-10 z-30 bg-white border rounded-lg shadow-lg w-44">
                        <form action="{{ route('courses.archive', $course) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                Архивировать
                            </button>
                        </form>
                    </div>
                @endif

                @if ($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}"
                        class="w-full h-48 object-cover rounded-lg mb-4">
                @endif
                
                <h2 class="text-2xl font-semibold mb-3 text-gray-800">
                    {{ $course->title }}
                </h2>
                <p class="text-gray-600 flex-grow">
                    {{ Str::limit($course->description, 120) }}
                </p>
                <p class="text-gray-600 flex-grow">
                    Доступен с {{ $course->formattedPeriodStartForUser(auth()->user()) ?? '—' }}
                </p>
                <p class="text-gray-600 flex-grow">
                    Доступен до {{ $course->formattedPeriodEndForUser(auth()->user()) ?? '—' }}
                </p>

                <div class="flex gap-2 mt-4">
                    <a href="{{ route('courses.show', $course) }}"
                        class="flex-1 inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Перейти
                    </a>
                    
                    @if($canEdit)
                        <a href="{{ route('courses.edit', $course) }}" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 font-medium">
                            ✏️
                        </a>
                    @endif
                    
                    @if($canDelete)
                        <form action="{{ route('courses.destroy', $course) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-medium" 
                                onclick="return confirm('Вы уверены?')">
                                🗑️
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-8">
                <p class="text-lg">Курсов пока нет 🙁</p>
                @if(auth()->user()->hasRole('teacher'))
                    <a href="{{ route('courses.create') }}" class="mt-4 inline-block px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700">
                        Создать первый курс
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</div>
