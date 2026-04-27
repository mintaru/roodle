<div>
    {{-- Success and Error Messages --}}
    @if($successMessage)
        <div style="padding: 12px 16px; background: var(--green-50); color: var(--green-600); border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid var(--green-200); font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $successMessage }}</span>
            <button @click="$wire.set('successMessage', '')" style="background: none; border: none; cursor: pointer; font-size: 16px;">✕</button>
        </div>
    @endif

    @if($errorMessage)
        <div style="padding: 12px 16px; background: #ffebee; color: var(--red-500); border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid #ffcdd2; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $errorMessage }}</span>
            <button @click="$wire.set('errorMessage', '')" style="background: none; border: none; cursor: pointer; font-size: 16px;">✕</button>
        </div>
    @endif

    {{-- Add New Section --}}
    @hasanyrole('teacher|admin')
        <div style="margin: 24px 0; padding: 16px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">
            <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--gray-800);">Добавить новую секцию</h2>
            <form @submit.prevent="$wire.addSection()" style="display: flex; gap: 8px;">
                <input type="text" wire:model="newSectionTitle" placeholder="Название секции" required style="flex: 1; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 13px;">
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">Создать</button>
            </form>
        </div>
    @endhasanyrole

    {{-- Sections List --}}
    @forelse($sections as $section)
        <div style="margin-bottom: 24px; padding: 16px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">
            {{-- Section Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                    @hasanyrole('teacher|admin')
                        @if($editingSectionId === $section->id)
                            <form @submit.prevent="$wire.updateSection()" style="display: flex; gap: 8px; flex: 1;">
                                <input type="text" wire:model="editingSectionTitle" style="flex: 1; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 14px; font-weight: 600;">
                                <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Сохранить</button>
                                <button type="button" @click="$wire.cancelEdit()" class="btn btn-ghost" style="padding: 6px 12px; font-size: 12px;">Отменить</button>
                            </form>
                        @else
                            <h2 style="font-size: 16px; font-weight: 700; color: var(--gray-800); cursor: pointer; flex: 1;" @click="$wire.editSection({{ $section->id }})">{{ $section->title }}</h2>
                        @endif
                    @else
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--gray-800);">{{ $section->title }}</h2>
                    @endhasanyrole
                </div>
                @hasanyrole('teacher|admin')
                    <div style="display: flex; gap: 6px;">
                        <button wire:click="moveSection({{ $section->id }}, 'up')" class="btn btn-ghost" style="padding: 6px 10px; font-size: 12px;" title="Сдвинуть вверх">↑</button>
                        <button wire:click="moveSection({{ $section->id }}, 'down')" class="btn btn-ghost" style="padding: 6px 10px; font-size: 12px;" title="Сдвинуть вниз">↓</button>
                        <button wire:click="deleteSection({{ $section->id }})" onclick="return confirm('Удалить секцию?')" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;">Удалить</button>
                    </div>
                @endhasanyrole
            </div>

            {{-- Section Items --}}
            <div style="margin-bottom: 16px;">
                @php
                    $items = $section->items()->orderBy('position')->get();
                @endphp
                @forelse($items as $sectionItem)
                    @php
                        $item = $sectionItem->item;
                        $isArchived = ($item instanceof \App\Models\Test && ($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
                            || ($item instanceof \App\Models\Lecture && ($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED);
                        
                        $periodEndPassed = false;
                        if ($item->period_end && \Carbon\Carbon::parse($item->period_end)->isPast()) {
                            $periodEndPassed = true;
                        }
                    @endphp
                    @if(($isArchived || $periodEndPassed) && !auth()->user()?->hasAnyRole(['teacher','admin']))
                        @continue
                    @endif
                    <div style="padding: 12px; background: var(--color-surface-2); border: 1px solid var(--color-border); border-radius: var(--r-md); margin-bottom: 8px; display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            @if($item instanceof \App\Models\Test)
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    <strong style="color: var(--teal-600); font-size: 12px; text-transform: uppercase;">Тест</strong>
                                    @if(($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
                                        <span style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирован]</span>
                                    @endif
                                </div>
                                <a href="{{ route('tests.view', $item) }}" style="color: var(--teal-600); text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->title }}</a>
                                <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0;">Доступен с {{ $item->formattedPeriodStart() ?? '—' }} до {{ $item->formattedPeriodEnd() ?? '—' }}</p>
                                @can('edit courses')
                                    <div style="margin-top: 6px;">
                                        <a href="{{ route('tests.show', $item) }}" style="font-size: 12px; color: var(--teal-600); text-decoration: none; margin-right: 12px;">Редактировать</a>
                                        <a href="{{ route('tests.results', $item) }}" style="font-size: 12px; color: var(--teal-600); text-decoration: none;">Обзор</a>
                                    </div>
                                @endcan
                            @elseif($item instanceof \App\Models\Lecture)
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    <strong style="color: var(--sky-600); font-size: 12px; text-transform: uppercase;">Лекция</strong>
                                    @if(($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED)
                                        <span style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирована]</span>
                                    @endif
                                </div>
                                <a href="{{ route('lectures.show', ['course' => $course, 'lecture' => $item]) }}" style="color: var(--sky-600); text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->title }}</a>
                            @elseif($item instanceof \App\Models\Material)
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    <strong style="color: var(--green-600); font-size: 12px; text-transform: uppercase;">📎 Материал</strong>
                                    @if(($item->status ?? 'active') === \App\Models\Material::STATUS_ARCHIVED)
                                        <span style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирован]</span>
                                    @endif
                                </div>
                                <p style="font-weight: 600; font-size: 14px; color: var(--gray-800); margin: 0;">{{ $item->title }}</p>
                                <a href="{{ route('materials.download', ['course' => $course, 'material' => $item]) }}" style="display: inline-block; margin-top: 6px; font-size: 12px; color: var(--green-600); text-decoration: none;">⬇ Скачать</a>
                            @endif
                        </div>
                        @hasanyrole('teacher|admin')
                            <div style="display: flex; gap: 4px; flex-shrink: 0; margin-left: 12px;">
                                <button wire:click="moveItem({{ $sectionItem->id }}, 'up')" class="btn btn-ghost" style="padding: 4px 8px; font-size: 11px;">↑</button>
                                <button wire:click="moveItem({{ $sectionItem->id }}, 'down')" class="btn btn-ghost" style="padding: 4px 8px; font-size: 11px;">↓</button>
                                <button wire:click="detachItem({{ $sectionItem->id }})" onclick="return confirm('Убрать элемент?')" class="btn btn-danger" style="padding: 4px 8px; font-size: 11px;">Убрать</button>
                            </div>
                        @endhasanyrole
                    </div>
                @empty
                    <div style="padding: 12px; text-align: center; color: var(--color-text-muted); font-size: 13px;">
                        В этой секции пока нет элементов
                    </div>
                @endforelse
            </div>

            {{-- Attach Items --}}
            @hasanyrole('teacher|admin')
                <div style="border-top: 1px solid var(--color-border); padding-top: 12px;">
                    <p style="font-size: 12px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Добавить элементы:</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        {{-- Add Test --}}
                        <select @change="$wire.attachItem({{ $section->id }}, 'test', $event.target.value)" style="padding: 6px 8px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 12px;">
                            <option value="">Выберите тест...</option>
                            @php
                                $addedTestIds = $section->items()->where('item_type', \App\Models\Test::class)->pluck('item_id')->toArray();
                            @endphp
                            @foreach($course->tests->where('status', \App\Models\Test::STATUS_ACTIVE) as $test)
                                @if(!in_array($test->id, $addedTestIds))
                                    <option value="{{ $test->id }}">{{ $test->title }}</option>
                                @endif
                            @endforeach
                        </select>

                        {{-- Add Lecture --}}
                        <select @change="$wire.attachItem({{ $section->id }}, 'lecture', $event.target.value)" style="padding: 6px 8px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 12px;">
                            <option value="">Выберите лекцию...</option>
                            @php
                                $addedLectureIds = $section->items()->where('item_type', \App\Models\Lecture::class)->pluck('item_id')->toArray();
                            @endphp
                            @foreach($course->lectures->where('status', \App\Models\Lecture::STATUS_ACTIVE) as $lecture)
                                @if(!in_array($lecture->id, $addedLectureIds))
                                    <option value="{{ $lecture->id }}">{{ $lecture->title }}</option>
                                @endif
                            @endforeach
                        </select>

                        {{-- Add Material --}}
                        <select @change="$wire.attachItem({{ $section->id }}, 'material', $event.target.value)" style="padding: 6px 8px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 12px;">
                            <option value="">Выберите материал...</option>
                            @php
                                $addedMaterialIds = $section->items()->where('item_type', \App\Models\Material::class)->pluck('item_id')->toArray();
                            @endphp
                            @foreach($course->materials->where('status', \App\Models\Material::STATUS_ACTIVE) as $material)
                                @if(!in_array($material->id, $addedMaterialIds))
                                    <option value="{{ $material->id }}">{{ $material->title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            @endhasanyrole
        </div>
    @empty
        <div style="padding: 24px; text-align: center; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">
            <p style="color: var(--color-text-muted); font-size: 14px;">Секции еще не созданы. Создайте первую секцию для организации контента курса.</p>
        </div>
    @endforelse
</div>
