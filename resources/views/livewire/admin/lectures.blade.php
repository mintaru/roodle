<div x-data="{ deleteLectureId: null }">
    <x-admin-search-bar :searchColumns="$this->getSearchColumns()" />

    <style>
        .groups-table th:not(:first-child),
        .groups-table td:not(:first-child) {
            text-align: center;
        }
    </style>

    <table class="groups-table">
        <thead>
            <tr>
                <th wire:click="sortBy('title')" style="cursor:pointer; user-select:none;">
                    Название
                    <span style="font-size:11px; display:inline-block; width:12px; text-align:center;">@if ($sortColumn === 'title'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</span>
                </th>
                <th wire:click="sortBy('course')" style="cursor:pointer; user-select:none;">
                    Курс
                    <span style="font-size:11px; display:inline-block; width:12px; text-align:center;">@if ($sortColumn === 'course'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</span>
                </th>
                <th>PDF файл</th>
                <th style="width:60px;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $lecture)
                <tr>
                    <td>
                        <a href="{{ route('admin.lectures.edit', $lecture) }}" class="table-link">
                            {{ $lecture->title }}
                        </a>
                    </td>
                    <td>{{ $lecture->course?->title ?? '—' }}</td>
                    <td>
                        @if($lecture->pdf_path)
                            <a href="{{ asset('storage/' . $lecture->pdf_path) }}" target="_blank" class="table-link">
                                Скачать
                            </a>
                        @else
                            <span style="color:#999;">Нет файла</span>
                        @endif
                    </td>
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
                                <a href="{{ route('admin.lectures.edit', $lecture) }}"
                                    @click="open = false"
                                    style="display:flex; align-items:center; gap:8px; padding:7px 14px; font-size:13px; color:#667eea; text-decoration:none;"
                                    @mouseenter="$el.style.background='#f5f5f5'"
                                    @mouseleave="$el.style.background='#fff'">
                                    <img src="{{ asset('images/edit.png') }}" alt="" style="width:18px; height:18px; flex-shrink:0;">
                                    Редактировать
                                </a>
                                <div style="border-top:1px solid #e0e0e0; margin:4px 0;"></div>
                                <button type="button"
                                    @click="deleteLectureId = {{ $lecture->id }}; open = false"
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
        <div x-show="deleteLectureId !== null" x-cloak class="modal-backdrop" @click.self="deleteLectureId = null">
            <div class="modal-box">
                <div class="modal-icon modal-icon--delete">
                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="#c62828" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3>Удалить лекцию?</h3>
                <p>Вы уверены, что хотите удалить эту лекцию?<br>Это действие нельзя отменить.</p>
                <div class="modal-actions">
                    <button class="modal-btn modal-btn--cancel" @click="deleteLectureId = null">Отмена</button>
                    <button class="modal-btn modal-btn--confirm-danger"
                        @click="deleteLectureId && document.getElementById('delete-form-' + deleteLectureId).submit()">Удалить</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Скрытые формы удаления --}}
    @foreach($items as $lecture)
        <form id="delete-form-{{ $lecture->id }}" action="{{ route('admin.lectures.destroy', $lecture) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
