<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/courses-show.css') }}">
    {{-- Если вам нужны другие CSS/JS, они должны быть здесь --}}
</head>
<body>
{{-- Меню: ПРАВИЛЬНОЕ МЕСТО (сразу после открытия <body>) --}}
@include('components.menu')

<div class="container">
    <div class="mb-4">
        <x-back-button :url="route('home')" text="К курсам" />
    </div>
    <h1>{{ $course->title }}</h1>
    <p>{{ $course->description }}</p>

    {{-- Кнопки --}}
    @hasanyrole('teacher|admin')
    <a href="{{ route('tests.create', $course) }}" class="btn btn-primary">
        Создать тест для курса
    </a>
    <a href="{{ route('lectures.create', $course) }}" class="btn btn-success">
        Создать лекцию для курса
    </a>
    <a href="{{ route('materials.create', $course) }}" class="btn btn-info">
        Загрузить материал для курса
    </a>
    <a href="{{ route('assignments.create', $course) }}" class="btn btn-warning">
        Создать задание для курса
    </a>
    @endhasanyrole

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-200 text-red-800 rounded mb-4">{{ session('error') }}</div>
    @endif

    <hr>

    @php
        $allSectionItemTestIds = collect();
        $allSectionItemLectureIds = collect();
        $allSectionItemMaterialIds = collect();
        foreach ($course->sections as $section) {
            foreach ($section->items as $item) {
                if ($item->item instanceof \App\Models\Test) {
                    $allSectionItemTestIds->push($item->item->id);
                }
                if ($item->item instanceof \App\Models\Lecture) {
                    $allSectionItemLectureIds->push($item->item->id);
                }
                if ($item->item instanceof \App\Models\Material) {
                    $allSectionItemMaterialIds->push($item->item->id);
                }
            }
        }
    @endphp

    @hasanyrole('teacher|admin')
        <div class="mb-4">
            <h2>Добавить секцию</h2>
            <form action="{{ route('courses.sections.store', $course) }}" method="POST">
                @csrf
                <input type="text" name="title" placeholder="Название секции" required>
                <button type="submit" class="btn btn-secondary">Создать секцию</button>
            </form>
        </div>
    @endhasanyrole

    @foreach($course->sections as $section)
        <div class="course-section mb-4 p-3 border rounded">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h2 class="inline-block mr-2">{{ $section->title }}</h2>
                    @hasanyrole('teacher|admin')
                        <form action="{{ route('courses.sections.update', [$course, $section]) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="text" name="title" value="{{ $section->title }}" class="small-input">
                            <button type="submit" class="btn btn-secondary btn-xs">Переименовать</button>
                        </form>
                    @endhasanyrole
                </div>
                @hasanyrole('teacher|admin')
                    <div class="flex gap-2">
                        <form action="{{ route('courses.sections.move', [$course, $section]) }}" method="POST" class="inline-block">
                            @csrf
                            <input type="hidden" name="direction" value="up">
                            <button type="submit" class="btn btn-light btn-xs">↑</button>
                        </form>
                        <form action="{{ route('courses.sections.move', [$course, $section]) }}" method="POST" class="inline-block">
                            @csrf
                            <input type="hidden" name="direction" value="down">
                            <button type="submit" class="btn btn-light btn-xs">↓</button>
                        </form>
                        <form action="{{ route('courses.sections.destroy', [$course, $section]) }}" method="POST" class="inline-block" onsubmit="return confirm('Удалить секцию?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Удалить</button>
                        </form>
                    </div>
                @endhasanyrole
            </div>

            <ul class="ml-4">
                @forelse($section->items as $sectionItem)
                    @php
                        $item = $sectionItem->item;
                        $isArchived = ($item instanceof \App\Models\Test && ($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
                            || ($item instanceof \App\Models\Lecture && ($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED);
                    @endphp
                    @if($isArchived && !auth()->user()?->hasAnyRole(['teacher','admin']))
                        @continue
                    @endif
                    @if($item instanceof \App\Models\Test)
                        <li class="mb-2">
                            <strong>Тест:</strong>
                            @hasanyrole('teacher|admin')
                                @if(($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
                                    <span class="text-yellow-700 font-medium">[архивирован]</span>
                                @endif
                            @endhasanyrole
                            <a href="{{ route('tests.view', $item) }}">{{ $item->title }}</a><br>
                            @can('edit courses')
                                <a href="{{ route('tests.show', $item) }}">Редактировать тест</a><br>
                                <a href="{{ route('tests.results', $item) }}" class="btn btn-info">Обзор теста</a><br>
                            @endcan
                            @auth
                                @php
                                    $remainingForThisTest = $remainingByTest[$item->id] ?? null;
                                @endphp
                                @if($remainingForThisTest !== null)
                                    <span>
                                        попытки: {{ $remainingForThisTest }}
                                    </span>
                                @endif
                            @endauth
                            <p class="text-gray-600 flex-grow">
                                Доступен с {{ $item->formattedPeriodStart() ?? '—' }}
                            </p>
                            <p class="text-gray-600 flex-grow">
                                Доступен до {{ $item->formattedPeriodEnd() ?? '—' }}
                            </p>
                            @php
                                $displayMode = $item->display_mode ?? 'per_question';
                            @endphp
                            @if($displayMode === 'single_page')
                                <a href="{{ route('tests.attempt', $item) }}">пройти тест</a>
                            @else
                                <a href="{{ route('tests.attempt.page', [$item->id, 1]) }}">пройти тест</a>
                            @endif

                            @hasanyrole('teacher|admin')
                                <div class="mt-1 flex gap-2">
                                    <form action="{{ route('courses.sections.items.move', [$course, $section, $sectionItem]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="btn btn-light btn-xs">↑</button>
                                    </form>
                                    <form action="{{ route('courses.sections.items.move', [$course, $section, $sectionItem]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="btn btn-light btn-xs">↓</button>
                                    </form>
                                    <form action="{{ route('courses.sections.items.detach', [$course, $section, $sectionItem]) }}" method="POST" onsubmit="return confirm('Убрать элемент из секции?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">Убрать</button>
                                    </form>
                                </div>
                            @endhasanyrole
                        </li>
                    @elseif($item instanceof \App\Models\Lecture)
                        <li class="mb-2">
                            <strong>Лекция:</strong>
                            @hasanyrole('teacher|admin')
                                @if(($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED)
                                    <span class="text-yellow-700 font-medium">[архивирован]</span>
                                @endif
                            @endhasanyrole
                            <a href="{{ route('lectures.show', ['course' => $course, 'lecture' => $item]) }}">
                                {{ $item->title }}
                            </a>

                            @hasanyrole('teacher|admin')
                                <div class="mt-1 flex gap-2">
                                    <form action="{{ route('courses.sections.items.move', [$course, $section, $sectionItem]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="btn btn-light btn-xs">↑</button>
                                    </form>
                                    <form action="{{ route('courses.sections.items.move', [$course, $section, $sectionItem]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="btn btn-light btn-xs">↓</button>
                                    </form>
                                    <form action="{{ route('courses.sections.items.detach', [$course, $section, $sectionItem]) }}" method="POST" onsubmit="return confirm('Убрать элемент из секции?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">Убрать</button>
                                    </form>
                                </div>
                            @endhasanyrole
                        </li>
                    @elseif($item instanceof \App\Models\Material)
                        <li class="mb-2">
                            <strong>📎 Материал:</strong>
                            @hasanyrole('teacher|admin')
                                @if(($item->status ?? 'active') === \App\Models\Material::STATUS_ARCHIVED)
                                    <span class="text-yellow-700 font-medium">[архивирован]</span>
                                @endif
                            @endhasanyrole
                            <span>{{ $item->title }}</span>
                            <a href="{{ route('materials.download', ['course' => $course, 'material' => $item]) }}" class="btn btn-sm">
                                ⬇ Скачать
                            </a>

                            @hasanyrole('teacher|admin')
                                <div class="mt-1 flex gap-2">
                                    <form action="{{ route('courses.sections.items.move', [$course, $section, $sectionItem]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="btn btn-light btn-xs">↑</button>
                                    </form>
                                    <form action="{{ route('courses.sections.items.move', [$course, $section, $sectionItem]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="btn btn-light btn-xs">↓</button>
                                    </form>
                                    <form action="{{ route('courses.sections.items.detach', [$course, $section, $sectionItem]) }}" method="POST" onsubmit="return confirm('Убрать элемент из секции?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">Убрать</button>
                                    </form>
                                </div>
                            @endhasanyrole
                        </li>
                    @endif
                @empty
                    <li>В этой секции пока нет элементов</li>
                @endforelse
            </ul>

            @hasanyrole('teacher|admin')
                <div class="mt-3">
                    <form action="{{ route('courses.sections.items.attach', [$course, $section]) }}" method="POST" class="mb-2">
                        @csrf
                        <label>
                            Добавить тест:
                            <select name="item_id">
                                @foreach($course->tests->where('status', \App\Models\Test::STATUS_ACTIVE) as $test)
                                    @if(!$allSectionItemTestIds->contains($test->id))
                                        <option value="{{ $test->id }}">{{ $test->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="item_type" value="test">
                        <button type="submit" class="btn btn-secondary btn-xs">Добавить</button>
                    </form>

                    <form action="{{ route('courses.sections.items.attach', [$course, $section]) }}" method="POST">
                        @csrf
                        <label>
                            Добавить лекцию:
                            <select name="item_id">
                                @foreach($course->lectures->where('status', \App\Models\Lecture::STATUS_ACTIVE) as $lecture)
                                    @if(!$allSectionItemLectureIds->contains($lecture->id))
                                        <option value="{{ $lecture->id }}">{{ $lecture->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="item_type" value="lecture">
                        <button type="submit" class="btn btn-secondary btn-xs">Добавить</button>
                    </form>

                    <form action="{{ route('courses.sections.items.attach', [$course, $section]) }}" method="POST">
                        @csrf
                        <label>
                            Добавить материал:
                            <select name="item_id">
                                @foreach($course->materials->where('status', \App\Models\Material::STATUS_ACTIVE) as $material)
                                    @if(!$allSectionItemMaterialIds->contains($material->id))
                                        <option value="{{ $material->id }}">{{ $material->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="item_type" value="material">
                        <button type="submit" class="btn btn-secondary btn-xs">Добавить</button>
                    </form>
                </div>
            @endhasanyrole
        </div>
    @endforeach

    {{-- Фолбэк: если секций нет, показываем старые блоки --}}
    @if($course->sections->isEmpty())
        <h2>Тесты курса</h2>
        <ul>
            @forelse($course->tests as $test)
                <li>
                    @hasanyrole('teacher|admin')
                        @if(($test->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
                            <span class="text-yellow-700 font-medium">[архивирован]</span>
                        @endif
                    @endhasanyrole
                    <a href="{{ route('tests.view', $test) }}">{{ $test->title }}</a><br>
                    @can('edit courses')
                        <a href="{{ route('tests.show', $test) }}">Редактировать тест</a><br>
                        <a href="{{ route('tests.results', $test) }}" class="btn btn-info">Обзор теста</a><br>
                    @endcan
                    @auth
                        @php
                            $remainingForThisTest = $remainingByTest[$test->id] ?? null;
                        @endphp
                        @if($remainingForThisTest !== null)
                            <span>
                                попытки: {{ $remainingForThisTest }}
                            </span>
                        @endif
                    @endauth
                    <p class="text-gray-600 flex-grow">
                        Доступен с {{ $test->formattedPeriodStart() ?? '—' }}
                    </p>
                    <p class="text-gray-600 flex-grow">
                        Доступен до {{ $test->formattedPeriodEnd() ?? '—' }}
                    </p>
                    @php
                        $displayMode = $test->display_mode ?? 'per_question';
                    @endphp
                    @if($displayMode === 'single_page')
                        <a href="{{ route('tests.attempt', $test) }}">пройти тест</a>
                    @else
                        <a href="{{ route('tests.attempt.page', [$test->id, 1]) }}">пройти тест</a>
                    @endif
                </li>
            @empty
                <li>Тестов пока нет</li>
            @endforelse
        </ul>

        <h2>Лекции курса</h2>
        <ul>
            @forelse($course->lectures as $lecture)
                <li>
                    @hasanyrole('teacher|admin')
                        @if(($lecture->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED)
                            <span class="text-yellow-700 font-medium">[архивирован]</span>
                        @endif
                    @endhasanyrole
                    <a href="{{ route('lectures.show', ['course' => $course, 'lecture' => $lecture]) }}">
                        {{ $lecture->title }}
                    </a>
                </li>
            @empty
                <li>Лекций пока нет</li>
            @endforelse
        </ul>

        <h2>Материалы курса</h2>
        <ul>
            @forelse($course->materials->where('status', \App\Models\Material::STATUS_ACTIVE) as $material)
                <li class="mb-2">
                    <strong>📎 {{ $material->title }}</strong>
                    <a href="{{ route('materials.download', ['course' => $course, 'material' => $material]) }}" class="btn btn-sm" style="margin-left: 10px;">
                        ⬇ Скачать ({{ strtoupper($material->file_type) }})
                    </a>
                    @hasanyrole('teacher|admin')
                        <div class="mt-1 flex gap-2">
                            <form action="{{ route('admin.materials.archive', $material) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-xs">Архивировать</button>
                            </form>
                            <form action="{{ route('materials.destroy', [$course, $material]) }}" method="POST" class="inline-block" onsubmit="return confirm('Удалить материал?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Удалить</button>
                            </form>
                        </div>
                    @endhasanyrole
                </li>
            @empty
                <li>Материалов пока нет</li>
            @endforelse
            {{-- Архивированные материалы для учителей --}}
            @hasanyrole('teacher|admin')
                @forelse($course->materials->where('status', \App\Models\Material::STATUS_ARCHIVED) as $material)
                    <li class="mb-2">
                        <strong>📎 {{ $material->title }}</strong>
                        <span class="text-yellow-700 font-medium">[архивирован]</span>
                        <a href="{{ route('materials.download', ['course' => $course, 'material' => $material]) }}" class="btn btn-sm" style="margin-left: 10px;">
                            ⬇ Скачать ({{ strtoupper($material->file_type) }})
                        </a>
                        <div class="mt-1 flex gap-2">
                            <form action="{{ route('admin.materials.restore', $material) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-xs">Восстановить</button>
                            </form>
                            <form action="{{ route('materials.destroy', [$course, $material]) }}" method="POST" class="inline-block" onsubmit="return confirm('Удалить материал?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Удалить</button>
                            </form>
                        </div>
                    </li>
                @empty
                @endforelse
            @endhasanyrole
        </ul>

        <h2>Задания курса</h2>
        <ul>
            @forelse($course->assignments->where('status', \App\Models\Assignment::STATUS_ACTIVE) as $assignment)
                <li class="mb-2">
                    <strong>📝 {{ $assignment->title }}</strong>
                    @if($assignment->due_date)
                        <br><small>
                            Срок: {{ $assignment->due_date->format('d.m.Y H:i') }}
                            @if($assignment->isOverdue())
                                <strong style="color: #dc3545;">(Сроки истекли)</strong>
                            @endif
                        </small>
                    @endif
                    <a href="{{ route('assignments.view', ['course' => $course, 'assignment' => $assignment]) }}" class="btn btn-sm" style="margin-left: 10px;">
                        → Перейти к заданию
                    </a>
                    @hasanyrole('teacher|admin')
                        <div class="mt-1 flex gap-2">
                            <a href="{{ route('assignments.show', [$course, $assignment]) }}" class="btn btn-secondary btn-xs">Управл. ответами</a>
                            <form action="{{ route('assignments.destroy', [$course, $assignment]) }}" method="POST" class="inline-block" onsubmit="return confirm('Удалить задание?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Удалить</button>
                            </form>
                        </div>
                    @endhasanyrole
                </li>
            @empty
                <li>Заданий пока нет</li>
            @endforelse

            {{-- Архивированные задания для учителей --}}
            @hasanyrole('teacher|admin')
                @forelse($course->assignments->where('status', \App\Models\Assignment::STATUS_ARCHIVED) as $assignment)
                    <li class="mb-2">
                        <strong>📝 {{ $assignment->title }}</strong>
                        <span class="text-yellow-700 font-medium">[архивировано]</span>
                        @if($assignment->due_date)
                            <br><small>Срок: {{ $assignment->due_date->format('d.m.Y H:i') }}</small>
                        @endif
                        <a href="{{ route('assignments.view', ['course' => $course, 'assignment' => $assignment]) }}" class="btn btn-sm" style="margin-left: 10px;">
                            → Узнать больше
                        </a>
                        <div class="mt-1 flex gap-2">
                            <a href="{{ route('assignments.edit', [$course, $assignment]) }}" class="btn btn-secondary btn-xs">Изменить</a>
                            <form action="{{ route('assignments.destroy', [$course, $assignment]) }}" method="POST" class="inline-block" onsubmit="return confirm('Удалить задание?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Удалить</button>
                            </form>
                        </div>
                    </li>
                @empty
                @endforelse
            @endhasanyrole
        </ul>
    @endif
</div>
</body>

</html>
