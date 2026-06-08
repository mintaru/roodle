<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать курс</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
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
        <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            К курсу
        </a>
    </aside>

    <main class="main">

        <nav style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
            <a href="{{ route('home') }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <a href="{{ route('courses.show', $course) }}" style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--teal-600)'" onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span style="color: var(--gray-600); font-weight: 500;">Редактировать</span>
        </nav>

        <div class="page-header">
            <h1 class="page-header__title">Редактировать курс</h1>
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

                <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data" id="course-form">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label for="title" style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                            Название курса <span style="color: var(--red-400);">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $course->title) }}"
                            placeholder="Например: Введение в программирование"
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0, 181, 165, 0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'"
                        >
                    </div>

                    {{-- Image / Pattern --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Изображение</label>

                        {{-- Current image (if exists) --}}
                        @if ($course->image_path)
                            <div style="margin-bottom: 10px;">
                                <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 6px;">Текущее изображение:</p>
                                <img src="{{ asset('storage/' . $course->image_path) }}" alt=""
                                    style="width: 96px; height: 96px; object-fit: cover; border-radius: var(--r-md); border: 1px solid var(--color-border);">
                            </div>
                        @endif

                        <div id="pattern-preview" style="width: 100%; height: 128px; border-radius: var(--r-md); margin-bottom: 10px; overflow: hidden; background: var(--gray-100); display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border);">
                            <span id="preview-placeholder" style="font-size: 13px; color: var(--color-text-muted);">Паттерн появится здесь</span>
                        </div>

                        <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                            <button type="button" id="generate-pattern" class="btn btn-primary" style="font-size: 13px; padding: 7px 14px;">
                                🎲 Случайный паттерн
                            </button>
                            <button type="button" id="clear-pattern" class="btn btn-ghost" style="font-size: 13px; padding: 7px 14px; display: none;">
                                ✕ Убрать
                            </button>
                        </div>

                        <input type="file" name="image_path" id="image_input" accept="image/*" style="width: 100%; font-size: 13px; color: var(--color-text-muted); font-family: var(--font-body);">
                        <input type="file" name="generated_pattern" id="generated_pattern_input" style="display:none">
                        <input type="hidden" name="use_generated_pattern" id="use_generated_pattern" value="0">
                    </div>

                    {{-- Period --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Доступен с:</label>
                        <input type="datetime-local" name="period_start"
                            value="{{ old('period_start', $course->formatPeriodForInput('period_start')) }}"
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Доступен до:</label>
                        <input type="datetime-local" name="period_end"
                            value="{{ old('period_end', $course->formatPeriodForInput('period_end')) }}"
                            style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Groups --}}
                    @if (isset($groups) && $groups->count() > 0)
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px;">Доступен группам</label>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px;">Выберите группы и при необходимости задайте своё время открытия/закрытия</p>

                            {{-- Group chips --}}
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;" id="group-chips">
                                @foreach ($groups as $group)
                                    @php
                                        $pivot = $course->groups->firstWhere('id', $group->id)?->pivot;
                                        $isSelected = $course->groups->contains($group->id);
                                        $pivotStart = $pivot && $pivot->period_start
                                            ? \Carbon\Carbon::parse($pivot->period_start, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i')
                                            : '';
                                        $pivotEnd = $pivot && $pivot->period_end
                                            ? \Carbon\Carbon::parse($pivot->period_end, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i')
                                            : '';
                                    @endphp
                                    <button
                                        type="button"
                                        data-group-id="{{ $group->id }}"
                                        data-group-name="{{ $group->name }}"
                                        data-pivot-start="{{ $pivotStart }}"
                                        data-pivot-end="{{ $pivotEnd }}"
                                        onclick="toggleGroup({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ $pivotStart }}', '{{ $pivotEnd }}')"
                                        id="chip-{{ $group->id }}"
                                        class="{{ $isSelected ? 'chip-active' : '' }}"
                                        style="
                                            display: inline-flex; align-items: center; gap: 6px;
                                            padding: 6px 12px;
                                            border-radius: 999px;
                                            border: 1.5px solid {{ $isSelected ? 'var(--teal-400)' : 'var(--color-border)' }};
                                            background: {{ $isSelected ? 'var(--teal-50)' : 'var(--color-surface)' }};
                                            font-size: 13px; font-weight: 500;
                                            color: {{ $isSelected ? 'var(--teal-700)' : 'var(--gray-600)' }};
                                            cursor: pointer;
                                            transition: all 0.15s;
                                            font-family: var(--font-body);
                                        "
                                        onmouseover="if(!this.classList.contains('chip-active')) { this.style.borderColor='var(--teal-300)'; this.style.color='var(--teal-700)'; }"
                                        onmouseout="if(!this.classList.contains('chip-active')) { this.style.borderColor='var(--color-border)'; this.style.color='var(--gray-600)'; }"
                                    >
                                        <svg width="12" height="12" id="chip-icon-{{ $group->id }}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            @if ($isSelected)
                                                <path d="M20 6L9 17l-5-5"/>
                                            @else
                                                <path d="M12 5v14M5 12h14"/>
                                            @endif
                                        </svg>
                                        {{ $group->name }}
                                    </button>
                                    <input type="checkbox" name="groups[]" value="{{ $group->id }}" id="checkbox-{{ $group->id }}" style="display:none" @if($isSelected) checked @endif>
                                @endforeach
                            </div>

                            {{-- Date pickers per selected group --}}
                            <div id="group-dates-container" style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach ($groups as $group)
                                    @php
                                        $pivot = $course->groups->firstWhere('id', $group->id)?->pivot;
                                        $isSelected = $course->groups->contains($group->id);
                                        $pivotStart = $pivot && $pivot->period_start
                                            ? \Carbon\Carbon::parse($pivot->period_start, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i')
                                            : '';
                                        $pivotEnd = $pivot && $pivot->period_end
                                            ? \Carbon\Carbon::parse($pivot->period_end, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i')
                                            : '';
                                    @endphp
                                    @if ($isSelected)
                                        <div id="date-row-{{ $group->id }}" style="border:1px solid var(--color-border); border-radius:var(--r-md); padding:14px 16px; background:var(--teal-50);">
                                            <p style="font-size:13px; font-weight:600; color:var(--teal-700); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                                {{ $group->name }}
                                            </p>
                                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                                <div>
                                                    <label style="display:block; font-size:12px; color:var(--color-text-muted); margin-bottom:5px;">Открыть с:</label>
                                                    <input type="datetime-local" name="group_period_start[{{ $group->id }}]"
                                                        value="{{ $pivotStart }}"
                                                        onchange="checkPastDate(this)"
                                                        style="width:100%; padding:8px 10px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:13px; font-family:var(--font-body); color:var(--color-text-primary); background:var(--color-surface); outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                                                        onfocus="this.style.borderColor='var(--teal-400)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                                        onblur="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'">
                                                </div>
                                                <div>
                                                    <label style="display:block; font-size:12px; color:var(--color-text-muted); margin-bottom:5px;">Закрыть до:</label>
                                                    <input type="datetime-local" name="group_period_end[{{ $group->id }}]"
                                                        value="{{ $pivotEnd }}"
                                                        style="width:100%; padding:8px 10px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:13px; font-family:var(--font-body); color:var(--color-text-primary); background:var(--color-surface); outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                                                        onfocus="this.style.borderColor='var(--teal-400)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                                        onblur="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'">
                                                </div>
                                            </div>
                                            <p style="font-size:11px; color:var(--color-text-muted); margin-top:8px;">Оставьте пустым — будут использоваться общие даты курса</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Обновить
                        </button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-ghost">Отмена</a>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>

<script src="https://unpkg.com/trianglify@4/dist/trianglify.bundle.js"></script>
<script>
// ── Past date validation ───────────────────────────────────────────────────────
function checkPastDate(input) {
    if (!input.value) return;
    const selected = new Date(input.value);
    const now = new Date();
    if (selected < now) {
        alert('Нельзя выбрать дату в прошлом');
        input.value = '';
    }
}

// ── Pattern generator ──────────────────────────────────────────────────────────
(function() {
    const useGeneratedInput = document.getElementById('use_generated_pattern');
    const imageInput        = document.getElementById('image_input');
    const clearBtn          = document.getElementById('clear-pattern');
    const form              = document.getElementById('course-form');
    let patternBlob = null;

const PALETTES = [
    // ===== BLUE =====
    ['#0b132b','#1c2541','#3a506b'],
    ['#03045e','#0077b6','#90e0ef'],
    ['#001f3f','#005f99','#66c2ff'],
    ['#14213d','#274c77','#6096ba'],
    ['#0f4c5c','#1b9aaa','#b2dbbf'],

    // ===== GREEN =====
    ['#081c15','#1b4332','#52b788'],
    ['#0b3d20','#2d6a4f','#95d5b2'],
    ['#1f4037','#2c7744','#90ee90'],
    ['#004b23','#006400','#38b000'],
    ['#2b9348','#55a630','#80b918'],

    // ===== RED =====
    ['#370617','#9d0208','#dc2f02'],
    ['#540b0e','#9e2a2b','#e09f3e'],
    ['#641220','#a4161a','#ff4d6d'],
    ['#7f1d1d','#b91c1c','#ef4444'],
    ['#450920','#a53860','#f4a7bb'],

    // ===== ORANGE =====
    ['#7c2d12','#ea580c','#fdba74'],
    ['#552200','#aa5500','#ff9900'],
    ['#8d5524','#c68642','#e0ac69'],
    ['#ff6f00','#ff8f00','#ffd180'],
    ['#bc6c25','#dda15e','#ffe6a7'],

    // ===== YELLOW =====
    ['#fffde7','#fff9c4','#fff176'],
    ['#f48c06','#ffba08','#ffe066'],
    ['#ffdd00','#ffd60a','#fff3b0'],
    ['#e09f3e','#f2cc8f','#fff3bf'],
    ['#c9a227','#ffd700','#fff4b5'],

    // ===== PURPLE =====
    ['#240046','#5a189a','#9d4edd'],
    ['#3c096c','#7b2d8b','#c77dff'],
    ['#4a148c','#6a1b9a','#ba68c8'],
    ['#2e1065','#7c3aed','#c4b5fd'],
    ['#5b2a86','#9163cb','#d6c6ff'],

    // ===== PINK =====
    ['#ff006e','#fb5607','#ffbe0b'],
    ['#f72585','#b5179e','#7209b7'],
    ['#ff5d8f','#ff99c8','#ffe5ec'],
    ['#d63384','#f06595','#faa2c1'],
    ['#c9184a','#ff4d6d','#ffb3c1'],

    // ===== CYAN / TEAL =====
    ['#004e64','#00a5cf','#9fffcb'],
    ['#006466','#065a60','#0b525b'],
    ['#003049','#00b4d8','#90e0ef'],
    ['#005f73','#0a9396','#94d2bd'],
    ['#0a2239','#53a2be','#bcd4de'],

    // ===== NEUTRAL / GRAY =====
    ['#0d0d0d','#1a1a1a','#2d2d2d'],
    ['#1f1f1f','#3d3d3d','#b0b0b0'],
    ['#2b2d42','#8d99ae','#edf2f4'],
    ['#111827','#374151','#d1d5db'],
    ['#22223b','#4a4e69','#c9ada7'],

    // ===== PASTEL =====
    ['#ffd6e7','#ffafcc','#bde0fe'],
    ['#cdb4db','#ffc8dd','#ffafcc'],
    ['#d8f3dc','#b7e4c7','#74c69d'],
    ['#fff1e6','#fde2e4','#fad2e1'],
    ['#e2ece9','#bee1e6','#f0efeb'],

    // ===== SUNSET =====
    ['#ff4e50','#f9d423','#fc913a'],
    ['#ee0979','#ff6a00','#ffca28'],
    ['#ff6b6b','#feca57','#ff9f43'],
    ['#ff7b00','#ff8800','#ffd000'],
    ['#ef476f','#ffd166','#06d6a0'],
];

    function randomPattern() {
        const palette  = PALETTES[Math.floor(Math.random() * PALETTES.length)];
        const shuffled = palette.slice().sort(() => Math.random() - 0.5);
        const cellSize = Math.floor(Math.random() * 120) + 40;
        const variance = Math.random() * 0.9 + 0.1;
        return window.trianglify({
            width:    600,
            height:   200,
            cellSize,
            variance,
            xColors:  shuffled,
            yColors:  Math.random() > 0.4 ? 'match' : shuffled.slice().reverse(),
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

    document.getElementById('generate-pattern').addEventListener('click', drawPattern);

    clearBtn.addEventListener('click', function() {
        const preview = document.getElementById('pattern-preview');
        preview.innerHTML = '<span id="preview-placeholder" style="font-size:13px;color:var(--color-text-muted);">Паттерн появится здесь</span>';
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

// ── Group chips ────────────────────────────────────────────────────────────────
const selectedGroups = new Set(
    Array.from(document.querySelectorAll('input[name="groups[]"]:checked')).map(el => parseInt(el.value))
);

function toggleGroup(id, name, pivotStart, pivotEnd) {
    if (selectedGroups.has(id)) {
        selectedGroups.delete(id);
        deactivateChip(id);
        removeDateRow(id);
        document.getElementById('checkbox-' + id).checked = false;
    } else {
        selectedGroups.add(id);
        activateChip(id);
        addDateRow(id, name, pivotStart, pivotEnd);
        document.getElementById('checkbox-' + id).checked = true;
    }
}

function activateChip(id) {
    const chip = document.getElementById('chip-' + id);
    chip.classList.add('chip-active');
    chip.style.borderColor = 'var(--teal-400)';
    chip.style.background  = 'var(--teal-50)';
    chip.style.color       = 'var(--teal-700)';
    const icon = document.getElementById('chip-icon-' + id);
    icon.setAttribute('viewBox', '0 0 24 24');
    icon.innerHTML = '<path d="M20 6L9 17l-5-5"/>';
}

function deactivateChip(id) {
    const chip = document.getElementById('chip-' + id);
    chip.classList.remove('chip-active');
    chip.style.borderColor = 'var(--color-border)';
    chip.style.background  = 'var(--color-surface)';
    chip.style.color       = 'var(--gray-600)';
    const icon = document.getElementById('chip-icon-' + id);
    icon.setAttribute('viewBox', '0 0 24 24');
    icon.innerHTML = '<path d="M12 5v14M5 12h14"/>';
}

function addDateRow(id, name, pivotStart, pivotEnd) {
    const container = document.getElementById('group-dates-container');
    const row = document.createElement('div');
    row.id = 'date-row-' + id;
    row.style.cssText = 'border:1px solid var(--color-border); border-radius:var(--r-md); padding:14px 16px; background:var(--teal-50); animation: fadeSlideIn 0.2s ease;';
    row.innerHTML = `
        <p style="font-size:13px; font-weight:600; color:var(--teal-700); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            ${name}
        </p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
                <label style="display:block; font-size:12px; color:var(--color-text-muted); margin-bottom:5px;">Открыть с:</label>
                <input type="datetime-local" name="group_period_start[${id}]"
                    value="${pivotStart || ''}"
                    onchange="checkPastDate(this)"
                    style="width:100%; padding:8px 10px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:13px; font-family:var(--font-body); color:var(--color-text-primary); background:var(--color-surface); outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                    onfocus="this.style.borderColor='var(--teal-400)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                    onblur="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'">
            </div>
            <div>
                <label style="display:block; font-size:12px; color:var(--color-text-muted); margin-bottom:5px;">Закрыть до:</label>
                <input type="datetime-local" name="group_period_end[${id}]"
                    value="${pivotEnd || ''}"
                    style="width:100%; padding:8px 10px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:13px; font-family:var(--font-body); color:var(--color-text-primary); background:var(--color-surface); outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                    onfocus="this.style.borderColor='var(--teal-400)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                    onblur="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'">
            </div>
        </div>
        <p style="font-size:11px; color:var(--color-text-muted); margin-top:8px;">Оставьте пустым — будут использоваться общие даты курса</p>
    `;
    container.appendChild(row);
}

function removeDateRow(id) {
    const row = document.getElementById('date-row-' + id);
    if (row) {
        row.style.animation = 'fadeSlideOut 0.15s ease forwards';
        setTimeout(() => row.remove(), 150);
    }
}
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
