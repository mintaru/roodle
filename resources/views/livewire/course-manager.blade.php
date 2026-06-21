<div x-data="{ search: '' }"
    @confirm-delete-section.window="Livewire.find('{{ $this->getId() }}').deleteSection($event.detail.id)"
    @confirm-detach-item.window="Livewire.find('{{ $this->getId() }}').detachItem($event.detail.id)">

    {{-- Success and Error Messages --}}
    @if ($successMessage)
        <div
            style="padding: 12px 16px; background: var(--green-50); color: var(--green-600); border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid var(--green-200); font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $successMessage }}</span>
            <button @click="$wire.set('successMessage', '')"
                style="background: none; border: none; cursor: pointer; font-size: 16px;">✕</button>
        </div>
    @endif

    @if ($errorMessage)
        <div
            style="padding: 12px 16px; background: #ffebee; color: var(--red-500); border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid #ffcdd2; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $errorMessage }}</span>
            <button @click="$wire.set('errorMessage', '')"
                style="background: none; border: none; cursor: pointer; font-size: 16px;">✕</button>
        </div>
    @endif

    {{-- Search --}}
    <div style="margin-bottom: 16px; position: relative; display: flex; gap: 8px; align-items: center;">
        <div style="position: relative; flex: 1;">
            <span
                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); font-size: 14px; pointer-events: none;">🔍</span>
            <input x-model="search" type="text" placeholder="Поиск по тестам, лекциям, материалам..."
                style="width: 100%; padding: 8px 36px 8px 32px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 13px; box-sizing: border-box; background: var(--color-surface);">
            <button x-show="search !== ''" @click="search = ''"
                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 14px; color: var(--color-text-muted); line-height: 1;">✕</button>
        </div>
        @if ($canManage)
            <button type="button" onclick="openCreateModal()"
                style="width: 36px; height: 36px; flex-shrink: 0; background: var(--teal-500); color: #fff; border: none; border-radius: var(--r-sm); font-size: 20px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-accent);"
                title="Создать">+</button>
        @endif
    </div>



    {{-- Sections List --}}
    @forelse($sections as $section)
        {{-- Фильтр видимости секции для студентов --}}
        @if (!$isTeacherOrAdmin)
            @php
                $sectionAllowedGroupIds = $section->visibleGroups->pluck('id')->toArray();
            @endphp
            @if (empty($sectionAllowedGroupIds) || empty(array_intersect($userGroupIds, $sectionAllowedGroupIds)))
                @continue
            @endif
        @endif

        @php $sectionItems = $section->items()->orderBy('position')->get(); @endphp
        <div wire:key="section-{{ $section->id }}" x-data="{ open: true }" x-effect="if (search !== '') open = true"
            x-show="search === '' || Array.from($el.querySelectorAll('[data-search-title]')).some(el => el.dataset.searchTitle.includes(search.toLowerCase().trim()))"
            style="margin-bottom: 24px; padding: 16px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                    @if ($canManage)
                        @if ($editingSectionId === $section->id)
                            <form @submit.prevent="$wire.updateSection()" style="display: flex; gap: 8px; flex: 1;">
                                <input type="text" wire:model="editingSectionTitle"
                                    style="flex: 1; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 14px; font-weight: 600;">
                                <button type="submit" class="btn btn-primary"
                                    style="padding: 6px 12px; font-size: 12px;">Сохранить</button>
                                <button type="button" @click="$wire.cancelEdit()" class="btn btn-ghost"
                                    style="padding: 6px 12px; font-size: 12px;">Отменить</button>
                            </form>
                        @else
                            <h2 style="font-size: 16px; font-weight: 700; color: var(--gray-800); cursor: pointer; flex: 1;"
                                @click="$wire.editSection({{ $section->id }})">{{ $section->title }}</h2>
                        @endif
                    @else
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--gray-800);">{{ $section->title }}
                        </h2>
                    @endif
                </div>
                <div style="display: flex; gap: 6px; align-items: center;">
                    <button @click="open = !open" class="btn btn-ghost" style="padding: 6px 10px; font-size: 12px;"
                        :title="open ? 'Свернуть' : 'Развернуть'">
                        <span x-text="open ? '▲' : '▼'"></span>
                    </button>
                    @if ($canManage)
                        <button wire:click="moveSection({{ $section->id }}, 'up')" class="btn btn-ghost"
                            style="padding: 6px 10px; font-size: 12px;" title="Сдвинуть вверх">↑</button>
                        <button wire:click="moveSection({{ $section->id }}, 'down')" class="btn btn-ghost"
                            style="padding: 6px 10px; font-size: 12px;" title="Сдвинуть вниз">↓</button>
                        <button type="button" onclick="openAttachModal({{ $section->id }})" class="btn btn-ghost"
                            style="padding: 6px 10px; font-size: 16px; line-height: 1; color: var(--teal-600); border-color: var(--teal-200);"
                            title="Добавить элемент">+</button>
                        {{-- ★ НОВАЯ КНОПКА: настройки видимости секции --}}
                        <button type="button" wire:click="openSectionVisibility({{ $section->id }})"
                            class="btn btn-ghost"
                            style="padding: 6px 10px; font-size: 13px; color: var(--sky-600); border-color: var(--sky-200);"
                            title="Настройки видимости">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                        <button type="button" x-data
                            @click="$dispatch('open-delete-section-modal', { id: {{ $section->id }} })"
                            class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;">Удалить</button>
                    @endif
                </div>
            </div>

            <div x-show="$data.open" x-transition.duration.150ms>
                <div>
                    @forelse($sectionItems as $sectionItem)
                        @php
                            $item = $sectionItem->item;
                        @endphp
                        @if (!$item)
                            @continue
                        @endif
                        @php
                            $isArchived =
                                ($item instanceof \App\Models\Test &&
                                    ($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED) ||
                                ($item instanceof \App\Models\Lecture &&
                                    ($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED);
                            $periodEndPassed = $item->period_end && \Carbon\Carbon::parse($item->period_end)->isPast();
                        @endphp
                        @if (
                            ($isArchived || $periodEndPassed) &&
                                !auth()->user()
                                    ?->hasAnyRole(['teacher', 'admin']))
                            @continue
                        @endif

                        @if (!$isTeacherOrAdmin)
                            @php $itemAllowedGroupIds = $sectionItem->visibleGroups->pluck('id')->toArray(); @endphp
                            @if (!empty($itemAllowedGroupIds) && empty(array_intersect($userGroupIds, $itemAllowedGroupIds)))
                                @continue
                            @endif
                            {{-- Если $itemAllowedGroupIds пустой — элемент наследует доступ от секции --}}
                        @endif

                        <div wire:key="section-item-{{ $sectionItem->id }}"
                            data-search-title="{{ strtolower($item->title) }}"
                            x-show="search === '' || '{{ strtolower(addslashes($item->title)) }}'.includes(search.toLowerCase().trim())"
                            style="padding: 12px; background: var(--color-surface-2); border: 1px solid var(--color-border); border-radius: var(--r-md); margin-bottom: 8px;">

                            @if ($canManage)
                                @php $menuId = 'menu-' . $sectionItem->id; @endphp
                            @endif

                            @if ($item instanceof \App\Models\Test)
                                <div x-data="{ open: false }" @click.outside="open = false"
                                    style="display: flex; align-items: center; gap: 14px; position: relative;">
                                    <div style="display: flex; align-items: center; flex-shrink: 0;">
                                        <img src="{{ asset('storage/icons/test.png') }}" alt="Тест"
                                            style="width: 36px; height: 36px;">
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                            <strong
                                                style="color: var(--teal-600); font-size: 12px; text-transform: uppercase;">Тест</strong>
                                            @if (($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
                                                <span
                                                    style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирован]</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('tests.view', $item) }}"
                                            style="color: var(--teal-600); text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->title }}</a>
                                        @if ($item->formattedPeriodEnd() != null)
                                            <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0;">
                                                Доступен с {{ $item->formattedPeriodStart() ?? '—' }} до
                                                {{ $item->formattedPeriodEnd() ?? '—' }}
                                            </p>
                                        @endif

                                    </div>
                                    @if ($canManage)
                                        <div style="flex-shrink: 0; position: relative;">
                                            <button type="button" @click="open = !open" class="btn btn-ghost"
                                                style="padding: 4px 10px; font-size: 18px; line-height: 1; color: var(--color-text-muted); border-color: transparent;">⋯</button>
                                            @include('livewire.partials.item-menu', [
                                                'item' => $item,
                                                'sectionItem' => $sectionItem,
                                                'course' => $course,
                                                'type' => 'test',
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @elseif($item instanceof \App\Models\Lecture)
                                <div x-data="{ open: false }" @click.outside="open = false"
                                    style="display: flex; align-items: center; gap: 14px; position: relative;">
                                    <div style="display: flex; align-items: center; flex-shrink: 0;">
                                        <img src="{{ asset('storage/icons/lecture.svg') }}" alt="Лекция"
                                            style="width: 36px; height: 36px;">
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                            <strong
                                                style="color: var(--sky-600); font-size: 12px; text-transform: uppercase;">Лекция</strong>
                                            @if (($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED)
                                                <span
                                                    style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирована]</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('lectures.show', ['course' => $course, 'lecture' => $item]) }}"
                                            style="color: var(--sky-600); text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->title }}</a>
                                    </div>
                                    @if ($canManage)
                                        <div style="flex-shrink: 0; position: relative;">
                                            <button type="button" @click="open = !open" class="btn btn-ghost"
                                                style="padding: 4px 10px; font-size: 18px; line-height: 1; color: var(--color-text-muted); border-color: transparent;">⋯</button>
                                            @include('livewire.partials.item-menu', [
                                                'item' => $item,
                                                'sectionItem' => $sectionItem,
                                                'course' => $course,
                                                'type' => 'lecture',
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @elseif($item instanceof \App\Models\Material)
                                <div x-data="{ open: false }" @click.outside="open = false"
                                    style="display: flex; align-items: center; gap: 14px; position: relative;">
                                    <div style="display: flex; align-items: center; flex-shrink: 0;">
                                        <img src="{{ asset('storage/icons/material.svg') }}" alt="Материал"
                                            style="width: 36px; height: 36px;">
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                            <strong
                                                style="color: var(--green-600); font-size: 12px; text-transform: uppercase;">Материал</strong>
                                            @if (($item->status ?? 'active') === \App\Models\Material::STATUS_ARCHIVED)
                                                <span
                                                    style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирован]</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('materials.download', ['course' => $course, 'material' => $item]) }}"
                                            style="font-weight: 600; font-size: 14px; color: var(--gray-800); text-decoration: none; margin: 0; display: block;">
                                            {{ $item->title }}
                                        </a>
                                    </div>
                                    @if ($canManage)
                                        <div style="flex-shrink: 0; position: relative;">
                                            <button type="button" @click="open = !open" class="btn btn-ghost"
                                                style="padding: 4px 10px; font-size: 18px; line-height: 1; color: var(--color-text-muted); border-color: transparent;">⋯</button>
                                            @include('livewire.partials.item-menu', [
                                                'item' => $item,
                                                'sectionItem' => $sectionItem,
                                                'course' => $course,
                                                'type' => 'material',
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @elseif($item instanceof \App\Models\Assignment)
                                <div x-data="{ open: false }" @click.outside="open = false"
                                    style="display: flex; align-items: center; gap: 14px; position: relative;">
                                    <div style="display: flex; align-items: center; flex-shrink: 0;">
                                        <img src="{{ asset('storage/icons/assignment.svg') }}" alt="Задание"
                                            style="width: 36px; height: 36px;">
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                            <strong
                                                style="color: var(--amber-600); font-size: 12px; text-transform: uppercase;">Задание</strong>
                                            @if (($item->status ?? 'active') === \App\Models\Assignment::STATUS_ARCHIVED)
                                                <span
                                                    style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивировано]</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('assignments.view', ['course' => $course, 'assignment' => $item]) }}"
                                            style="color: var(--amber-600); text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->title }}</a>
                                        @if ($item->due_date)
                                            <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0;">
                                                Срок сдачи: {{ $item->due_date->format('d.m.Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                    @if ($canManage)
                                        <div style="flex-shrink: 0; position: relative;">
                                            <button type="button" @click="open = !open" class="btn btn-ghost"
                                                style="padding: 4px 10px; font-size: 18px; line-height: 1; color: var(--color-text-muted); border-color: transparent;">⋯</button>
                                            @include('livewire.partials.item-menu', [
                                                'item' => $item,
                                                'sectionItem' => $sectionItem,
                                                'course' => $course,
                                                'type' => 'assignment',
                                            ])
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div
                            style="padding: 12px; text-align: center; color: var(--color-text-muted); font-size: 13px;">
                            В этой секции пока нет элементов
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @empty
        <div
            style="padding: 32px 24px; text-align: center; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">
            <p style="color: var(--color-text-muted); font-size: 14px; margin-bottom: 16px;">Секции еще не созданы.
                Создайте первую секцию для организации контента курса.</p>
            @if ($canManage)
                <button type="button" onclick="openCreateModalAtSection()"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: var(--teal-500); color: #fff; border: none; border-radius: var(--r-full); font-family: var(--font-body); font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: var(--shadow-accent);">
                    <span style="font-size: 18px; line-height: 1;">+</span> Создать секцию
                </button>
            @endif
        </div>
    @endforelse

    {{-- Модалка удаления секции --}}
    <div x-data="{ open: false, sectionId: null }" @open-delete-section-modal.window="open = true; sectionId = $event.detail.id"
        @keydown.escape.window="open = false" x-cloak x-show="open" x-transition:enter.duration.160ms
        x-transition:leave.duration.120ms class="modal-alpine-overlay"
        style="--tw-enter-opacity: 0; --tw-leave-opacity: 0;"
        :style="open ? 'animation: overlay-in 160ms ease forwards;' : 'animation: overlay-out 120ms ease forwards;'">
        <div class="modal-alpine-backdrop" @click="open = false"></div>
        <div class="modal-alpine-box"
            :style="open ? 'animation: modal-in 160ms ease forwards;' : 'animation: modal-out 120ms ease forwards;'">
            <div
                style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem 1rem; border-bottom:1px solid var(--color-border);">
                <div>
                    <div class="modal__title">Удалить секцию?</div>
                    <div class="modal__subtitle">Это действие нельзя отменить</div>
                </div>
                <button @click="open = false" class="modal__close">×</button>
            </div>
            <div class="modal__body">
                <div
                    style="width:48px; height:48px; border-radius:var(--r-md); background:#ffebee; color:var(--red-500); display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                        <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" />
                    </svg>
                </div>
                <p style="font-size:14px; color:var(--color-text-muted); line-height:1.5;">
                    Секция и все её элементы будут удалены без возможности восстановления.
                </p>
            </div>
            <div class="modal__footer">
                <button @click="open = false" class="btn-ghost">Отмена</button>
                <button @click="$dispatch('confirm-delete-section', { id: sectionId }); open = false"
                    class="btn-primary btn-danger">Удалить</button>
            </div>
        </div>
    </div>

    {{-- Модалка открепления элемента --}}
    <div x-data="{ open: false, itemId: null }" @open-detach-item-modal.window="open = true; itemId = $event.detail.id"
        @keydown.escape.window="open = false" x-cloak x-show="open" x-transition:enter.duration.160ms
        x-transition:leave.duration.120ms class="modal-alpine-overlay"
        :style="open ? 'animation: overlay-in 160ms ease forwards;' : 'animation: overlay-out 120ms ease forwards;'">
        <div class="modal-alpine-backdrop" @click="open = false"></div>
        <div class="modal-alpine-box"
            :style="open ? 'animation: modal-in 160ms ease forwards;' : 'animation: modal-out 120ms ease forwards;'">
            <div
                style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem 1rem; border-bottom:1px solid var(--color-border);">
                <div>
                    <div class="modal__title">Убрать элемент?</div>
                    <div class="modal__subtitle">Элемент будет откреплён от секции</div>
                </div>
                <button @click="open = false" class="modal__close">×</button>
            </div>
            <div class="modal__body">
                <div
                    style="width:48px; height:48px; border-radius:var(--r-md); background:var(--teal-50); color:var(--teal-600); display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </div>
                <p style="font-size:14px; color:var(--color-text-muted); line-height:1.5;">
                    Элемент будет откреплён от секции, но останется в системе.
                </p>
            </div>
            <div class="modal__footer">
                <button @click="open = false" class="btn-ghost">Отмена</button>
                <button @click="$dispatch('confirm-detach-item', { id: itemId }); open = false"
                    class="btn-primary btn-danger">Убрать</button>
            </div>
        </div>
    </div>

    {{-- ============================================================
         ★ МОДАЛКА НАСТРОЙКИ ВИДИМОСТИ ПО ГРУППАМ
         Работает и для секций, и для отдельных элементов.
         Открывается через wire:click="openSectionVisibility(id)"
         или wire:click="openItemVisibility(id)"
         ============================================================ --}}
    @if ($canManage)
        <div x-data="{ open: false, label: '' }"
            @open-visibility-modal.window="
                open = true;
                label = ($wire.visibilityType === 'section') ? 'секции' : 'элемента'
            "
            @close-visibility-modal.window="open = false" @keydown.escape.window="if(open) open = false" x-cloak
            x-show="open" x-transition:enter.duration.160ms x-transition:leave.duration.120ms
            class="modal-alpine-overlay"
            :style="open
                ?
                'animation: overlay-in 160ms ease forwards;' :
                'animation: overlay-out 120ms ease forwards;'">

            <div class="modal-alpine-backdrop" @click="open = false"></div>

            <div class="modal-alpine-box"
                :style="open
                    ?
                    'animation: modal-in 160ms ease forwards;' :
                    'animation: modal-out 120ms ease forwards;'">

                {{-- Заголовок --}}
                <div
                    style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem 1rem; border-bottom:1px solid var(--color-border);">
                    <div>
                        <div class="modal__title">Доступ по группам</div>
                        <div class="modal__subtitle" x-text="'Настройка видимости ' + label"></div>
                    </div>
                    <button @click="open = false" class="modal__close">×</button>
                </div>

                <div class="modal__body">
                    {{-- Иконка --}}
                    <div
                        style="width:48px; height:48px; border-radius:var(--r-md); background:var(--sky-50); color:var(--sky-600); display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>

                    <p style="font-size:13px; color:var(--color-text-muted); line-height:1.5; margin-bottom:14px;">
                        Отмеченные группы увидят этот элемент. Без отметок — скрыто от всех студентов.
                    </p>

                    {{-- Быстрые действия --}}
                    <div style="display:flex; gap:8px; margin-bottom:14px;">
                        <button type="button"
                            wire:click="$set('visibilityGroupIds', {{ $course->groups->pluck('id')->map(fn($id) => (string) $id)->toJson() }})"
                            class="btn btn-ghost"
                            style="font-size:12px; padding:5px 12px; color:var(--green-600); border-color:var(--green-200);">
                            ✓ Разрешить всем
                        </button>
                        <button type="button" wire:click="$set('visibilityGroupIds', [])" class="btn btn-ghost"
                            style="font-size:12px; padding:5px 12px; color:var(--red-500); border-color:#ffcdd2;">
                            ✕ Запретить всем
                        </button>
                    </div>

                    {{-- Список групп курса с чекбоксами --}}
                    <div style="display:flex; flex-direction:column; gap:6px; max-height:280px; overflow-y:auto;">
                        @forelse($course->groups as $group)
                            <label
                                style="display:flex; align-items:center; gap:10px; padding:9px 12px; border:1.5px solid var(--color-border); border-radius:var(--r-md); background:var(--color-surface-2); cursor:pointer; font-size:13px; color:var(--gray-700); transition: border-color .12s, background .12s; user-select:none;"
                                :style="$wire.visibilityGroupIds.includes('{{ $group->id }}') ?
                                    'border-color: var(--sky-400); background: var(--sky-50);' :
                                    ''">
                                <input type="checkbox" wire:model="visibilityGroupIds" value="{{ $group->id }}"
                                    style="width:16px; height:16px; accent-color:var(--sky-500); cursor:pointer; flex-shrink:0;">
                                <span style="font-weight:500;">{{ $group->name }}</span>
                            </label>
                        @empty
                            <div
                                style="padding:20px; text-align:center; color:var(--color-text-muted); font-size:13px; border:1px dashed var(--color-border); border-radius:var(--r-md);">
                                К курсу не привязано ни одной группы.<br>
                                <span style="font-size:12px;">Добавьте группы к курсу, чтобы управлять доступом.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Футер --}}
                <div class="modal__footer">
                    <button @click="open = false" class="btn-ghost">Отмена</button>
                    <button wire:click="saveVisibility" @click="open = false" class="btn-primary"
                        style="background:var(--sky-500); border-color:var(--sky-500);">
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: добавить элемент — только для учителей с правом редактирования --}}
    @if ($canManage)
        <div>
            <div id="create-modal-overlay" onclick="if(event.target===this) closeCreateModal()"
                style="display:none; position:fixed; inset:0; background:rgba(17,23,32,.45); backdrop-filter:blur(2px); z-index:1000; align-items:center; justify-content:center; padding:1.5rem;">
                <div
                    style="background:var(--color-surface); border-radius:var(--r-xl); box-shadow:var(--shadow-lg); width:100%; max-width:480px; max-height:calc(100vh - 3rem); overflow-y:auto; animation: modal-in .16s ease;">

                    {{-- Заголовок --}}
                    <div
                        style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem 1rem; border-bottom:1px solid var(--color-border);">
                        <div>
                            <div style="font-size:16px; font-weight:700; color:var(--gray-800);"
                                id="create-modal-title">
                                Создать</div>
                            <div style="font-size:13px; color:var(--color-text-muted); margin-top:2px;"
                                id="create-modal-subtitle">Выберите тип</div>
                        </div>
                        <button onclick="closeCreateModal()"
                            style="width:28px; height:28px; border:none; background:var(--gray-100); color:var(--gray-500); border-radius:var(--r-sm); cursor:pointer; font-size:17px; line-height:1; display:flex; align-items:center; justify-content:center;">×</button>
                    </div>

                    <div style="padding:1.25rem 1.5rem;">

                        {{-- ШАГ 1: выбор типа --}}
                        <div id="create-step-type">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:1.25rem;">

                                <button type="button" onclick="selectCreateType('section')"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--gray-100); color:var(--gray-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📁</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Секция</div>
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                        Группировка контента</div>
                                </button>

                                <a href="{{ route('tests.create', $course) }}"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center; text-decoration:none; display:block;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--teal-50); color:var(--teal-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📝</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Тест</div>
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">Создать
                                        новый тест</div>
                                </a>

                                <a href="{{ route('lectures.create', $course) }}"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center; text-decoration:none; display:block;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--sky-50); color:var(--sky-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📖</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Лекция</div>
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">Создать
                                        новую лекцию</div>
                                </a>

                                <a href="{{ route('materials.create', $course) }}"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center; text-decoration:none; display:block;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--green-50); color:var(--green-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📎</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Материал</div>
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                        Загрузить файл</div>
                                </a>

                                <a href="{{ route('assignments.create', $course) }}"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center; text-decoration:none; display:block;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--amber-50); color:var(--amber-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📋</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Задание</div>
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">Создать
                                        задание</div>
                                </a>



                            </div>
                        </div>

                        {{-- ШАГ 2: создать секцию --}}
                        <div id="create-step-section" style="display:none;">
                            <button type="button" onclick="backToCreateType()"
                                style="background:none; border:none; cursor:pointer; font-size:12px; font-weight:600; color:var(--color-text-muted); padding:0; margin-bottom:14px; display:flex; align-items:center; gap:4px;">←
                                Назад</button>
                            <div style="margin-bottom:12px;">
                                <label
                                    style="display:block; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--gray-500); margin-bottom:5px;">Название
                                    секции</label>
                                <input type="text" id="create-section-input" placeholder="Введите название секции"
                                    style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:14px; font-family:var(--font-body); background:var(--color-surface);"
                                    onkeydown="if(event.key==='Enter'){event.preventDefault();doCreateSection();}">
                            </div>
                        </div>

                    </div>

                    {{-- Футер --}}
                    <div style="padding:1rem 1.5rem; border-top:1px solid var(--color-border); display:flex; align-items:center; justify-content:flex-end; gap:8px;"
                        id="create-modal-footer">
                        <button type="button" onclick="closeCreateModal()"
                            style="display:inline-flex; align-items:center; padding:8px 16px; background:transparent; color:var(--color-text-secondary); border:1px solid var(--color-border); border-radius:var(--r-full); font-family:var(--font-body); font-size:14px; font-weight:600; cursor:pointer;">Отмена</button>
                        <button type="button" id="create-section-confirm-btn" onclick="doCreateSection()"
                            style="display:none; padding:9px 20px; background:var(--teal-500); color:#fff; border:none; border-radius:var(--r-full); font-family:var(--font-body); font-size:14px; font-weight:600; cursor:pointer;">Создать
                            секцию</button>
                    </div>

                </div>
            </div>

            <script>
                function openCreateModal() {
                    backToCreateType();
                    document.getElementById('create-modal-overlay').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                function openCreateModalAtSection() {
                    // Сначала сбрасываем в исходное состояние, затем сразу переходим к шагу секции
                    backToCreateType();
                    document.getElementById('create-modal-overlay').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    selectCreateType('section');
                }

                function closeCreateModal() {
                    document.getElementById('create-modal-overlay').style.display = 'none';
                    document.body.style.overflow = '';
                    document.getElementById('create-section-input').value = '';
                }

                function selectCreateType(type) {
                    if (type === 'section') {
                        document.getElementById('create-step-type').style.display = 'none';
                        document.getElementById('create-step-section').style.display = '';
                        document.getElementById('create-modal-title').textContent = 'Новая секция';
                        document.getElementById('create-modal-subtitle').textContent = 'Введите название';
                        document.getElementById('create-section-confirm-btn').style.display = '';
                        setTimeout(() => document.getElementById('create-section-input').focus(), 50);
                    }
                }

                function backToCreateType() {
                    document.getElementById('create-step-type').style.display = '';
                    document.getElementById('create-step-section').style.display = 'none';
                    document.getElementById('create-modal-title').textContent = 'Создать';
                    document.getElementById('create-modal-subtitle').textContent = 'Выберите тип';
                    document.getElementById('create-section-confirm-btn').style.display = 'none';
                    document.getElementById('create-section-input').value = '';
                }

                function doCreateSection() {
                    const val = document.getElementById('create-section-input').value.trim();
                    if (!val) return;
                    @this.set('newSectionTitle', val);
                    @this.call('addSection');
                    closeCreateModal();
                }

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && document.getElementById('create-modal-overlay').style.display === 'flex') {
                        closeCreateModal();
                    }
                });
            </script>
        </div>



        @php
            $attachData = [];
            foreach ($sections as $sec) {
                $addedTestIds = $sec->items()->where('item_type', \App\Models\Test::class)->pluck('item_id')->toArray();
                $addedLectureIds = $sec
                    ->items()
                    ->where('item_type', \App\Models\Lecture::class)
                    ->pluck('item_id')
                    ->toArray();
                $addedMaterialIds = $sec
                    ->items()
                    ->where('item_type', \App\Models\Material::class)
                    ->pluck('item_id')
                    ->toArray();
                $addedAssignmentIds = $sec
                    ->items()
                    ->where('item_type', \App\Models\Assignment::class)
                    ->pluck('item_id')
                    ->toArray();
                $globalTestsForSec = \App\Models\Test::where('is_global', true)
                    ->where('status', \App\Models\Test::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedTestIds)
                    ->get()
                    ->map(fn($t) => ['id' => $t->id, 'title' => $t->title])
                    ->values();
                $myTestsForSec = \App\Models\Test::where('user_id', auth()->id())
                    ->where('status', \App\Models\Test::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedTestIds)
                    ->get()
                    ->map(fn($t) => ['id' => $t->id, 'title' => $t->title])
                    ->values();

                $globalLecturesForSec = \App\Models\Lecture::where('is_global', true)
                    ->where('status', \App\Models\Lecture::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedLectureIds)
                    ->get()
                    ->map(fn($l) => ['id' => $l->id, 'title' => $l->title])
                    ->values();
                $myLecturesForSec = \App\Models\Lecture::where('user_id', auth()->id())
                    ->where('status', \App\Models\Lecture::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedLectureIds)
                    ->get()
                    ->map(fn($l) => ['id' => $l->id, 'title' => $l->title])
                    ->values();

                $globalMaterialsForSec = \App\Models\Material::where('is_global', true)
                    ->where('status', \App\Models\Material::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedMaterialIds)
                    ->get()
                    ->map(fn($m) => ['id' => $m->id, 'title' => $m->title])
                    ->values();
                $myMaterialsForSec = \App\Models\Material::where('user_id', auth()->id())
                    ->where('status', \App\Models\Material::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedMaterialIds)
                    ->get()
                    ->map(fn($m) => ['id' => $m->id, 'title' => $m->title])
                    ->values();

                $globalAssignmentsForSec = \App\Models\Assignment::where('is_global', true)
                    ->where('status', \App\Models\Assignment::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedAssignmentIds)
                    ->get()
                    ->map(fn($a) => ['id' => $a->id, 'title' => $a->title])
                    ->values();
                $myAssignmentsForSec = \App\Models\Assignment::where('user_id', auth()->id())
                    ->where('status', \App\Models\Assignment::STATUS_ACTIVE)
                    ->whereNotIn('id', $addedAssignmentIds)
                    ->get()
                    ->map(fn($a) => ['id' => $a->id, 'title' => $a->title])
                    ->values();

                $attachData[$sec->id] = [
                    'tests' => [
                        'course' => $course->tests
                            ->where('status', \App\Models\Test::STATUS_ACTIVE)
                            ->whereNotIn('id', $addedTestIds)
                            ->map(fn($t) => ['id' => $t->id, 'title' => $t->title])
                            ->values(),
                        'global' => $globalTestsForSec,
                        'mine' => $myTestsForSec,
                    ],
                    'lectures' => [
                        'course' => $course->lectures
                            ->where('status', \App\Models\Lecture::STATUS_ACTIVE)
                            ->whereNotIn('id', $addedLectureIds)
                            ->map(fn($l) => ['id' => $l->id, 'title' => $l->title])
                            ->values(),
                        'global' => $globalLecturesForSec,
                        'mine' => $myLecturesForSec,
                    ],
                    'materials' => [
                        'course' => $course->materials
                            ->where('status', \App\Models\Material::STATUS_ACTIVE)
                            ->whereNotIn('id', $addedMaterialIds)
                            ->map(fn($m) => ['id' => $m->id, 'title' => $m->title])
                            ->values(),
                        'global' => $globalMaterialsForSec,
                        'mine' => $myMaterialsForSec,
                    ],
                    'assignments' => [
                        'course' => $course->assignments
                            ->where('status', \App\Models\Assignment::STATUS_ACTIVE)
                            ->whereNotIn('id', $addedAssignmentIds)
                            ->map(fn($a) => ['id' => $a->id, 'title' => $a->title])
                            ->values(),
                        'global' => $globalAssignmentsForSec,
                        'mine' => $myAssignmentsForSec,
                    ],
                ];
            }
        @endphp

        <div id="attach-teacher-root">
            <div id="attach-modal-overlay" onclick="if(event.target===this) closeAttachModal()"
                style="display:none; position:fixed; inset:0; background:rgba(17,23,32,.45); backdrop-filter:blur(2px); z-index:1000; align-items:center; justify-content:center; padding:1.5rem;">
                <div
                    style="background:var(--color-surface); border-radius:var(--r-xl); box-shadow:var(--shadow-lg); width:100%; max-width:480px; max-height:calc(100vh - 3rem); overflow-y:auto;">
                    <div
                        style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem 1rem; border-bottom:1px solid var(--color-border);">
                        <div>
                            <div style="font-size:16px; font-weight:700; color:var(--gray-800);">Добавить элемент</div>
                            <div id="attach-modal-subtitle"
                                style="font-size:13px; color:var(--color-text-muted); margin-top:2px;">Выберите тип и
                                элемент</div>
                        </div>
                        <button onclick="closeAttachModal()"
                            style="width:28px; height:28px; border:none; background:var(--gray-100); color:var(--gray-500); border-radius:var(--r-sm); cursor:pointer; font-size:17px; line-height:1; display:flex; align-items:center; justify-content:center;">×</button>
                    </div>
                    <div style="padding:1.25rem 1.5rem;">
                        <div id="attach-step-type">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:1.25rem;">
                                <button type="button" onclick="selectAttachType('test')"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--teal-50); color:var(--teal-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📝</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Тест</div>
                                </button>
                                <button type="button" onclick="selectAttachType('lecture')"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--sky-50); color:var(--sky-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📖</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Лекция</div>
                                </button>
                                <button type="button" onclick="selectAttachType('material')"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--green-50); color:var(--green-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📎</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Материал</div>
                                </button>
                                <button type="button" onclick="selectAttachType('assignment')"
                                    style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div
                                        style="width:36px; height:36px; border-radius:var(--r-md); background:var(--amber-50); color:var(--amber-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">
                                        📋</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Задание</div>
                                </button>
                            </div>
                        </div>
                        <div id="attach-step-item" style="display:none;">
                            <button type="button" onclick="backToTypeStep()"
                                style="background:none; border:none; cursor:pointer; font-size:12px; font-weight:600; color:var(--color-text-muted); padding:0; margin-bottom:12px; display:flex; align-items:center; gap:4px;">←
                                Назад</button>
                            <input type="text" id="attach-search-input" placeholder="Поиск..."
                                oninput="filterAttachItems(this.value)"
                                style="width:100%; box-sizing:border-box; padding:7px 11px; border:1px solid var(--color-border); border-radius:var(--r-sm); font-size:13px; font-family:var(--font-body); background:var(--color-surface); margin-bottom:10px;">
                            <div id="attach-item-list"
                                style="display:flex; flex-direction:column; gap:6px; max-height:260px; overflow-y:auto;">
                            </div>
                        </div>
                    </div>
                    <div
                        style="padding:1rem 1.5rem; border-top:1px solid var(--color-border); display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                        <button type="button" onclick="closeAttachModal()"
                            style="display:inline-flex; align-items:center; padding:8px 16px; background:transparent; color:var(--color-text-secondary); border:1px solid var(--color-border); border-radius:var(--r-full); font-family:var(--font-body); font-size:14px; font-weight:600; cursor:pointer;">Отмена</button>
                        <button type="button" id="attach-confirm-btn" onclick="confirmAttach()"
                            style="display:inline-flex; align-items:center; padding:9px 20px; background:var(--teal-500); color:#fff; border:none; border-radius:var(--r-full); font-family:var(--font-body); font-size:14px; font-weight:600; cursor:pointer; opacity:.5; pointer-events:none;">Добавить</button>
                    </div>
                </div>
            </div>
            <script>
                let ATTACH_DATA = @json($attachData);
                let _aSectionId = null,
                    _aType = null,
                    _aItemId = null,
                    _aItems = [];

                async function openAttachModal(sid) {
                    _aSectionId = sid;
                    _aType = null;
                    _aItemId = null;
                    document.getElementById('attach-step-type').style.display = '';
                    document.getElementById('attach-step-item').style.display = 'none';
                    document.getElementById('attach-modal-subtitle').textContent = 'Выберите тип и элемент';
                    setACE(false);
                    document.getElementById('attach-modal-overlay').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    const data = await @this.getAttachData(sid);
                    ATTACH_DATA[sid] = data;
                }

                function closeAttachModal() {
                    document.getElementById('attach-modal-overlay').style.display = 'none';
                    document.body.style.overflow = '';
                }
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') closeAttachModal();
                });

                function selectAttachType(type) {
                    _aType = type;
                    _aItemId = null;
                    const map = {
                        test: {
                            key: 'tests',
                            sub: 'Выберите тест (общий банк / мои / курс)'
                        },
                        lecture: {
                            key: 'lectures',
                            sub: 'Выберите лекцию (общий банк / мои / курс)'
                        },
                        material: {
                            key: 'materials',
                            sub: 'Выберите материал (общий банк / мои / курс)'
                        },
                        assignment: {
                            key: 'assignments',
                            sub: 'Выберите задание (общий банк / мои / курс)'
                        },
                    };

                    document.getElementById('attach-modal-subtitle').textContent = map[type].sub;
                    document.getElementById('attach-step-type').style.display = 'none';
                    document.getElementById('attach-step-item').style.display = '';
                    document.getElementById('attach-search-input').value = '';

                    if (type === 'test' || type === 'lecture' || type === 'material' || type === 'assignment') {
                        // grouped: { course:[], global:[], mine:[] }
                        _aItems = (ATTACH_DATA[_aSectionId] && ATTACH_DATA[_aSectionId][map[type].key]) || {
                            course: [],
                            global: [],
                            mine: []
                        };
                        renderAItemsGrouped(_aItems);
                    } else {
                        _aItems = (ATTACH_DATA[_aSectionId] || {})[map[type].key] || [];
                        renderAItems(_aItems);
                    }

                    setACE(false);
                }

                function backToTypeStep() {
                    _aType = null;
                    _aItemId = null;
                    document.getElementById('attach-step-type').style.display = '';
                    document.getElementById('attach-step-item').style.display = 'none';
                    document.getElementById('attach-modal-subtitle').textContent = 'Выберите тип и элемент';
                    setACE(false);
                }

                function filterAttachItems(q) {
                    if (_aType === 'test' || _aType === 'lecture' || _aType === 'material' || _aType === 'assignment') {
                        const grouped = _aItems;
                        const f = (arr) => arr.filter(i => i.title.toLowerCase().includes(q.toLowerCase()));
                        renderAItemsGrouped({
                            course: f(grouped.course || []),
                            global: f(grouped.global || []),
                            mine: f(grouped.mine || []),
                        });
                    } else {
                        renderAItems(q ? _aItems.filter(i => i.title.toLowerCase().includes(q.toLowerCase())) : _aItems);
                    }
                }

                function renderAItems(items) {
                    const list = document.getElementById('attach-item-list');
                    if (!items.length) {
                        list.innerHTML =
                            '<div style="padding:24px;text-align:center;color:var(--color-text-muted);font-size:13px;">Нет доступных элементов</div>';
                        return;
                    }
                    list.innerHTML = items.map(item =>
                        `<div onclick="selectAItem(${item.id},this)" data-id="${item.id}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid var(--color-border);border-radius:var(--r-md);background:var(--color-surface-2);font-size:13px;color:var(--gray-700);cursor:pointer;"><div class="acheck" style="width:18px;height:18px;flex-shrink:0;border-radius:50%;border:1.5px solid var(--gray-300);display:flex;align-items:center;justify-content:center;font-size:11px;"></div><span>${item.title.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</span></div>`
                    ).join('');
                }

                function renderAItemsGrouped(grouped) {
                    const list = document.getElementById('attach-item-list');
                    const hasAny = (grouped.course && grouped.course.length) || (grouped.global && grouped.global.length) || (
                        grouped.mine && grouped.mine.length);
                    if (!hasAny) {
                        list.innerHTML =
                            '<div style="padding:24px;text-align:center;color:var(--color-text-muted);font-size:13px;">Нет доступных элементов</div>';
                        return;
                    }
                    const mineLabel = _aType === 'lecture' ? 'Мои лекции' : (_aType === 'material' ? 'Мои материалы' : (_aType === 'assignment' ? 'Мои задания' : 'Мои тесты'));
                    let html = '';
                    const section = (title, arr) => {
                        if (!arr || !arr.length) return '';
                        return `<div style="font-size:13px;font-weight:700;color:var(--gray-700);margin:6px 0;">${title}</div>` +
                            arr.map(item =>
                                `<div onclick="selectAItem(${item.id},this)" data-id="${item.id}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid var(--color-border);border-radius:var(--r-md);background:var(--color-surface-2);font-size:13px;color:var(--gray-700);cursor:pointer;"><div class="acheck" style="width:18px;height:18px;flex-shrink:0;border-radius:50%;border:1.5px solid var(--gray-300);display:flex;align-items:center;justify-content:center;font-size:11px;"></div><span>${item.title.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</span></div>`
                            ).join('');
                    };

                    html += section('В этом курсе', grouped.course || []);
                    html += section('Общий банк', grouped.global || []);
                    html += section(mineLabel, grouped.mine || []);

                    list.innerHTML = html;
                }

                function selectAItem(id, el) {
                    _aItemId = id;
                    document.querySelectorAll('#attach-item-list [data-id]').forEach(r => {
                        r.style.borderColor = 'var(--color-border)';
                        r.style.background = 'var(--color-surface-2)';
                        const c = r.querySelector('.acheck');
                        c.textContent = '';
                        c.style.background = '';
                        c.style.borderColor = 'var(--gray-300)';
                        c.style.color = '';
                    });
                    el.style.borderColor = 'var(--teal-500)';
                    el.style.background = 'var(--teal-50)';
                    const c = el.querySelector('.acheck');
                    c.textContent = '✓';
                    c.style.background = 'var(--teal-500)';
                    c.style.borderColor = 'var(--teal-500)';
                    c.style.color = '#fff';
                    setACE(true);
                }

                function setACE(on) {
                    const b = document.getElementById('attach-confirm-btn');
                    b.style.opacity = on ? '1' : '.5';
                    b.style.pointerEvents = on ? 'auto' : 'none';
                }

                function confirmAttach() {
                    if (!_aSectionId || !_aType || !_aItemId) return;
                    @this.attachItem(_aSectionId, _aType, _aItemId);
                    closeAttachModal();
                }
            </script>
        </div>
    @endif
</div>
