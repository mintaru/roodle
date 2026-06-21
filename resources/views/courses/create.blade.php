<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание курса</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body>

@include('components.menu')

<div class="layout">
    <aside class="sidebar">
        <p class="sidebar-section-title">Навигация</p>
        <a href="{{ route('home') }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Все курсы
        </a>
    </aside>

    <main class="main">

        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span style="color: var(--gray-600); font-weight: 500;">Создать курс</span>
        </nav>

        <div class="page-header">
            <h1 class="page-header__title">Создать курс</h1>
        </div>

        <div style="max-width: 600px;">
            <div class="panel" style="padding: 2rem;">

                @if (session('success'))
                    <div style="background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: var(--r-md); padding: 12px 16px; margin-bottom: 1.5rem;">
                        <p style="font-size: 13px; color: #2e7d32;">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div style="background: #ffebee; border: 1px solid #ffcdd2; border-radius: var(--r-md); padding: 12px 16px; margin-bottom: 1.5rem;">
                        <p style="font-size: 13px; font-weight: 600; color: var(--red-500); margin-bottom: 6px;">Пожалуйста, исправьте ошибки:</p>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                            @foreach ($errors->all() as $error)
                                <li style="font-size: 13px; color: var(--red-500);">• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" id="course-form">
                    @csrf

                    {{-- Title --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label for="title" style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                            Название курса <span style="color: var(--red-400);">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Например: Введение в программирование"
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0, 181, 165, 0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                    </div>

                    {{-- Image / Pattern --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Изображение</label>

                        <div id="pattern-preview" style="width: 100%; height: 128px; border-radius: var(--r-md); margin-bottom: 10px; overflow: hidden; background: var(--gray-100); display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border);">
                            <span id="preview-placeholder" style="font-size: 13px; color: var(--color-text-muted);">Обложка появится здесь</span>
                        </div>

                        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                            <button type="button" id="generate-pattern" class="btn btn-primary" style="font-size: 13px; padding: 7px 14px;">
                                 Случайная обложка
                            </button>
                            <button type="button" id="clear-pattern" class="btn btn-ghost" style="font-size: 13px; padding: 7px 14px; display: none;">
                                 Убрать
                            </button>
                        </div>

                        <input type="file" name="image_path" id="image_input" accept="image/*" style="width: 100%; font-size: 13px; color: var(--color-text-muted); font-family: var(--font-body);">
                        <input type="file" name="generated_pattern" id="generated_pattern_input" style="display:none">
                        <input type="hidden" name="use_generated_pattern" id="use_generated_pattern" value="0">
                    </div>

                    {{-- Period --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Доступен с:</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="datetime-local" name="period_start"
                                value="{{ now()->format('Y-m-d\TH:i') }}"
                                style="flex: 1; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                                onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                            <button type="button" onclick="setToday(this.previousElementSibling)"
                                style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border: 1px solid var(--color-border); border-radius: var(--r-md); background: var(--color-surface); font-size: 12px; color: var(--teal-600); cursor: pointer; font-family: var(--font-body); white-space: nowrap; transition: border-color 0.2s, background 0.2s, color 0.2s;"
                                onmouseover="this.style.borderColor='var(--teal-400)'; this.style.background='var(--teal-50)'; this.style.color='var(--teal-700)'"
                                onmouseout="this.style.borderColor='var(--color-border)'; this.style.background='var(--color-surface)'; this.style.color='var(--teal-600)'">Сегодня</button>
                        </div>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Доступен до:</label>
                        <input type="datetime-local" name="period_end"
                            value="{{ now()->format('Y-m-d\TH:i') }}"
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Groups --}}
                    @if (isset($groups) && $groups->count() > 0)
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px;">Доступен группам</label>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 10px;">Выберите группы, которым будет открыт курс</p>

                            {{-- Trigger button --}}
                            <button type="button" onclick="openGroupModal()" style="
                                display: inline-flex; align-items: center; gap: 8px;
                                padding: 9px 16px;
                                border: 1px solid var(--color-border);
                                border-radius: var(--r-md);
                                background: var(--color-surface);
                                font-size: 13px; font-weight: 600;
                                color: var(--gray-700);
                                cursor: pointer;
                                font-family: var(--font-body);
                                transition: border-color 0.2s, background 0.2s;
                            "
                            onmouseover="this.style.borderColor='var(--teal-400)'; this.style.background='var(--teal-50)'; this.style.color='var(--teal-700)'"
                            onmouseout="this.style.borderColor='var(--color-border)'; this.style.background='var(--color-surface)'; this.style.color='var(--gray-700)'">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                Настроить видимость групп
                                <span id="group-badge" style="display: none; background: var(--teal-500); color: #fff; font-size: 11px; font-weight: 700; border-radius: 999px; padding: 1px 7px; line-height: 18px;"></span>
                            </button>

                            {{-- Summary of selected groups (shown below button after picking) --}}
                            <div id="group-summary" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;"></div>

                            {{-- Hidden form inputs injected by JS on apply --}}
                            <div id="group-hidden-inputs"></div>
                        </div>
                    @else
                        <div style="margin-bottom: 1.5rem; padding: 12px 14px; background: var(--gray-50); border-radius: var(--r-md); border: 1px solid var(--color-border);">
                            <p style="font-size: 13px; color: var(--color-text-muted);">Пока нет созданных групп. <a href="{{ route('admin.groups.create') }}" style="color: var(--teal-600); font-weight: 600; text-decoration: none;">Создать группу</a></p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Сохранить
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-ghost">Отмена</a>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>

{{-- ── Group modal ─────────────────────────────────────────────────────────── --}}
@if (isset($groups) && $groups->count() > 0)
<div id="group-modal-overlay" style="
    display: none;
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(0,0,0,0.4);
    align-items: center;
    justify-content: center;
    padding: 1rem;
" onclick="handleOverlayClick(event)">
    <div style="
        background: var(--color-surface);
        border-radius: var(--r-lg, 12px);
        border: 1px solid var(--color-border);
        width: 100%; max-width: 520px;
        max-height: 90vh;
        display: flex; flex-direction: column;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        overflow: hidden;
    " onclick="event.stopPropagation()">

        {{-- Modal header --}}
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: flex-start; justify-content: space-between; flex-shrink: 0;">
            <div>
                <p style="font-size: 15px; font-weight: 700; color: var(--color-text-primary); margin: 0 0 2px;">Видимость по группам</p>
                <p style="font-size: 12px; color: var(--color-text-muted); margin: 0;">Выберите группы и при необходимости задайте отдельные даты</p>
            </div>
            <button type="button" onclick="closeGroupModal()" style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 2px; margin-left: 8px; line-height: 1;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Search --}}
        <div style="padding: 10px 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--color-text-muted); flex-shrink: 0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input
                type="text"
                id="modal-group-search"
                placeholder="Поиск группы..."
                oninput="filterModalGroups(this.value)"
                style="border: none; outline: none; background: transparent; font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); width: 100%; padding: 0;"
            >
        </div>

        {{-- Select all / deselect --}}
        <div style="padding: 8px 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
            <span id="modal-count-label" style="font-size: 12px; color: var(--color-text-muted);">Выбрано: 0</span>
            <div style="display: flex; gap: 8px;">
                <button type="button" onclick="selectAllVisible()" style="font-size: 12px; background: none; border: none; cursor: pointer; color: var(--teal-600); font-weight: 600; font-family: var(--font-body); padding: 0;">Выбрать все</button>
                <span style="color: var(--color-border);">·</span>
                <button type="button" onclick="deselectAll()" style="font-size: 12px; background: none; border: none; cursor: pointer; color: var(--color-text-muted); font-family: var(--font-body); padding: 0;">Сбросить</button>
            </div>
        </div>

        {{-- Group list --}}
        <div id="modal-group-list" style="overflow-y: auto; flex: 1; padding: 6px 0;">
            {{-- rendered by JS --}}
        </div>

        {{-- Footer --}}
        <div style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-shrink: 0;">
            <button type="button" onclick="closeGroupModal()" class="btn btn-ghost" style="font-size: 13px; padding: 8px 16px;">Отмена</button>
            <button type="button" onclick="applyGroupModal()" class="btn btn-primary" style="font-size: 13px; padding: 8px 20px;">Применить</button>
        </div>
    </div>
</div>
@endif

<script src="{{ asset('js/trianglify.bundle.js') }}"></script>
<script>
function setToday(el) {
    var d = new Date();
    el.value = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0') + 'T' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
}

// ── Pattern generator ──────────────────────────────────────────────────────────
(function() {
    const useGeneratedInput = document.getElementById('use_generated_pattern');
    const imageInput        = document.getElementById('image_input');
    const clearBtn          = document.getElementById('clear-pattern');
    const form              = document.getElementById('course-form');
    let patternBlob = null;

    const PALETTES = [
        ['#0b132b','#1c2541','#3a506b'],
        ['#03045e','#0077b6','#90e0ef'],
        ['#001f3f','#005f99','#66c2ff'],
        ['#14213d','#274c77','#6096ba'],
        ['#0f4c5c','#1b9aaa','#b2dbbf'],
        ['#081c15','#1b4332','#52b788'],
        ['#0b3d20','#2d6a4f','#95d5b2'],
        ['#1f4037','#2c7744','#90ee90'],
        ['#004b23','#006400','#38b000'],
        ['#2b9348','#55a630','#80b918'],
        ['#370617','#9d0208','#dc2f02'],
        ['#540b0e','#9e2a2b','#e09f3e'],
        ['#641220','#a4161a','#ff4d6d'],
        ['#7f1d1d','#b91c1c','#ef4444'],
        ['#450920','#a53860','#f4a7bb'],
        ['#7c2d12','#ea580c','#fdba74'],
        ['#552200','#aa5500','#ff9900'],
        ['#8d5524','#c68642','#e0ac69'],
        ['#ff6f00','#ff8f00','#ffd180'],
        ['#bc6c25','#dda15e','#ffe6a7'],
        ['#fffde7','#fff9c4','#fff176'],
        ['#f48c06','#ffba08','#ffe066'],
        ['#ffdd00','#ffd60a','#fff3b0'],
        ['#e09f3e','#f2cc8f','#fff3bf'],
        ['#c9a227','#ffd700','#fff4b5'],
        ['#240046','#5a189a','#9d4edd'],
        ['#3c096c','#7b2d8b','#c77dff'],
        ['#4a148c','#6a1b9a','#ba68c8'],
        ['#2e1065','#7c3aed','#c4b5fd'],
        ['#5b2a86','#9163cb','#d6c6ff'],
        ['#ff006e','#fb5607','#ffbe0b'],
        ['#f72585','#b5179e','#7209b7'],
        ['#ff5d8f','#ff99c8','#ffe5ec'],
        ['#d63384','#f06595','#faa2c1'],
        ['#c9184a','#ff4d6d','#ffb3c1'],
        ['#004e64','#00a5cf','#9fffcb'],
        ['#006466','#065a60','#0b525b'],
        ['#003049','#00b4d8','#90e0ef'],
        ['#005f73','#0a9396','#94d2bd'],
        ['#0a2239','#53a2be','#bcd4de'],
        ['#0d0d0d','#1a1a1a','#2d2d2d'],
        ['#1f1f1f','#3d3d3d','#b0b0b0'],
        ['#2b2d42','#8d99ae','#edf2f4'],
        ['#111827','#374151','#d1d5db'],
        ['#22223b','#4a4e69','#c9ada7'],
        ['#ffd6e7','#ffafcc','#bde0fe'],
        ['#cdb4db','#ffc8dd','#ffafcc'],
        ['#d8f3dc','#b7e4c7','#74c69d'],
        ['#fff1e6','#fde2e4','#fad2e1'],
        ['#e2ece9','#bee1e6','#f0efeb'],
        ['#ff4e50','#f9d423','#fc913a'],
        ['#ee0979','#ff6a00','#ffca28'],
        ['#ff6b6b','#feca57','#ff9f43'],
        ['#ff7b00','#ff8800','#ffd000'],
        ['#ef476f','#ffd166','#06d6a0'],
    ];

    function randomPattern() {
        const palette  = PALETTES[Math.floor(Math.random() * PALETTES.length)];
        const shuffled = palette.sort(() => Math.random() - 0.5);
        const cellSize = Math.floor(Math.random() * 120) + 40;
        const variance = Math.random() * 0.9 + 0.1;
        return window.trianglify({
            width: 600, height: 200,
            cellSize, variance,
            xColors: shuffled,
            yColors: Math.random() > 0.4 ? 'match' : shuffled.slice().reverse(),
        });
    }

    function drawPattern() {
        const pattern = randomPattern();
        const c = pattern.toCanvas();
        c.style.width  = '100%';
        c.style.height = '100%';
        const preview = document.getElementById('pattern-preview');
        preview.innerHTML = '';
        preview.appendChild(c);
        clearBtn.style.display  = 'inline-flex';
        useGeneratedInput.value = '1';
        c.toBlob(blob => { patternBlob = blob; }, 'image/png');
    }

    drawPattern();

    document.getElementById('generate-pattern').addEventListener('click', drawPattern);

    clearBtn.addEventListener('click', function() {
        const preview = document.getElementById('pattern-preview');
        preview.innerHTML = '<span id="preview-placeholder" style="font-size:13px;color:var(--color-text-muted);">Обложка появится здесь</span>';
        clearBtn.style.display  = 'none';
        useGeneratedInput.value = '0';
        patternBlob = null;
    });

    imageInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            clearBtn.style.display  = 'none';
            useGeneratedInput.value = '0';
            patternBlob = null;
        }
    });

    form.addEventListener('submit', function(e) {
        if (useGeneratedInput.value === '1' && patternBlob && !imageInput.files.length) {
            e.preventDefault();
            const file = new File([patternBlob], 'pattern.png', { type: 'image/png' });
            const dt   = new DataTransfer();
            dt.items.add(file);
            imageInput.files = dt.files;
            form.submit();
        }
    });
})();

// ── Group modal ────────────────────────────────────────────────────────────────
@if (isset($groups) && $groups->count() > 0)
const ALL_GROUPS = @json($groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values());

// State: { groupId: { selected: bool, start: string, end: string } }
const groupState = {};
ALL_GROUPS.forEach(g => {
    groupState[g.id] = { selected: false, start: '', end: '' };
});

// Which groups are expanded (dates shown)
const expandedGroups = new Set();

function openGroupModal() {
    document.getElementById('group-modal-overlay').style.display = 'flex';
    document.getElementById('modal-group-search').value = '';
    renderModalList('');
    document.body.style.overflow = 'hidden';
}

function closeGroupModal() {
    document.getElementById('group-modal-overlay').style.display = 'none';
    document.body.style.overflow = '';
}

function handleOverlayClick(e) {
    if (e.target === document.getElementById('group-modal-overlay')) {
        closeGroupModal();
    }
}

function filterModalGroups(query) {
    renderModalList(query);
}

function renderModalList(query) {
    const q = query.toLowerCase().trim();
    const filtered = q ? ALL_GROUPS.filter(g => g.name.toLowerCase().includes(q)) : ALL_GROUPS;
    const list = document.getElementById('modal-group-list');

    if (!filtered.length) {
        list.innerHTML = '<p style="font-size: 13px; color: var(--color-text-muted); text-align: center; padding: 1.5rem;">Ничего не найдено</p>';
        return;
    }

    list.innerHTML = filtered.map(g => {
        const state   = groupState[g.id];
        const isExp   = expandedGroups.has(g.id);
        const checked = state.selected ? 'checked' : '';

        return `
        <div style="padding: 0 1.25rem;">
            <div style="
                display: flex; align-items: center; gap: 10px;
                padding: 9px 0;
                border-bottom: 1px solid var(--color-border);
                cursor: pointer;
            " onclick="toggleModalGroup(${g.id})">
                <input
                    type="checkbox"
                    ${checked}
                    onclick="event.stopPropagation(); toggleModalGroup(${g.id})"
                    style="width: 15px; height: 15px; cursor: pointer; accent-color: var(--teal-500); flex-shrink: 0;"
                >
                <span style="font-size: 14px; flex: 1; color: var(--color-text-primary); user-select: none;">${g.name}</span>
                ${state.selected ? `
                <button
                    type="button"
                    onclick="event.stopPropagation(); toggleExpandDates(${g.id})"
                    style="
                        display: inline-flex; align-items: center; gap: 5px;
                        padding: 3px 9px;
                        border-radius: 999px;
                        border: 1px solid ${isExp ? 'var(--teal-400)' : 'var(--color-border)'};
                        background: ${isExp ? 'var(--teal-50)' : 'transparent'};
                        color: ${isExp ? 'var(--teal-700)' : 'var(--color-text-muted)'};
                        font-size: 12px; font-weight: 500;
                        cursor: pointer;
                        font-family: var(--font-body);
                        white-space: nowrap;
                        transition: all 0.15s;
                    "
                >
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    ${(state.start || state.end) ? 'Даты ' : 'Даты'}
                </button>
                ` : ''}
            </div>
            ${state.selected && isExp ? `
            <div style="
                padding: 10px 0 12px;
                display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
            ">
                <div>
                    <label style="display: block; font-size: 12px; color: var(--color-text-muted); margin-bottom: 5px;">Открыть с:</label>
                    <div style="display: flex; gap: 6px;">
                        <input
                            type="datetime-local"
                            value="${state.start}"
                            onchange="setGroupDate(${g.id}, 'start', this.value)"
                            style="flex: 1; padding: 7px 10px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 13px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                        <button type="button" onclick="setToday(this.previousElementSibling); setGroupDate(${g.id},'start',this.previousElementSibling.value)"
                            style="display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border:1px solid var(--color-border);border-radius:var(--r-md);background:var(--color-surface);font-size:11px;color:var(--teal-600);cursor:pointer;font-family:var(--font-body);white-space:nowrap;transition:border-color 0.2s,background 0.2s,color 0.2s;"
                            onmouseover="this.style.borderColor='var(--teal-400)';this.style.background='var(--teal-50)';this.style.color='var(--teal-700)'"
                            onmouseout="this.style.borderColor='var(--color-border)';this.style.background='var(--color-surface)';this.style.color='var(--teal-600)'">Сегодня</button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: var(--color-text-muted); margin-bottom: 5px;">Закрыть до:</label>
                    <input
                        type="datetime-local"
                        value="${state.end}"
                        onchange="setGroupDate(${g.id}, 'end', this.value)"
                        style="width: 100%; padding: 7px 10px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 13px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); outline: none; box-sizing: border-box;"
                        onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                        onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                    >
                </div>
                <p style="grid-column: 1/-1; font-size: 11px; color: var(--color-text-muted); margin: 0;">Оставьте пустым — будут использоваться общие даты курса</p>
            </div>
            ` : ''}
        </div>
        `;
    }).join('');

    updateModalCount();
}

function toggleModalGroup(id) {
    groupState[id].selected = !groupState[id].selected;
    if (!groupState[id].selected) {
        expandedGroups.delete(id);
    }
    renderModalList(document.getElementById('modal-group-search').value);
}

function toggleExpandDates(id) {
    if (expandedGroups.has(id)) expandedGroups.delete(id);
    else expandedGroups.add(id);
    renderModalList(document.getElementById('modal-group-search').value);
}

function setGroupDate(id, field, value) {
    groupState[id][field] = value;
}

function selectAllVisible() {
    const q = document.getElementById('modal-group-search').value.toLowerCase().trim();
    const filtered = q ? ALL_GROUPS.filter(g => g.name.toLowerCase().includes(q)) : ALL_GROUPS;
    filtered.forEach(g => { groupState[g.id].selected = true; });
    renderModalList(document.getElementById('modal-group-search').value);
}

function deselectAll() {
    ALL_GROUPS.forEach(g => {
        groupState[g.id].selected = false;
        expandedGroups.delete(g.id);
    });
    renderModalList(document.getElementById('modal-group-search').value);
}

function updateModalCount() {
    const n = ALL_GROUPS.filter(g => groupState[g.id].selected).length;
    document.getElementById('modal-count-label').textContent = `Выбрано: ${n} ${pluralGroup(n)}`;
}

function pluralGroup(n) {
    if (n % 10 === 1 && n % 100 !== 11) return 'группа';
    if ([2,3,4].includes(n % 10) && ![12,13,14].includes(n % 100)) return 'группы';
    return 'групп';
}

function applyGroupModal() {
    const selected = ALL_GROUPS.filter(g => groupState[g.id].selected);

    // Update badge on trigger button
    const badge = document.getElementById('group-badge');
    if (selected.length > 0) {
        badge.textContent = selected.length;
        badge.style.display = 'inline';
    } else {
        badge.style.display = 'none';
    }

    // Update summary chips below button
    const summary = document.getElementById('group-summary');
    summary.innerHTML = selected.map(g => {
        const s = groupState[g.id];
        const hasDates = s.start || s.end;
        return `
        <span style="
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid ${hasDates ? 'var(--teal-400)' : 'var(--color-border)'};
            background: ${hasDates ? 'var(--teal-50)' : 'var(--color-surface)'};
            font-size: 12px;
            color: ${hasDates ? 'var(--teal-700)' : 'var(--gray-600)'};
        ">
            ${hasDates ? `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>` : ''}
            ${g.name}
        </span>
        `;
    }).join('');

    // Inject hidden inputs for form submission
    const container = document.getElementById('group-hidden-inputs');
    container.innerHTML = '';
    selected.forEach(g => {
        const s = groupState[g.id];

        const cbInput = document.createElement('input');
        cbInput.type  = 'hidden';
        cbInput.name  = 'groups[]';
        cbInput.value = g.id;
        container.appendChild(cbInput);

        if (s.start) {
            const startInput = document.createElement('input');
            startInput.type  = 'hidden';
            startInput.name  = `group_period_start[${g.id}]`;
            startInput.value = s.start;
            container.appendChild(startInput);
        }

        if (s.end) {
            const endInput = document.createElement('input');
            endInput.type  = 'hidden';
            endInput.name  = `group_period_end[${g.id}]`;
            endInput.value = s.end;
            container.appendChild(endInput);
        }
    });

    closeGroupModal();
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeGroupModal();
});
@endif
</script>

<style>
@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeSlideOut {
    from { opacity: 1; transform: translateY(0); }
    to   { opacity: 0; transform: translateY(-6px); }
}
</style>

</body>
</html>
