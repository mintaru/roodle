<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оценки - {{ $course->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/courses-show.css') }}">
    <style>
        .grades-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .group-selector {
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f4f8;
            border-radius: 5px;
        }

        .group-selector label {
            margin-right: 10px;
            font-weight: bold;
        }

        .group-selector select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .grades-table thead {
            background-color: #2c3e50;
            color: white;
        }

        .grades-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #bdc3c7;
        }

        .grades-table td {
            padding: 12px 15px;
            border: 1px solid #ecf0f1;
        }

        .grades-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .grades-table tbody tr:hover {
            background-color: #ecf0f1;
        }

        .student-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .score {
            text-align: center;
            font-weight: 500;
        }

        .score.not-graded {
            color: #95a5a6;
            font-style: italic;
        }

        .score.good {
            color: #27ae60;
        }

        .score.average {
            color: #f39c12;
        }

        .score.low {
            color: #e74c3c;
        }

        .totals-row {
            background-color: #ecf0f1 !important;
            font-weight: 600;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #7f8c8d;
        }

        .no-students {
            padding: 20px;
            text-align: center;
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            color: #856404;
            margin-top: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .section-subtitle {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .score-label {
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
@include('components.menu')

<div class="container">
    <a href="{{ route('courses.show', $course) }}" class="back-button">← Вернуться к курсу</a>

    <div class="grades-container">
        <h1 class="section-title">Ведомость оценок</h1>
        <p class="section-subtitle">Курс: <strong>{{ $course->title }}</strong></p>

        @if($groups->count() > 0)
            <div class="group-selector">
                <form method="GET" action="{{ route('courses.grades', $course) }}" id="gradesFilterForm" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <label for="group_id" style="margin: 0;">Группа:</label>
                    <select name="group_id" id="group_id" onchange="document.getElementById('gradesFilterForm').submit()">
                        <option value="">-- Выберите группу --</option>
                        <option value="all" {{ $allGroupsSelected ? 'selected' : '' }}>
                            📊 Все группы
                        </option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>

                    @if(($selectedGroup || $allGroupsSelected) && $students->count() > 0)
                        <label for="student_id" style="margin-left: 20px; margin: 0;">Студент:</label>
                        <select name="student_id" id="student_id" onchange="document.getElementById('gradesFilterForm').submit()">
                            <option value="">-- Все студенты --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ $selectedStudent && $selectedStudent->id == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>

                        <label for="item_type" style="margin-left: 20px; margin: 0;">Тест/Задание:</label>
                        <select name="item_type" id="item_type" onchange="updateItemSelect()">
                            <option value="">-- Все оценки --</option>
                            @if($tests->count() > 0)
                                <optgroup label="Тесты">
                                    @foreach($tests as $test)
                                        <option value="test" data-item-id="{{ $test->id }}" 
                                            {{ $selectedItemType === 'test' && $selectedItem && $selectedItem->id == $test->id ? 'selected' : '' }}>
                                            📝 {{ $test->title }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($assignments->count() > 0)
                                <optgroup label="Задания">
                                    @foreach($assignments as $assignment)
                                        <option value="assignment" data-item-id="{{ $assignment->id }}"
                                            {{ $selectedItemType === 'assignment' && $selectedItem && $selectedItem->id == $assignment->id ? 'selected' : '' }}>
                                            ✓ {{ $assignment->title }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>

                        <input type="hidden" name="item_id" id="item_id" value="{{ $selectedItem ? $selectedItem->id : '' }}">

                        @if($selectedStudent || $selectedItem)
                            <a href="{{ route('courses.grades', $course) }}" style="margin-left: 20px;" class="btn btn-secondary btn-xs">✕ Очистить фильтры</a>
                        @endif
                    @endif
                </form>

                <script>
                    function updateItemSelect() {
                        const itemTypeSelect = document.getElementById('item_type');
                        const selectedOption = itemTypeSelect.options[itemTypeSelect.selectedIndex];
                        const itemId = selectedOption.getAttribute('data-item-id') || '';
                        document.getElementById('item_id').value = itemId;
                        document.getElementById('gradesFilterForm').submit();
                    }
                </script>
            </div>

            @if(($selectedGroup || $allGroupsSelected) && $students->count() > 0)
                <div class="table-wrapper">
                    <!-- Таблица по группе (все студенты) -->
                    @if($filterMode === 'group')
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th style="width: 200px;">Ученик</th>
                                    @foreach($tests as $test)
                                        <th style="width: 120px;" title="{{ $test->title }}">
                                            <span class="score-label">Тест</span>
                                            <br>{{ substr($test->title, 0, 15) }}{{ strlen($test->title) > 15 ? '...' : '' }}
                                        </th>
                                    @endforeach
                                    @foreach($assignments as $assignment)
                                        <th style="width: 120px;" title="{{ $assignment->title }}">
                                            <span class="score-label">Задание</span>
                                            <br>{{ substr($assignment->title, 0, 15) }}{{ strlen($assignment->title) > 15 ? '...' : '' }}
                                        </th>
                                    @endforeach
                                    <th style="width: 100px;">Ср. оценка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gradesData as $grades)
                                    <tr>
                                        <td class="student-name">
                                            <a href="{{ route('courses.grades', ['course' => $course, 'group_id' => $selectedGroupId, 'student_id' => $grades['student']->id]) }}" style="color: inherit; text-decoration: none; cursor: pointer;">
                                                {{ $grades['student']->name }}
                                            </a>
                                        </td>

                                        @foreach($tests as $test)
                                            @php
                                                $testGrade = $grades['tests'][$test->id] ?? null;
                                                $score = $testGrade['score'] ?? '-';
                                                $scoreClass = 'not-graded';
                                                
                                                if ($score !== '-') {
                                                    if ($score >= 80) {
                                                        $scoreClass = 'good';
                                                    } elseif ($score >= 60) {
                                                        $scoreClass = 'average';
                                                    } else {
                                                        $scoreClass = 'low';
                                                    }
                                                }
                                            @endphp
                                            <td class="score {{ $scoreClass }}">
                                                @if($score === '-')
                                                    -
                                                @else
                                                    {{ $score }}%
                                                @endif
                                            </td>
                                        @endforeach

                                        @foreach($assignments as $assignment)
                                            @php
                                                $assignmentGrade = $grades['assignments'][$assignment->id] ?? null;
                                                $score = $assignmentGrade['score'] ?? '-';
                                                $maxScore = $assignmentGrade['max_score'] ?? 100;
                                                $scoreClass = 'not-graded';
                                                
                                                if ($score !== '-') {
                                                    $percentage = ($score / $maxScore) * 100;
                                                    if ($percentage >= 80) {
                                                        $scoreClass = 'good';
                                                    } elseif ($percentage >= 60) {
                                                        $scoreClass = 'average';
                                                    } else {
                                                        $scoreClass = 'low';
                                                    }
                                                }
                                            @endphp
                                            <td class="score {{ $scoreClass }}">
                                                @if($score === '-')
                                                    -
                                                @else
                                                    {{ $score }}/{{ $maxScore }}
                                                @endif
                                            </td>
                                        @endforeach

                                        @php
                                            $validTests = collect($grades['tests'])->filter(fn($t) => $t['score'] !== '-')->count();
                                            $validAssignments = collect($grades['assignments'])->filter(fn($a) => $a['score'] !== '-')->count();
                                            
                                            $avgTestScore = 0;
                                            $avgAssignmentScore = 0;
                                            
                                            if ($validTests > 0) {
                                                $totalTestScore = collect($grades['tests'])->filter(fn($t) => $t['score'] !== '-')->sum('score');
                                                $avgTestScore = $totalTestScore / $validTests;
                                            }
                                            
                                            if ($validAssignments > 0) {
                                                $totalAssignmentScore = 0;
                                                foreach ($grades['assignments'] as $assignment) {
                                                    if ($assignment['score'] !== '-') {
                                                        $totalAssignmentScore += ($assignment['score'] / $assignment['max_score']) * 100;
                                                    }
                                                }
                                                $avgAssignmentScore = $totalAssignmentScore / $validAssignments;
                                            }
                                            
                                            $overallAvg = 0;
                                            if ($validTests + $validAssignments > 0) {
                                                $overallAvg = ($avgTestScore + $avgAssignmentScore) / 2;
                                            }
                                            
                                            $avgClass = 'not-graded';
                                            if ($validTests + $validAssignments > 0) {
                                                if ($overallAvg >= 80) {
                                                    $avgClass = 'good';
                                                } elseif ($overallAvg >= 60) {
                                                    $avgClass = 'average';
                                                } else {
                                                    $avgClass = 'low';
                                                }
                                            }
                                        @endphp
                                        <td class="score {{ $avgClass }}">
                                            @if($validTests + $validAssignments > 0)
                                                {{ round($overallAvg, 1) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($tests) + count($assignments) + 2 }}" class="no-students">
                                            В этой группе нет студентов
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    <!-- Таблица по одному студенту -->
                    @elseif($filterMode === 'student')
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th style="width: 300px;">Название</th>
                                    <th style="width: 150px;">Оценка</th>
                                    <th style="width: 150px;">Процент</th>
                                    <th style="width: 150px;">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($gradesData) > 0 && isset($gradesData[0]['tests']) && count($gradesData[0]['tests']) > 0)
                                    <tr style="background-color: #e3f2fd;">
                                        <td colspan="4" style="font-weight: bold; padding: 10px;">Тесты</td>
                                    </tr>
                                    @foreach($gradesData[0]['tests'] as $testData)
                                        @php
                                            $score = $testData['score'];
                                            $maxScore = $testData['max_score'];
                                            $percentage = $score === '-' ? 0 : $score;
                                            $scoreClass = 'not-graded';
                                            
                                            if ($score !== '-') {
                                                if ($percentage >= 80) {
                                                    $scoreClass = 'good';
                                                } elseif ($percentage >= 60) {
                                                    $scoreClass = 'average';
                                                } else {
                                                    $scoreClass = 'low';
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="student-name">{{ $testData['name'] }}</td>
                                            <td class="score {{ $scoreClass }}">
                                                @if($score === '-') - @else {{ $score }} @endif
                                            </td>
                                            <td class="score {{ $scoreClass }}">
                                                @if($score === '-') 
                                                    Не пройден
                                                @elseif($score == 5)
                                                    Отлично
                                                @elseif($score == 4)
                                                    Хорошо
                                                @elseif($score ==3 )
                                                    Удовлетворительно
                                                @elseif($score==2)
                                                    Неудовлетворительно
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if(count($gradesData) > 0 && isset($gradesData[0]['assignments']) && count($gradesData[0]['assignments']) > 0)
                                    <tr style="background-color: #f3e5f5;">
                                        <td colspan="4" style="font-weight: bold; padding: 10px;">Задания</td>
                                    </tr>
                                    @foreach($gradesData[0]['assignments'] as $assignmentData)
                                        @php
                                            $score = $assignmentData['score'];
                                            $maxScore = $assignmentData['max_score'];
                                            $scoreClass = 'not-graded';
                                            
                                            if ($score !== '-') {
                                                if ($score >= 4) {
                                                    $scoreClass = 'good';
                                                } elseif ($score >= 3) {
                                                    $scoreClass = 'average';
                                                } else {
                                                    $scoreClass = 'low';
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="student-name">{{ $assignmentData['name'] }}</td>
                                            <td class="score {{ $scoreClass }}">
                                                @if($score === '-') - @else {{ $score }}/{{ $maxScore }} @endif
                                            </td>
                                            <td class="score {{ $scoreClass }}">
                                                @if($score === '-') 
                                                    Не пройден
                                                @elseif($score == 5)
                                                    Отлично
                                                @elseif($score == 4)
                                                    Хорошо
                                                @elseif($score ==3 )
                                                    Удовлетворительно
                                                @elseif($score==2)
                                                    Неудовлетворительно
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                    <!-- Таблица по одному тесту/заданию -->
                    @elseif($filterMode === 'item')
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th style="width: 250px;">Ученик</th>
                                    <th style="width: 150px;">Оценка</th>
                                    <th style="width: 150px;">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gradesData as $itemGrade)
                                    @php
                                        $score = $itemGrade['item_score'];
                                        $maxScore = $itemGrade['item_max_score'];
                                        $percentage = $score === '-' ? 0 : ($score / $maxScore) * 100;
                                        $scoreClass = 'not-graded';
                                        
                                        if ($score !== '-') {
                                            if ($percentage >= 80) {
                                                $scoreClass = 'good';
                                            } elseif ($percentage >= 60) {
                                                $scoreClass = 'average';
                                            } else {
                                                $scoreClass = 'low';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="student-name">{{ $itemGrade['student']->name }}</td>
                                        <td class="score {{ $scoreClass }}">
                                            @if($score === '-') - @else {{ $score }} @endif
                                        </td>
                                        <td class="score {{ $scoreClass }}">
                                            @if($score === '-') 
                                            Не пройден
                                        @elseif($score == 5)
                                            Отлично
                                        @elseif($score == 4)
                                            Хорошо
                                        @elseif($score ==3 )
                                            Удовлетворительно
                                        @elseif($score==2)
                                            Неудовлетворительно
                                        @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="no-students">
                                            Нет данных для отображения
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>
            @elseif($selectedGroup && $students->count() === 0)
                <div class="no-students">
                    В группе <strong>{{ $selectedGroup->name }}</strong> нет студентов
                </div>
            @elseif($allGroupsSelected && $students->count() === 0)
                <div class="no-students">
                    Во всех выбранных группах нет студентов
                </div>
            @else
                <div class="no-students">
                    Пожалуйста, выберите группу для просмотра оценок
                </div>
            @endif
        @else
            <div class="no-students">
                К этому курсу не привязана ни одна группа. Пожалуйста, добавьте группу в курс перед просмотром оценок.
            </div>
        @endif
    </div>
</div>
</body>
</html>
