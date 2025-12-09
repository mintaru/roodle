<div class="py-10 px-6">
    <h1 class="text-3xl font-bold text-center mb-10">Наши курсы</h1>

    <div class="p-6">
        <h1 class="text-xl font-bold mb-3">Поиск</h1>
    
        <input type="text" wire:model.live="search" placeholder="Введите текст..." class="border rounded p-2">
    
    </div>
    

    

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    
        @if(auth()->check())
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
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 flex flex-col">
                @if($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}"
                         class="w-full h-48 object-cover rounded-lg mb-4">
                @endif
                <h2 class="text-2xl font-semibold mb-3 text-gray-800">
                    {{ $course->title }}
                </h2>
                <p class="text-gray-600 flex-grow">
                    {{ Str::limit($course->description, 120) }}
                </p>
                <a href="{{ route('courses.show', $course) }}"
                   class="mt-4 inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Перейти на курс
                </a>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500">
                Курсов пока нет 🙁
            </div>
        @endforelse
    </div>
</div>
