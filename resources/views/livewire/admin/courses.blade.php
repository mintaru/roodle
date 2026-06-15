<div x-data="{ deleteCourseId: null, archiveCourseId: null, actionType: null }">
    <x-admin-search-bar :searchColumns="$this->getSearchColumns()" />

    <style>
        .groups-table th:not(:first-child),
        .groups-table td:not(:first-child) {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge--active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge--archived {
            background: #fff3e0;
            color: #e65100;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-box {
            background: #fff;
            border-radius: 28px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
        }

        .modal-icon {
            width: 52px;
            height: 52px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .modal-icon--delete {
            background: #ffebee;
        }

        .modal-icon--archive {
            background: #fff3e0;
        }

        .modal-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e2530;
            margin: 0 0 .5rem;
        }

        .modal-box p {
            font-size: 14px;
            color: #6b7a89;
            line-height: 1.6;
            margin: 0 0 1.5rem;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
        }

        .modal-btn {
            flex: 1;
            padding: 11px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Manrope', sans-serif;
            border: none;
            cursor: pointer;
            transition: .2s ease;
        }

        .modal-btn--cancel {
            background: #f0f3f5;
            color: #4a5668;
        }

        .modal-btn--cancel:hover {
            background: #e2e8ed;
        }

        .modal-btn--confirm-danger {
            background: #e74c3c;
            color: #fff;
        }

        .modal-btn--confirm-danger:hover {
            background: #c62828;
        }

        .modal-btn--confirm-archive {
            background: #f57c00;
            color: #fff;
        }

        .modal-btn--confirm-archive:hover {
            background: #e65100;
        }
    </style>

    <table class="groups-table">
        <thead>
            <tr>
                <th wire:click="sortBy('title')" style="cursor:pointer; user-select:none;">
                    Название
                    <span style="font-size:11px; display:inline-block; width:12px; text-align:center;">@if ($sortColumn === 'title'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</span>
                </th>
                <th wire:click="sortBy('author')" style="cursor:pointer; user-select:none;">
                    Автор
                    <span style="font-size:11px; display:inline-block; width:12px; text-align:center;">@if ($sortColumn === 'author'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</span>
                </th>
                <th wire:click="sortBy('status')" style="cursor:pointer; user-select:none;">
                    Статус
                    <span style="font-size:11px; display:inline-block; width:12px; text-align:center;">@if ($sortColumn === 'status'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</span>
                </th>
                <th wire:click="sortBy('group')" style="cursor:pointer; user-select:none;">
                    Группы
                    <span style="font-size:11px; display:inline-block; width:12px; text-align:center;">@if ($sortColumn === 'group'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</span>
                </th>
                <th style="width:60px;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $course)
                <tr>
                    <td>
                        <a href="{{ route('courses.edit', $course) }}" class="table-link">
                            {{ $course->title }}
                        </a>
                    </td>
                    <td>{{ $course->author?->name ?? '—' }}</td>
                    <td>
                        @if ($course->status === \App\Models\Course::STATUS_ACTIVE)
                            <span class="status-badge status-badge--active">Активен</span>
                        @else
                            <span class="status-badge status-badge--archived">В архиве</span>
                        @endif
                    </td>
                    <td>{{ $course->groups->pluck('name')->join(', ') ?: '—' }}</td>
                    <td style="position:relative;">
                        <div x-data="{ open: false }" @click.outside="open = false"
                            style="display:inline-flex; position:relative;">
                            <button type="button" @click="open = !open"
                                style="background:none; border:none; cursor:pointer; font-size:18px; line-height:1; color:var(--color-text-muted, #888); padding:4px 8px;">⋯</button>
                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                style="position:absolute; top:100%; right:0; z-index:9999;
                                       background:#fff; border:1px solid #e0e0e0;
                                       border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.12);
                                       min-width:170px; padding:4px 0; white-space:nowrap;">
                                <a href="{{ route('courses.edit', $course) }}"
                                    @click="open = false"
                                    style="display:flex; align-items:center; gap:8px; padding:7px 14px; font-size:13px; color:#667eea; text-decoration:none;"
                                    @mouseenter="$el.style.background='#f5f5f5'"
                                    @mouseleave="$el.style.background='#fff'">
                                    <img src="{{ asset('images/edit.png') }}" alt="" style="width:18px; height:18px; flex-shrink:0;">
                                    Редактировать
                                </a>
                                    @if ($course->status === \App\Models\Course::STATUS_ACTIVE)
                                    <div style="border-top:1px solid #e0e0e0; margin:4px 0;"></div>
                                    <button type="button"
                                        @click="archiveCourseId = {{ $course->id }}; actionType = 'archive'; open = false"
                                        style="display:flex; align-items:center; gap:8px; width:100%; text-align:left; padding:7px 14px; font-size:13px; color:#f57c00; background:none; border:none; cursor:pointer;"
                                        @mouseenter="$el.style.background='#f5f5f5'"
                                        @mouseleave="$el.style.background='#fff'">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#f57c00" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        Архивировать
                                    </button>
                                @else
                                    <div style="border-top:1px solid #e0e0e0; margin:4px 0;"></div>
                                    <button type="button"
                                        @click="archiveCourseId = {{ $course->id }}; actionType = 'restore'; open = false"
                                        style="display:flex; align-items:center; gap:8px; width:100%; text-align:left; padding:7px 14px; font-size:13px; color:#2e7d32; background:none; border:none; cursor:pointer;"
                                        @mouseenter="$el.style.background='#f5f5f5'"
                                        @mouseleave="$el.style.background='#fff'">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#2e7d32" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Восстановить
                                    </button>
                                @endif
                                <div style="border-top:1px solid #e0e0e0; margin:4px 0;"></div>
                                <button type="button"
                                    @click="deleteCourseId = {{ $course->id }}; actionType = 'delete'; open = false"
                                    style="display:flex; align-items:center; gap:8px; width:100%; text-align:left; padding:7px 14px; font-size:13px; color:#e74c3c; background:none; border:none; cursor:pointer;"
                                    @mouseenter="$el.style.background='#f5f5f5'"
                                    @mouseleave="$el.style.background='#fff'">
                                    <img src="{{ asset('images/delete.png') }}" alt="" style="width:14px; height:14px; flex-shrink:0;">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($items->hasPages())
        <div style="margin-top:1.5rem;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:13px; color:#666;">
                    Показано с {{ $items->firstItem() }} по {{ $items->lastItem() }} из {{ $items->total() }}
                </div>
                <div style="display:flex; gap:4px;">
                    @if($items->onFirstPage())
                        <span style="padding:4px 10px; font-size:13px; color:#aaa; border:1px solid #e0e0e0; border-radius:6px;">‹</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $items->currentPage() - 1 }})" style="padding:4px 10px; font-size:13px; color:#333; border:1px solid #e0e0e0; border-radius:6px; background:#fff; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">‹</button>
                    @endif
                    @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                        @if($page == $items->currentPage())
                            <span style="padding:4px 10px; font-size:13px; color:#fff; border:1px solid #667eea; border-radius:6px; background:#667eea;">{{ $page }}</span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }})" style="padding:4px 10px; font-size:13px; color:#333; border:1px solid #e0e0e0; border-radius:6px; background:#fff; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">{{ $page }}</button>
                        @endif
                    @endforeach
                    @if($items->hasMorePages())
                        <button type="button" wire:click="gotoPage({{ $items->currentPage() + 1 }})" style="padding:4px 10px; font-size:13px; color:#333; border:1px solid #e0e0e0; border-radius:6px; background:#fff; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">›</button>
                    @else
                        <span style="padding:4px 10px; font-size:13px; color:#aaa; border:1px solid #e0e0e0; border-radius:6px;">›</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal подтверждения удаления --}}
    <template x-teleport="body">
        <div x-show="deleteCourseId !== null" x-cloak class="modal-backdrop" @click.self="deleteCourseId = null">
            <div class="modal-box">
                <div class="modal-icon modal-icon--delete">
                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#c62828" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3>Удалить курс?</h3>
                <p>Вы уверены, что хотите удалить этот курс?<br>Это действие нельзя отменить.</p>
                <div class="modal-actions">
                    <button class="modal-btn modal-btn--cancel" @click="deleteCourseId = null">Отмена</button>
                    <button class="modal-btn modal-btn--confirm-danger"
                        @click="deleteCourseId && document.getElementById('delete-form-' + deleteCourseId).submit()">Удалить</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal подтверждения архивирования / восстановления --}}
    <template x-teleport="body">
        <div x-show="archiveCourseId !== null" x-cloak class="modal-backdrop" @click.self="archiveCourseId = null">
            <div class="modal-box">
                <div class="modal-icon modal-icon--archive">
                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#e65100" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <h3 x-text="actionType === 'archive' ? 'Архивировать курс?' : 'Восстановить курс?'"></h3>
                <p x-text="actionType === 'archive' ? 'Курс будет отправлен в архив. Студенты потеряют к нему доступ.' : 'Курс будет восстановлен и снова станет доступен.'"></p>
                <div class="modal-actions">
                    <button class="modal-btn modal-btn--cancel" @click="archiveCourseId = null">Отмена</button>
                    <button class="modal-btn modal-btn--confirm-archive"
                        @click="archiveCourseId && document.getElementById('archive-form-' + archiveCourseId).submit()"
                        x-text="actionType === 'archive' ? 'Архивировать' : 'Восстановить'"></button>
                </div>
            </div>
        </div>
    </template>

    {{-- Скрытые формы удаления --}}
    @foreach($items as $course)
        <form id="delete-form-{{ $course->id }}" action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    {{-- Скрытые формы архивирования / восстановления --}}
    @foreach($items as $course)
        @if ($course->status === \App\Models\Course::STATUS_ACTIVE)
            <form id="archive-form-{{ $course->id }}" action="{{ route('courses.archive', $course) }}" method="POST" style="display:none;">
                @csrf
                @method('PATCH')
            </form>
        @else
            <form id="archive-form-{{ $course->id }}" action="{{ route('courses.restore', $course) }}" method="POST" style="display:none;">
                @csrf
                @method('PATCH')
            </form>
        @endif
    @endforeach
</div>
