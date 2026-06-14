<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать курс</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>

<body>

    @include('components.menu')

    <div class="layout">
        <aside class="sidebar">
            <p class="sidebar-section-title">Навигация</p>
            <a href="{{ route('home') }}" class="sidebar-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                Все курсы
            </a>
            <a href="{{ route('courses.show', $course) }}" class="sidebar-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                К курсу
            </a>
        </aside>

        <main class="main">

            <nav
                style="display: flex; align-items: center; gap: 8px; margin-bottom: 1.75rem; font-size: 13px; color: var(--color-text-muted);">
                <a href="{{ route('home') }}"
                    style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;"
                    onmouseover="this.style.color='var(--teal-600)'"
                    onmouseout="this.style.color='var(--color-text-muted)'">Курсы</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" />
                </svg>
                <a href="{{ route('courses.show', $course) }}"
                    style="color: var(--color-text-muted); text-decoration: none; transition: color 0.2s;"
                    onmouseover="this.style.color='var(--teal-600)'"
                    onmouseout="this.style.color='var(--color-text-muted)'">{{ $course->title }}</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" />
                </svg>
                <span style="color: var(--gray-600); font-weight: 500;">Редактировать</span>
            </nav>

            <div class="page-header">
                <h1 class="page-header__title">Редактировать курс</h1>
            </div>

            <div style="max-width: 600px;">
                <div class="panel" style="padding: 2rem;">

                    @if (session('success'))
                        <div
                            style="background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: var(--r-md); padding: 12px 16px; margin-bottom: 1.5rem;">
                            <p style="font-size: 13px; color: #2e7d32;">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div
                            style="background: #ffebee; border: 1px solid #ffcdd2; border-radius: var(--r-md); padding: 12px 16px; margin-bottom: 1.5rem;">
                            <p style="font-size: 13px; font-weight: 600; color: var(--red-500); margin-bottom: 6px;">
                                Пожалуйста, исправьте ошибки:</p>
                            <ul
                                style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                                @foreach ($errors->all() as $error)
                                    <li style="font-size: 13px; color: var(--red-500);">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data"
                        id="course-form">
                        @csrf
                        @method('PUT')

                        {{-- Title --}}
                        <div style="margin-bottom: 1.5rem;">
                            <label for="title"
                                style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">
                                Название курса <span style="color: var(--red-400);">*</span>
                            </label>
                            <input type="text" id="title" name="title"
                                value="{{ old('title', $course->title) }}"
                                placeholder="Например: Введение в программирование"
                                style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                                onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0, 181, 165, 0.1)'"
                                onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                        </div>

                        {{-- Image / Pattern --}}
                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Изображение</label>

                            {{-- Current image (if exists) --}}
                            @if ($course->image_path)
                                <div style="margin-bottom: 10px;">
                                    <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 6px;">
                                        Текущее изображение:</p>
                                    <img src="{{ asset('storage/' . $course->image_path) }}" alt=""
                                        style="width: 96px; height: 96px; object-fit: cover; border-radius: var(--r-md); border: 1px solid var(--color-border);">
                                </div>
                            @endif

                            <div id="pattern-preview"
                                style="width: 100%; height: 128px; border-radius: var(--r-md); margin-bottom: 10px; overflow: hidden; background: var(--gray-100); display: flex; align-items: center; justify-content: center; border: 1px solid var(--color-border);">
                                <span id="preview-placeholder"
                                    style="font-size: 13px; color: var(--color-text-muted);">Паттерн появится
                                    здесь</span>
                            </div>

                            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                                <button type="button" id="generate-pattern" class="btn btn-primary"
                                    style="font-size: 13px; padding: 7px 14px;">
                                    🎲 Случайный паттерн
                                </button>
                                <button type="button" id="clear-pattern" class="btn btn-ghost"
                                    style="font-size: 13px; padding: 7px 14px; display: none;">
                                    ✕ Убрать
                                </button>
                            </div>

                            <input type="file" name="image_path" id="image_input" accept="image/*"
                                style="width: 100%; font-size: 13px; color: var(--color-text-muted); font-family: var(--font-body);">
                            <input type="file" name="generated_pattern" id="generated_pattern_input"
                                style="display:none">
                            <input type="hidden" name="use_generated_pattern" id="use_generated_pattern"
                                value="0">
                        </div>

                        {{-- Period --}}
                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Доступен
                                с:</label>
                            <input type="datetime-local" name="period_start"
                                value="{{ old('period_start', $course->formatPeriodForInput('period_start')) }}"
                                style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                                onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px;">Доступен
                                до:</label>
                            <input type="datetime-local" name="period_end"
                                value="{{ old('period_end', $course->formatPeriodForInput('period_end')) }}"
                                style="width: 100%; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: border-color 0.2s, box-shadow 0.2s; outline: none; box-sizing: border-box;"
                                onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px;">Доступен
                                группам</label>
                            <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px;">Выберите
                                группы и при необходимости задайте своё время открытия/закрытия</p>

                            <button type="button" onclick="openCourseGroupsModal()"
                                style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border: 1px solid var(--color-border); border-radius: var(--r-md); background: var(--color-surface); font-size: 13px; font-weight: 600; color: var(--gray-700); cursor: pointer; font-family: var(--font-body); transition: border-color 0.2s, background 0.2s;"
                                onmouseover="this.style.borderColor='var(--teal-400)'; this.style.background='var(--teal-50)'; this.style.color='var(--teal-700)'"
                                onmouseout="this.style.borderColor='var(--color-border)'; this.style.background='var(--color-surface)'; this.style.color='var(--gray-700)'">
                                <svg width="15" height="15" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 00-3-3.87" />
                                    <path d="M16 3.13a4 4 0 010 7.75" />
                                </svg>
                                Выбрать группы
                                <span id="course-groups-badge"
                                    style="display: none; background: var(--teal-500); color: #fff; font-size: 11px; font-weight: 700; border-radius: 999px; padding: 1px 7px; line-height: 18px;"></span>
                            </button>

                            <div id="course-groups-summary"
                                style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;"></div>
                            <div id="course-groups-hidden-inputs"></div>
                        </div>

                        {{-- Actions --}}
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
                                Обновить
                            </button>
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-ghost">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <div id="course-groups-modal-overlay"
        style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; padding: 1rem;"
        onclick="if(event.target===this) closeCourseGroupsModal()">
        <div style="background: var(--color-surface); border-radius: var(--r-lg, 12px); border: 1px solid var(--color-border); width: 100%; max-width: 520px; max-height: 80vh; display: flex; flex-direction: column; box-shadow: 0 8px 32px rgba(0,0,0,0.12); overflow: hidden;"
            onclick="event.stopPropagation()">

            <div
                style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: flex-start; justify-content: space-between; flex-shrink: 0;">
                <div>
                    <p style="font-size: 15px; font-weight: 700; color: var(--color-text-primary); margin: 0 0 2px;">
                        Выбор групп</p>
                    <p style="font-size: 12px; color: var(--color-text-muted); margin: 0;">Отметьте группы и при
                        необходимости задайте своё время</p>
                </div>
                <button type="button" onclick="closeCourseGroupsModal()"
                    style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 2px; margin-left: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div
                style="padding: 10px 1.25rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" style="color: var(--color-text-muted); flex-shrink: 0;">
                    <circle cx="11" cy="11" r="8" />
                    <path d="M21 21l-4.35-4.35" />
                </svg>
                <input type="text" id="course-modal-group-search" placeholder="Поиск группы..."
                    oninput="filterCourseModalGroups(this.value)"
                    style="border: none; outline: none; background: transparent; font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); width: 100%; padding: 0;">
            </div>

            <div id="course-modal-group-list" style="overflow-y: auto; flex: 1; padding: 6px 0;"></div>

            <div
                style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-shrink: 0;">
                <button type="button" onclick="closeCourseGroupsModal()" class="btn btn-ghost"
                    style="font-size: 13px; padding: 8px 16px;">Отмена</button>
                <button type="button" onclick="applyCourseGroupsModal()" class="btn btn-primary"
                    style="font-size: 13px; padding: 8px 20px;">Применить</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/trianglify.bundle.js') }}"></script>

    <script>
        // ── Pattern generator ──────────────────────────────────────────────────────────
        (function() {
            const useGeneratedInput = document.getElementById('use_generated_pattern');
            const imageInput = document.getElementById('image_input');
            const clearBtn = document.getElementById('clear-pattern');
            const form = document.getElementById('course-form');
            let patternBlob = null;

            const PALETTES = [
                // ===== BLUE =====
                ['#0b132b', '#1c2541', '#3a506b'],
                ['#03045e', '#0077b6', '#90e0ef'],
                ['#001f3f', '#005f99', '#66c2ff'],
                ['#14213d', '#274c77', '#6096ba'],
                ['#0f4c5c', '#1b9aaa', '#b2dbbf'],

                // ===== GREEN =====
                ['#081c15', '#1b4332', '#52b788'],
                ['#0b3d20', '#2d6a4f', '#95d5b2'],
                ['#1f4037', '#2c7744', '#90ee90'],
                ['#004b23', '#006400', '#38b000'],
                ['#2b9348', '#55a630', '#80b918'],

                // ===== RED =====
                ['#370617', '#9d0208', '#dc2f02'],
                ['#540b0e', '#9e2a2b', '#e09f3e'],
                ['#641220', '#a4161a', '#ff4d6d'],
                ['#7f1d1d', '#b91c1c', '#ef4444'],
                ['#450920', '#a53860', '#f4a7bb'],

                // ===== ORANGE =====
                ['#7c2d12', '#ea580c', '#fdba74'],
                ['#552200', '#aa5500', '#ff9900'],
                ['#8d5524', '#c68642', '#e0ac69'],
                ['#ff6f00', '#ff8f00', '#ffd180'],
                ['#bc6c25', '#dda15e', '#ffe6a7'],

                // ===== YELLOW =====
                ['#fffde7', '#fff9c4', '#fff176'],
                ['#f48c06', '#ffba08', '#ffe066'],
                ['#ffdd00', '#ffd60a', '#fff3b0'],
                ['#e09f3e', '#f2cc8f', '#fff3bf'],
                ['#c9a227', '#ffd700', '#fff4b5'],

                // ===== PURPLE =====
                ['#240046', '#5a189a', '#9d4edd'],
                ['#3c096c', '#7b2d8b', '#c77dff'],
                ['#4a148c', '#6a1b9a', '#ba68c8'],
                ['#2e1065', '#7c3aed', '#c4b5fd'],
                ['#5b2a86', '#9163cb', '#d6c6ff'],

                // ===== PINK =====
                ['#ff006e', '#fb5607', '#ffbe0b'],
                ['#f72585', '#b5179e', '#7209b7'],
                ['#ff5d8f', '#ff99c8', '#ffe5ec'],
                ['#d63384', '#f06595', '#faa2c1'],
                ['#c9184a', '#ff4d6d', '#ffb3c1'],

                // ===== CYAN / TEAL =====
                ['#004e64', '#00a5cf', '#9fffcb'],
                ['#006466', '#065a60', '#0b525b'],
                ['#003049', '#00b4d8', '#90e0ef'],
                ['#005f73', '#0a9396', '#94d2bd'],
                ['#0a2239', '#53a2be', '#bcd4de'],

                // ===== NEUTRAL / GRAY =====
                ['#0d0d0d', '#1a1a1a', '#2d2d2d'],
                ['#1f1f1f', '#3d3d3d', '#b0b0b0'],
                ['#2b2d42', '#8d99ae', '#edf2f4'],
                ['#111827', '#374151', '#d1d5db'],
                ['#22223b', '#4a4e69', '#c9ada7'],

                // ===== PASTEL =====
                ['#ffd6e7', '#ffafcc', '#bde0fe'],
                ['#cdb4db', '#ffc8dd', '#ffafcc'],
                ['#d8f3dc', '#b7e4c7', '#74c69d'],
                ['#fff1e6', '#fde2e4', '#fad2e1'],
                ['#e2ece9', '#bee1e6', '#f0efeb'],

                // ===== SUNSET =====
                ['#ff4e50', '#f9d423', '#fc913a'],
                ['#ee0979', '#ff6a00', '#ffca28'],
                ['#ff6b6b', '#feca57', '#ff9f43'],
                ['#ff7b00', '#ff8800', '#ffd000'],
                ['#ef476f', '#ffd166', '#06d6a0'],
            ];

            function randomPattern() {
                const palette = PALETTES[Math.floor(Math.random() * PALETTES.length)];
                const shuffled = palette.slice().sort(() => Math.random() - 0.5);
                const cellSize = Math.floor(Math.random() * 120) + 40;
                const variance = Math.random() * 0.9 + 0.1;
                return window.trianglify({
                    width: 600,
                    height: 200,
                    cellSize,
                    variance,
                    xColors: shuffled,
                    yColors: Math.random() > 0.4 ? 'match' : shuffled.slice().reverse(),
                });
            }

            function drawPattern() {
                const pattern = randomPattern();
                const c = pattern.toCanvas();
                c.style.width = '100%';
                c.style.height = '100%';
                const preview = document.getElementById('pattern-preview');
                preview.innerHTML = '';
                preview.appendChild(c);
                clearBtn.style.display = 'inline-flex';
                useGeneratedInput.value = '1';
                c.toBlob(blob => {
                    patternBlob = blob;
                }, 'image/png');
            }

            document.getElementById('generate-pattern').addEventListener('click', drawPattern);

            clearBtn.addEventListener('click', function() {
                const preview = document.getElementById('pattern-preview');
                preview.innerHTML =
                    '<span id="preview-placeholder" style="font-size:13px;color:var(--color-text-muted);">Паттерн появится здесь</span>';
                clearBtn.style.display = 'none';
                useGeneratedInput.value = '0';
                patternBlob = null;
            });

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    clearBtn.style.display = 'none';
                    useGeneratedInput.value = '0';
                    patternBlob = null;
                }
            });

            form.addEventListener('submit', function(e) {
                if (useGeneratedInput.value === '1' && patternBlob && !imageInput.files.length) {
                    e.preventDefault();
                    const file = new File([patternBlob], 'pattern.png', {
                        type: 'image/png'
                    });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    imageInput.files = dt.files;
                    form.submit();
                }
            });
        })();

        @php
            $___initialCourseGroups = $course->groups->mapWithKeys(function ($group) {
                return [
                    $group->id => [
                        'checked' => true,
                        'period_start' => $group->pivot->period_start ? \Carbon\Carbon::parse($group->pivot->period_start, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '',
                        'period_end' => $group->pivot->period_end ? \Carbon\Carbon::parse($group->pivot->period_end, 'UTC')->setTimezone('Asia/Krasnoyarsk')->format('Y-m-d\TH:i') : '',
                    ],
                ];
            });
        @endphp

        // ── Course groups modal ────────────────────────────────────────────────────────
        const COURSE_GROUPS = @json($groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values());
        let selectedCourseGroups = @json($___initialCourseGroups);
        let tempCourseGroups = {};

        function openCourseGroupsModal() {
            tempCourseGroups = JSON.parse(JSON.stringify(selectedCourseGroups));
            document.getElementById('course-groups-modal-overlay').style.display = 'flex';
            document.getElementById('course-modal-group-search').value = '';
            renderCourseGroupList('');
            document.body.style.overflow = 'hidden';
        }

        function closeCourseGroupsModal() {
            document.getElementById('course-groups-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
        }

        function filterCourseModalGroups(query) {
            renderCourseGroupList(query);
        }

        function renderCourseGroupList(query) {
            const q = query.toLowerCase().trim();
            const filtered = q ? COURSE_GROUPS.filter(g => g.name.toLowerCase().includes(q)) : COURSE_GROUPS;
            const list = document.getElementById('course-modal-group-list');

            if (!filtered.length) {
                list.innerHTML =
                    '<p style="font-size: 13px; color: var(--color-text-muted); text-align: center; padding: 1.5rem;">Ничего не найдено</p>';
                return;
            }

            list.innerHTML = filtered.map(g => {
                const p = tempCourseGroups[g.id];
                const isSel = p?.checked;
                return `
            <div style="padding: 0 1.25rem; border-bottom: 1px solid var(--color-border);">
                <div style="display:flex;align-items:center;gap:10px;padding:10px 0;cursor:pointer;" onclick="toggleCourseGroup(${g.id})">
                    <div style="width:15px;height:15px;border-radius:3px;border:2px solid ${isSel ? 'var(--teal-500)' : 'var(--color-border)'};background:${isSel ? 'var(--teal-500)' : 'transparent'};flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                        ${isSel ? '<svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5"><path d="M2 6l3 3 5-5"/></svg>' : ''}
                    </div>
                    <span style="font-size:14px;flex:1;color:var(--color-text-primary);user-select:none;">${g.name}</span>
                </div>
                ${isSel ? `
                            <div style="margin-bottom:10px;margin-left:25px;display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label style="display:block; font-size:12px; color:var(--color-text-muted); margin-bottom:5px;">Открыть с:</label>
                                    <input type="datetime-local" value="${p.period_start || ''}"
                                        onchange="setCourseGroupPeriod(${g.id}, 'period_start', this.value); checkPastDate(this)"
                                        style="width:100%; padding:8px 10px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:13px; font-family:var(--font-body); color:var(--color-text-primary); background:var(--color-surface); outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                                        onfocus="this.style.borderColor='var(--teal-400)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                        onblur="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; color:var(--color-text-muted); margin-bottom:5px;">Закрыть до:</label>
                                    <input type="datetime-local" value="${p.period_end || ''}"
                                        onchange="setCourseGroupPeriod(${g.id}, 'period_end', this.value)"
                                        style="width:100%; padding:8px 10px; border:1px solid var(--color-border); border-radius:var(--r-md); font-size:13px; font-family:var(--font-body); color:var(--color-text-primary); background:var(--color-surface); outline:none; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                                        onfocus="this.style.borderColor='var(--teal-400)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                                        onblur="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'">
                                </div>
                                <p style="font-size:11px; color:var(--color-text-muted); margin-top:8px; grid-column: span 2;">Оставьте пустым — будут использоваться общие даты курса</p>
                            </div>` : ''}
            </div>`;
            }).join('');
        }

        function toggleCourseGroup(id) {
            if (tempCourseGroups[id]?.checked) {
                tempCourseGroups[id] = {
                    checked: false,
                    period_start: '',
                    period_end: ''
                };
            } else {
                const currentPivot = @json($___initialCourseGroups);
                tempCourseGroups[id] = {
                    checked: true,
                    period_start: currentPivot[id]?.period_start || '',
                    period_end: currentPivot[id]?.period_end || '',
                };
            }
            renderCourseGroupList(document.getElementById('course-modal-group-search').value);
        }

        function setCourseGroupPeriod(id, key, value) {
            if (tempCourseGroups[id]) tempCourseGroups[id][key] = value;
        }

        function applyCourseGroupsModal() {
            selectedCourseGroups = JSON.parse(JSON.stringify(tempCourseGroups));

            const hiddenContainer = document.getElementById('course-groups-hidden-inputs');
            const summaryContainer = document.getElementById('course-groups-summary');
            const badge = document.getElementById('course-groups-badge');

            hiddenContainer.innerHTML = '';
            summaryContainer.innerHTML = '';

            const selected = Object.entries(selectedCourseGroups).filter(([, v]) => v && v.checked);

            if (selected.length) {
                badge.textContent = selected.length;
                badge.style.display = 'inline';

                selected.forEach(([id, p]) => {
                    const group = COURSE_GROUPS.find(g => g.id == id);
                    if (group) {
                        summaryContainer.innerHTML += `
                    <span style="
                        display: inline-flex; align-items: center; gap: 5px;
                        padding: 4px 10px;
                        border-radius: 999px;
                        border: 1px solid var(--teal-400);
                        background: var(--teal-50);
                        font-size: 12px;
                        color: var(--teal-700);
                    ">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        ${group.name}
                    </span>`;

                        hiddenContainer.innerHTML += `
                    <input type="hidden" name="groups[]" value="${id}">
                    <input type="hidden" name="group_period_start[${id}]" value="${p.period_start || ''}">
                    <input type="hidden" name="group_period_end[${id}]" value="${p.period_end || ''}">
                `;
                    }
                });
            } else {
                badge.style.display = 'none';
            }

            closeCourseGroupsModal();
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (Object.keys(selectedCourseGroups).length > 0) {
                tempCourseGroups = JSON.parse(JSON.stringify(selectedCourseGroups));
                applyCourseGroupsModal();
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCourseGroupsModal();
                }
            });
        });

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
            const imageInput = document.getElementById('image_input');
            const clearBtn = document.getElementById('clear-pattern');
            const form = document.getElementById('course-form');
            let patternBlob = null;

            const PALETTES = [
                // ===== BLUE =====
                ['#0b132b', '#1c2541', '#3a506b'],
                ['#03045e', '#0077b6', '#90e0ef'],
                ['#001f3f', '#005f99', '#66c2ff'],
                ['#14213d', '#274c77', '#6096ba'],
                ['#0f4c5c', '#1b9aaa', '#b2dbbf'],

                // ===== GREEN =====
                ['#081c15', '#1b4332', '#52b788'],
                ['#0b3d20', '#2d6a4f', '#95d5b2'],
                ['#1f4037', '#2c7744', '#90ee90'],
                ['#004b23', '#006400', '#38b000'],
                ['#2b9348', '#55a630', '#80b918'],

                // ===== RED =====
                ['#370617', '#9d0208', '#dc2f02'],
                ['#540b0e', '#9e2a2b', '#e09f3e'],
                ['#641220', '#a4161a', '#ff4d6d'],
                ['#7f1d1d', '#b91c1c', '#ef4444'],
                ['#450920', '#a53860', '#f4a7bb'],

                // ===== ORANGE =====
                ['#7c2d12', '#ea580c', '#fdba74'],
                ['#552200', '#aa5500', '#ff9900'],
                ['#8d5524', '#c68642', '#e0ac69'],
                ['#ff6f00', '#ff8f00', '#ffd180'],
                ['#bc6c25', '#dda15e', '#ffe6a7'],

                // ===== YELLOW =====
                ['#fffde7', '#fff9c4', '#fff176'],
                ['#f48c06', '#ffba08', '#ffe066'],
                ['#ffdd00', '#ffd60a', '#fff3b0'],
                ['#e09f3e', '#f2cc8f', '#fff3bf'],
                ['#c9a227', '#ffd700', '#fff4b5'],

                // ===== PURPLE =====
                ['#240046', '#5a189a', '#9d4edd'],
                ['#3c096c', '#7b2d8b', '#c77dff'],
                ['#4a148c', '#6a1b9a', '#ba68c8'],
                ['#2e1065', '#7c3aed', '#c4b5fd'],
                ['#5b2a86', '#9163cb', '#d6c6ff'],

                // ===== PINK =====
                ['#ff006e', '#fb5607', '#ffbe0b'],
                ['#f72585', '#b5179e', '#7209b7'],
                ['#ff5d8f', '#ff99c8', '#ffe5ec'],
                ['#d63384', '#f06595', '#faa2c1'],
                ['#c9184a', '#ff4d6d', '#ffb3c1'],

                // ===== CYAN / TEAL =====
                ['#004e64', '#00a5cf', '#9fffcb'],
                ['#006466', '#065a60', '#0b525b'],
                ['#003049', '#00b4d8', '#90e0ef'],
                ['#005f73', '#0a9396', '#94d2bd'],
                ['#0a2239', '#53a2be', '#bcd4de'],

                // ===== NEUTRAL / GRAY =====
                ['#0d0d0d', '#1a1a1a', '#2d2d2d'],
                ['#1f1f1f', '#3d3d3d', '#b0b0b0'],
                ['#2b2d42', '#8d99ae', '#edf2f4'],
                ['#111827', '#374151', '#d1d5db'],
                ['#22223b', '#4a4e69', '#c9ada7'],

                // ===== PASTEL =====
                ['#ffd6e7', '#ffafcc', '#bde0fe'],
                ['#cdb4db', '#ffc8dd', '#ffafcc'],
                ['#d8f3dc', '#b7e4c7', '#74c69d'],
                ['#fff1e6', '#fde2e4', '#fad2e1'],
                ['#e2ece9', '#bee1e6', '#f0efeb'],

                // ===== SUNSET =====
                ['#ff4e50', '#f9d423', '#fc913a'],
                ['#ee0979', '#ff6a00', '#ffca28'],
                ['#ff6b6b', '#feca57', '#ff9f43'],
                ['#ff7b00', '#ff8800', '#ffd000'],
                ['#ef476f', '#ffd166', '#06d6a0'],
            ];

            function randomPattern() {
                const palette = PALETTES[Math.floor(Math.random() * PALETTES.length)];
                const shuffled = palette.slice().sort(() => Math.random() - 0.5);
                const cellSize = Math.floor(Math.random() * 120) + 40;
                const variance = Math.random() * 0.9 + 0.1;
                return window.trianglify({
                    width: 600,
                    height: 200,
                    cellSize,
                    variance,
                    xColors: shuffled,
                    yColors: Math.random() > 0.4 ? 'match' : shuffled.slice().reverse(),
                });
            }

            function drawPattern() {
                const pattern = randomPattern();
                const c = pattern.toCanvas();
                c.style.width = '100%';
                c.style.height = '100%';
                const preview = document.getElementById('pattern-preview');
                preview.innerHTML = '';
                preview.appendChild(c);
                clearBtn.style.display = 'inline-flex';
                useGeneratedInput.value = '1';
                c.toBlob(blob => {
                    patternBlob = blob;
                }, 'image/png');
            }

            document.getElementById('generate-pattern').addEventListener('click', drawPattern);

            clearBtn.addEventListener('click', function() {
                const preview = document.getElementById('pattern-preview');
                preview.innerHTML =
                    '<span id="preview-placeholder" style="font-size:13px;color:var(--color-text-muted);">Паттерн появится здесь</span>';
                clearBtn.style.display = 'none';
                useGeneratedInput.value = '0';
                patternBlob = null;
            });

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    clearBtn.style.display = 'none';
                    useGeneratedInput.value = '0';
                    patternBlob = null;
                }
            });

            form.addEventListener('submit', function(e) {
                if (useGeneratedInput.value === '1' && patternBlob && !imageInput.files.length) {
                    e.preventDefault();
                    const file = new File([patternBlob], 'pattern.png', {
                        type: 'image/png'
                    });
                    const dt = new DataTransfer();
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
            chip.style.background = 'var(--teal-50)';
            chip.style.color = 'var(--teal-700)';
            const icon = document.getElementById('chip-icon-' + id);
            icon.setAttribute('viewBox', '0 0 24 24');
            icon.innerHTML = '<path d="M20 6L9 17l-5-5"/>';
        }

        function deactivateChip(id) {
            const chip = document.getElementById('chip-' + id);
            chip.classList.remove('chip-active');
            chip.style.borderColor = 'var(--color-border)';
            chip.style.background = 'var(--color-surface)';
            chip.style.color = 'var(--gray-600)';
            const icon = document.getElementById('chip-icon-' + id);
            icon.setAttribute('viewBox', '0 0 24 24');
            icon.innerHTML = '<path d="M12 5v14M5 12h14"/>';
        }

        function addDateRow(id, name, pivotStart, pivotEnd) {
            const container = document.getElementById('group-dates-container');
            const row = document.createElement('div');
            row.id = 'date-row-' + id;
            row.style.cssText =
                'border:1px solid var(--color-border); border-radius:var(--r-md); padding:14px 16px; background:var(--teal-50); animation: fadeSlideIn 0.2s ease;';
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
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeSlideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-6px);
            }
        }
    </style>

</body>

</html>
