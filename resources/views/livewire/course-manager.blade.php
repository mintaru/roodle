<div x-data="{ search: '' }">

    {{-- Success and Error Messages --}}
    @if ($successMessage)
        <div style="padding: 12px 16px; background: var(--green-50); color: var(--green-600); border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid var(--green-200); font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $successMessage }}</span>
            <button @click="$wire.set('successMessage', '')" style="background: none; border: none; cursor: pointer; font-size: 16px;">✕</button>
        </div>
    @endif

    @if ($errorMessage)
        <div style="padding: 12px 16px; background: #ffebee; color: var(--red-500); border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid #ffcdd2; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <span>{{ $errorMessage }}</span>
            <button @click="$wire.set('errorMessage', '')" style="background: none; border: none; cursor: pointer; font-size: 16px;">✕</button>
        </div>
    @endif

    {{-- Search --}}
    <div style="margin-bottom: 16px; position: relative;">
        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); font-size: 14px; pointer-events: none;">🔍</span>
        <input x-model="search" type="text" placeholder="Поиск по тестам, лекциям, материалам..."
            style="width: 100%; padding: 8px 36px 8px 32px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 13px; box-sizing: border-box; background: var(--color-surface);">
        <button x-show="search !== ''" @click="search = ''"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 14px; color: var(--color-text-muted); line-height: 1;">✕</button>
    </div>

    {{-- Add New Section --}}
    @hasanyrole('teacher|admin')
        <div style="margin: 24px 0; padding: 16px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">
            <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--gray-800);">Добавить новую секцию</h2>
            <form @submit.prevent="$wire.addSection()" style="display: flex; gap: 8px;">
                <input type="text" wire:model="newSectionTitle" placeholder="Название секции" required
                    style="flex: 1; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 13px;">
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">Создать</button>
            </form>
        </div>
    @endhasanyrole

    {{-- Sections List --}}
    @forelse($sections as $section)
        @php $sectionItems = $section->items()->orderBy('position')->get(); @endphp
        <div
            x-data="{ open: true }"
            x-effect="if (search !== '') open = true"
            x-show="search === '' || Array.from($el.querySelectorAll('[data-search-title]')).some(el => el.dataset.searchTitle.includes(search.toLowerCase().trim()))"
            style="margin-bottom: 24px; padding: 16px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                    @hasanyrole('teacher|admin')
                        @if ($editingSectionId === $section->id)
                            <form @submit.prevent="$wire.updateSection()" style="display: flex; gap: 8px; flex: 1;">
                                <input type="text" wire:model="editingSectionTitle"
                                    style="flex: 1; padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--r-sm); font-size: 14px; font-weight: 600;">
                                <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Сохранить</button>
                                <button type="button" @click="$wire.cancelEdit()" class="btn btn-ghost" style="padding: 6px 12px; font-size: 12px;">Отменить</button>
                            </form>
                        @else
                            <h2 style="font-size: 16px; font-weight: 700; color: var(--gray-800); cursor: pointer; flex: 1;"
                                @click="$wire.editSection({{ $section->id }})">{{ $section->title }}</h2>
                        @endif
                    @else
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--gray-800);">{{ $section->title }}</h2>
                    @endhasanyrole
                </div>
                <div style="display: flex; gap: 6px; align-items: center;">
                    <button @click="open = !open" class="btn btn-ghost" style="padding: 6px 10px; font-size: 12px;" :title="open ? 'Свернуть' : 'Развернуть'">
                        <span x-text="open ? '▲' : '▼'"></span>
                    </button>
                    @hasanyrole('teacher|admin')
                        <button wire:click="moveSection({{ $section->id }}, 'up')" class="btn btn-ghost" style="padding: 6px 10px; font-size: 12px;" title="Сдвинуть вверх">↑</button>
                        <button wire:click="moveSection({{ $section->id }}, 'down')" class="btn btn-ghost" style="padding: 6px 10px; font-size: 12px;" title="Сдвинуть вниз">↓</button>
                        <button type="button" onclick="openAttachModal({{ $section->id }})" class="btn btn-ghost"
                            style="padding: 6px 10px; font-size: 16px; line-height: 1; color: var(--teal-600); border-color: var(--teal-200);" title="Добавить элемент">+</button>
                        <button wire:click="deleteSection({{ $section->id }})" onclick="return confirm('Удалить секцию?')"
                            class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;">Удалить</button>
                    @endhasanyrole
                </div>
            </div>

            <div x-show="open" x-transition>
                <div>
                    @forelse($sectionItems as $sectionItem)
                        @php
                            $item = $sectionItem->item;
                            $isArchived =
                                ($item instanceof \App\Models\Test && ($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED) ||
                                ($item instanceof \App\Models\Lecture && ($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED);
                            $periodEndPassed = $item->period_end && \Carbon\Carbon::parse($item->period_end)->isPast();
                        @endphp
                        @if (($isArchived || $periodEndPassed) && !auth()->user()?->hasAnyRole(['teacher', 'admin']))
                            @continue
                        @endif
                        <div
                            data-search-title="{{ strtolower($item->title) }}"
                            x-show="search === '' || '{{ strtolower(addslashes($item->title)) }}'.includes(search.toLowerCase().trim())"
                            style="padding: 12px; background: var(--color-surface-2); border: 1px solid var(--color-border); border-radius: var(--r-md); margin-bottom: 8px; display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                @if ($item instanceof \App\Models\Test)
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <strong style="color: var(--teal-600); font-size: 12px; text-transform: uppercase;">Тест</strong>
                                        @if (($item->status ?? 'active') === \App\Models\Test::STATUS_ARCHIVED)
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
                                        @if (($item->status ?? 'active') === \App\Models\Lecture::STATUS_ARCHIVED)
                                            <span style="color: var(--amber-500); font-size: 12px; font-weight: 600;">[архивирована]</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('lectures.show', ['course' => $course, 'lecture' => $item]) }}" style="color: var(--sky-600); text-decoration: none; font-weight: 600; font-size: 14px;">{{ $item->title }}</a>
                                @elseif($item instanceof \App\Models\Material)
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <strong style="color: var(--green-600); font-size: 12px; text-transform: uppercase;">📎 Материал</strong>
                                        @if (($item->status ?? 'active') === \App\Models\Material::STATUS_ARCHIVED)
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
            </div>
        </div>
    @empty
        <div style="padding: 24px; text-align: center; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--r-lg);">
            <p style="color: var(--color-text-muted); font-size: 14px;">Секции еще не созданы. Создайте первую секцию для организации контента курса.</p>
        </div>
    @endforelse

    {{-- MODAL: добавить элемент — только для учителей, всё в одном div --}}
    @hasanyrole('teacher|admin')
        @php
        $attachData = [];
        foreach ($sections as $sec) {
            $addedTestIds     = $sec->items()->where('item_type', \App\Models\Test::class)->pluck('item_id')->toArray();
            $addedLectureIds  = $sec->items()->where('item_type', \App\Models\Lecture::class)->pluck('item_id')->toArray();
            $addedMaterialIds = $sec->items()->where('item_type', \App\Models\Material::class)->pluck('item_id')->toArray();
            $attachData[$sec->id] = [
                'tests'     => $course->tests->where('status', \App\Models\Test::STATUS_ACTIVE)->whereNotIn('id', $addedTestIds)->map(fn($t) => ['id' => $t->id, 'title' => $t->title])->values(),
                'lectures'  => $course->lectures->where('status', \App\Models\Lecture::STATUS_ACTIVE)->whereNotIn('id', $addedLectureIds)->map(fn($l) => ['id' => $l->id, 'title' => $l->title])->values(),
                'materials' => $course->materials->where('status', \App\Models\Material::STATUS_ACTIVE)->whereNotIn('id', $addedMaterialIds)->map(fn($m) => ['id' => $m->id, 'title' => $m->title])->values(),
            ];
        }
        @endphp

        <div id="attach-teacher-root">
            <div id="attach-modal-overlay" onclick="if(event.target===this) closeAttachModal()"
                style="display:none; position:fixed; inset:0; background:rgba(17,23,32,.45); backdrop-filter:blur(2px); z-index:1000; align-items:center; justify-content:center; padding:1.5rem;">
                <div style="background:var(--color-surface); border-radius:var(--r-xl); box-shadow:var(--shadow-lg); width:100%; max-width:480px; max-height:calc(100vh - 3rem); overflow-y:auto;">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem 1rem; border-bottom:1px solid var(--color-border);">
                        <div>
                            <div style="font-size:16px; font-weight:700; color:var(--gray-800);">Добавить элемент</div>
                            <div id="attach-modal-subtitle" style="font-size:13px; color:var(--color-text-muted); margin-top:2px;">Выберите тип и элемент</div>
                        </div>
                        <button onclick="closeAttachModal()" style="width:28px; height:28px; border:none; background:var(--gray-100); color:var(--gray-500); border-radius:var(--r-sm); cursor:pointer; font-size:17px; line-height:1; display:flex; align-items:center; justify-content:center;">×</button>
                    </div>
                    <div style="padding:1.25rem 1.5rem;">
                        <div id="attach-step-type">
                            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:1.25rem;">
                                <button type="button" onclick="selectAttachType('test')" style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div style="width:36px; height:36px; border-radius:var(--r-md); background:var(--teal-50); color:var(--teal-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">📝</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Тест</div>
                                </button>
                                <button type="button" onclick="selectAttachType('lecture')" style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div style="width:36px; height:36px; border-radius:var(--r-md); background:var(--sky-50); color:var(--sky-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">📖</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Лекция</div>
                                </button>
                                <button type="button" onclick="selectAttachType('material')" style="padding:1rem .75rem; border:1.5px solid var(--color-border); border-radius:var(--r-lg); cursor:pointer; background:transparent; font-family:var(--font-body); text-align:center;">
                                    <div style="width:36px; height:36px; border-radius:var(--r-md); background:var(--green-50); color:var(--green-600); display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-size:18px;">📎</div>
                                    <div style="font-size:13px; font-weight:700; color:var(--gray-800);">Материал</div>
                                </button>
                            </div>
                        </div>
                        <div id="attach-step-item" style="display:none;">
                            <button type="button" onclick="backToTypeStep()" style="background:none; border:none; cursor:pointer; font-size:12px; font-weight:600; color:var(--color-text-muted); padding:0; margin-bottom:12px; display:flex; align-items:center; gap:4px;">← Назад</button>
                            <input type="text" id="attach-search-input" placeholder="Поиск..." oninput="filterAttachItems(this.value)"
                                style="width:100%; box-sizing:border-box; padding:7px 11px; border:1px solid var(--color-border); border-radius:var(--r-sm); font-size:13px; font-family:var(--font-body); background:var(--color-surface); margin-bottom:10px;">
                            <div id="attach-item-list" style="display:flex; flex-direction:column; gap:6px; max-height:260px; overflow-y:auto;"></div>
                        </div>
                    </div>
                    <div style="padding:1rem 1.5rem; border-top:1px solid var(--color-border); display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                        <button type="button" onclick="closeAttachModal()" style="display:inline-flex; align-items:center; padding:8px 16px; background:transparent; color:var(--color-text-secondary); border:1px solid var(--color-border); border-radius:var(--r-full); font-family:var(--font-body); font-size:14px; font-weight:600; cursor:pointer;">Отмена</button>
                        <button type="button" id="attach-confirm-btn" onclick="confirmAttach()" style="display:inline-flex; align-items:center; padding:9px 20px; background:var(--teal-500); color:#fff; border:none; border-radius:var(--r-full); font-family:var(--font-body); font-size:14px; font-weight:600; cursor:pointer; opacity:.5; pointer-events:none;">Добавить</button>
                    </div>
                </div>
            </div>
            <script>
            const ATTACH_DATA = @json($attachData);
            let _aSectionId = null, _aType = null, _aItemId = null, _aItems = [];
            function openAttachModal(sid) {
                _aSectionId = sid; _aType = null; _aItemId = null;
                document.getElementById('attach-step-type').style.display = '';
                document.getElementById('attach-step-item').style.display = 'none';
                document.getElementById('attach-modal-subtitle').textContent = 'Выберите тип и элемент';
                setACE(false);
                document.getElementById('attach-modal-overlay').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            function closeAttachModal() {
                document.getElementById('attach-modal-overlay').style.display = 'none';
                document.body.style.overflow = '';
            }
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAttachModal(); });
            function selectAttachType(type) {
                _aType = type; _aItemId = null;
                const map = { test:{key:'tests',sub:'Выберите тест'}, lecture:{key:'lectures',sub:'Выберите лекцию'}, material:{key:'materials',sub:'Выберите материал'} };
                _aItems = (ATTACH_DATA[_aSectionId] || {})[map[type].key] || [];
                document.getElementById('attach-modal-subtitle').textContent = map[type].sub;
                document.getElementById('attach-step-type').style.display = 'none';
                document.getElementById('attach-step-item').style.display = '';
                document.getElementById('attach-search-input').value = '';
                renderAItems(_aItems); setACE(false);
            }
            function backToTypeStep() {
                _aType = null; _aItemId = null;
                document.getElementById('attach-step-type').style.display = '';
                document.getElementById('attach-step-item').style.display = 'none';
                document.getElementById('attach-modal-subtitle').textContent = 'Выберите тип и элемент';
                setACE(false);
            }
            function filterAttachItems(q) { renderAItems(q ? _aItems.filter(i => i.title.toLowerCase().includes(q.toLowerCase())) : _aItems); }
            function renderAItems(items) {
                const list = document.getElementById('attach-item-list');
                if (!items.length) { list.innerHTML = '<div style="padding:24px;text-align:center;color:var(--color-text-muted);font-size:13px;">Нет доступных элементов</div>'; return; }
                list.innerHTML = items.map(item => `<div onclick="selectAItem(${item.id},this)" data-id="${item.id}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid var(--color-border);border-radius:var(--r-md);background:var(--color-surface-2);font-size:13px;color:var(--gray-700);cursor:pointer;"><div class="acheck" style="width:18px;height:18px;flex-shrink:0;border-radius:50%;border:1.5px solid var(--gray-300);display:flex;align-items:center;justify-content:center;font-size:11px;"></div><span>${item.title.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</span></div>`).join('');
            }
            function selectAItem(id, el) {
                _aItemId = id;
                document.querySelectorAll('#attach-item-list [data-id]').forEach(r => {
                    r.style.borderColor='var(--color-border)'; r.style.background='var(--color-surface-2)';
                    const c=r.querySelector('.acheck'); c.textContent=''; c.style.background=''; c.style.borderColor='var(--gray-300)'; c.style.color='';
                });
                el.style.borderColor='var(--teal-500)'; el.style.background='var(--teal-50)';
                const c=el.querySelector('.acheck'); c.textContent='✓'; c.style.background='var(--teal-500)'; c.style.borderColor='var(--teal-500)'; c.style.color='#fff';
                setACE(true);
            }
            function setACE(on) { const b=document.getElementById('attach-confirm-btn'); b.style.opacity=on?'1':'.5'; b.style.pointerEvents=on?'auto':'none'; }
            function confirmAttach() {
                if (!_aSectionId||!_aType||!_aItemId) return;
                @this.attachItem(_aSectionId, _aType, _aItemId);
                closeAttachModal();
            }
            </script>
        </div>
    @endhasanyrole

</div>
