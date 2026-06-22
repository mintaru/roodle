{{-- resources/views/livewire/partials/item-menu.blade.php --}}
<div x-show="open"
    x-transition:enter="transition ease-out duration-100"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    x-init="
    $watch('open', val => {
        if (val) {
            $nextTick(() => {
                const btn = $el.previousElementSibling;
                const rect = btn.getBoundingClientRect();
                const menuHeight = $el.offsetHeight;
                const spaceBelow = window.innerHeight - rect.bottom;
                const spaceAbove = rect.top;

                if (spaceBelow < menuHeight + 8 && spaceAbove >= menuHeight + 8) {
                    // Открываем вверх
                    $el.style.top = (rect.top - menuHeight - 4) + 'px';
                } else {
                    // Открываем вниз (по умолчанию)
                    $el.style.top = (rect.bottom + 4) + 'px';
                }

                $el.style.left = (rect.right - $el.offsetWidth) + 'px';
            });
        }
    })
    "
    style="position: fixed; z-index: 9999;
           background: var(--color-surface); border: 1px solid var(--color-border);
           border-radius: var(--r-md); box-shadow: 0 4px 16px rgba(0,0,0,0.12);
           min-width: 185px; padding: 4px 0; white-space: nowrap;">

    <div style="display: flex; padding: 4px 8px; border-bottom: 1px solid var(--color-border); gap: 4px;">

        <div style="display: flex; padding: 4px 8px; border-bottom: 1px solid var(--color-border); gap: 4px;">
            <button wire:click="moveItem({{ $sectionItem->id }}, 'up')" @click="open = false" class="btn btn-ghost"
                style="flex:1; padding: 3px 6px; font-size: 12px;">↑ Вверх</button>
            <button wire:click="moveItem({{ $sectionItem->id }}, 'down')" @click="open = false" class="btn btn-ghost"
                style="flex:1; padding: 3px 6px; font-size: 12px;">↓ Вниз</button>
        </div>
    </div>

    @if ($type === 'test')
        <a href="{{ route('tests.edit-settings', $item) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--teal-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Редактировать тест</a>
        <a href="{{ route('tests.show', $item) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--teal-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Редактировать вопросы</a>
        <a href="{{ route('tests.results', $item) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--teal-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Обзор</a>
    @elseif ($type === 'lecture')
        <a href="{{ route('admin.lectures.edit', $item) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--sky-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Редактировать</a>
    @elseif ($type === 'material')
        <a href="{{ route('materials.edit', ['course' => $course, 'material' => $item]) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--green-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Редактировать</a>
    @elseif ($type === 'assignment')
        <a href="{{ route('assignments.edit', ['course' => $course, 'assignment' => $item]) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--teal-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Редактировать</a>
        <a href="{{ route('assignments.show', ['course' => $course, 'assignment' => $item]) }}"
            style="display:block; padding:7px 14px; font-size:13px; color:var(--teal-600); text-decoration:none;"
            @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">Обзор</a>
    @endif

    <button type="button" wire:click="openItemVisibility({{ $sectionItem->id }})" @click="open = false"
        style="display:block; width:100%; text-align:left; padding:7px 14px; font-size:13px; color:var(--sky-600); background:none; border:none; cursor:pointer;"
        @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">
         Видимость
    </button>

    <div style="border-top: 1px solid var(--color-border); margin: 4px 0;"></div>

    <button type="button"
        @click="$dispatch('open-detach-item-modal', { id: {{ $sectionItem->id }} }); open = false"
        style="display:block; width:100%; text-align:left; padding:7px 14px; font-size:13px; color:#dc2626; background:none; border:none; cursor:pointer;"
        @mouseenter="$el.style.background='var(--color-surface-2)'"
@mouseleave="$el.style.background='var(--color-surface)'">
        Убрать из секции
    </button>
</div>
