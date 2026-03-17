<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/courses-show.css') }}">
    <style>
        .assignment-header {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .assignment-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 10px;
            font-size: 14px;
        }
        .assignment-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .assignment-section h3 {
            margin-top: 0;
            color: #333;
        }
        .file-list {
            list-style: none;
            padding: 0;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: white;
            margin-bottom: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-submitted {
            background-color: #28a745;
            color: white;
        }
        .status-graded {
            background-color: #17a2b8;
            color: white;
        }
        .status-overdue {
            background-color: #ffc107;
            color: black;
        }
        .grade-display {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .teacher-comment {
            background-color: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success-message {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
@include('components.menu')

<div class="container" style="max-width: 900px; margin: 40px auto;">
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $course)" text="К курсу" />
    </div>

    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <div class="assignment-header">
        <h1 style="margin: 0;">{{ $assignment->title }}</h1>
        @if($assignment->isOverdue())
            <span class="status-badge status-overdue">Сроки истекли</span>
        @endif
    </div>

    @if($assignment->description)
        <div class="assignment-section">
            <h3>Описание</h3>
            <p>{{ $assignment->description }}</p>
        </div>
    @endif

    @if($assignment->instructions)
        <div class="assignment-section">
            <h3>Инструкции</h3>
            <p>{!! nl2br(e($assignment->instructions)) !!}</p>
        </div>
    @endif

    @if($assignment->due_date)
        <div class="assignment-section">
            <h3>Информация</h3>
            <p>
                <strong>Срок сдачи:</strong> 
                {{ $assignment->due_date->format('d.m.Y H:i') }}
                @if($assignment->isOverdue())
                    <strong style="color: #dc3545;">(Сроки истекли)</strong>
                @endif
            </p>
        </div>
    @endif

    @if($assignment->files->count())
        <div class="assignment-section">
            <h3>📎 Файлы задания</h3>
            <ul class="file-list">
                @foreach($assignment->files as $file)
                    <li class="file-item">
                        <div>
                            <strong>{{ $file->title }}</strong><br>
                            <small>{{ number_format($file->file_size / 1024, 2) }} KB</small>
                        </div>
                        <a href="{{ route('assignments.download-file', [$course, $assignment, $file]) }}" class="btn-primary" style="margin: 0;">Скачать</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @auth
        @if(Auth::user()->hasAnyRole(['teacher', 'admin']))
            <!-- Teacher view - show submissions -->
            <div class="assignment-section" style="border-left-color: #28a745;">
                <h3>Ответы учеников</h3>
                @if($submissions->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f0f0f0;">
                                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ученик</th>
                                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Статус</th>
                                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Оценка</th>
                                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Действие</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                            <strong>{{ $submission->user->name }}</strong><br>
                                            <small>{{ $submission->user->email }}</small>
                                        </td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                            @if($submission->submitted_at)
                                                <span class="status-badge status-submitted">Сдано</span>
                                                <br><small>{{ $submission->submitted_at->format('d.m.Y H:i') }}</small>
                                            @else
                                                <span style="color: #999;">Не сдано</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                            @if($submission->isGraded())
                                                <span class="grade-display">{{ $submission->score }}</span>
                                            @elseif($submission->submitted_at)
                                                <span style="color: #ff6b6b;">Ожидает оценки</span>
                                            @else
                                                <span style="color: #999;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                            <button onclick="toggleSubmissionDetails({{ $submission->id }})" class="btn-primary" style="margin: 0;">Просмотр</button>
                                        </td>
                                    </tr>
                                    <tr id="submission-details-{{ $submission->id }}" style="display: none;">
                                        <td colspan="4" style="padding: 15px; background-color: #f9f9f9;">
                                            <h4>Ответ ученика</h4>
                                            @if($submission->answer_text)
                                                <div style="background-color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                                                    {!! nl2br(e($submission->answer_text)) !!}
                                                </div>
                                            @else
                                                <p style="color: #999;">Текст ответа не предоставлен</p>
                                            @endif

                                            @if($submission->files->count())
                                                <h5>Загруженные файлы:</h5>
                                                <ul class="file-list">
                                                    @foreach($submission->files as $file)
                                                        <li class="file-item">
                                                            <div>
                                                                <strong>{{ $file->file_name }}</strong><br>
                                                                <small>{{ number_format($file->file_size / 1024, 2) }} KB • Загружено: {{ $file->created_at->format('d.m.Y H:i') }}</small>
                                                            </div>
                                                            <a href="{{ route('assignments.download-submission-file', [$course, $assignment, $submission, $file]) }}" class="btn-primary" style="margin: 0;">Скачать</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            @if($submission->submitted_at)
                                                <hr style="margin: 15px 0;">
                                                <h4>Выставить оценку</h4>
                                                <form action="{{ route('assignments.grade', [$course, $assignment, $submission]) }}" method="POST">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="score-{{ $submission->id }}">Оценка</label>
                                                        <input type="number" id="score-{{ $submission->id }}" name="score" step="0.01" min="0" value="{{ $submission->score ?? '' }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="comment-{{ $submission->id }}">Комментарий</label>
                                                        <textarea id="comment-{{ $submission->id }}" name="teacher_comment">{{ $submission->teacher_comment ?? '' }}</textarea>
                                                    </div>
                                                    <button type="submit" class="btn-primary">Выставить оценку</button>
                                                </form>

                                                @if($submission->isGraded())
                                                    <hr style="margin: 15px 0;">
                                                    <h4>Выставленная оценка</h4>
                                                    <p style="font-size: 18px; font-weight: bold; color: #28a745;">{{ $submission->score }}</p>
                                                    @if($submission->teacher_comment)
                                                        <div class="teacher-comment">
                                                            <strong>Комментарий:</strong><br>
                                                            {!! nl2br(e($submission->teacher_comment)) !!}
                                                        </div>
                                                    @endif
                                                    <small style="color: #666;">Оценено: {{ $submission->graded_at->format('d.m.Y H:i') }}</small>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="color: #999;">На данный момент нет ответов от учеников</p>
                @endif
            </div>
        @elseif(Auth::user()->hasRole('student'))
            <!-- Student view - submit answer -->
            <div class="assignment-section" style="border-left-color: #28a745;">
                <h3>Ваш ответ</h3>

                @if($submission && $submission->submitted_at)
                    <div class="status-badge status-submitted">Сдано</div>
                    <p style="margin-top: 10px;"><strong>Дата отправки:</strong> {{ $submission->submitted_at->format('d.m.Y H:i') }}</p>
                @endif

                @if($submission && $submission->isGraded())
                    <div style="margin: 15px 0;">
                        <h4>Ваша оценка</h4>
                        <p style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $submission->score }}</p>
                        @if($submission->teacher_comment)
                            <div class="teacher-comment">
                                <strong>Комментарий учителя:</strong><br>
                                {!! nl2br(e($submission->teacher_comment)) !!}
                            </div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('assignments.submit', [$course, $assignment]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="answer_text">Ваш ответ</label>
                        <textarea id="answer_text" name="answer_text" placeholder="Напишите ваш ответ здесь...">{{ old('answer_text', $submission->answer_text ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="files">Загрузить файлы (максимум 100MB каждый)</label>
                        <input type="file" id="files" name="files[]" multiple>
                        <small>Вы можете загрузить несколько файлов одновременно</small>
                    </div>

                    @if($submission && $submission->files->count())
                        <div style="margin-bottom: 15px;">
                            <h4>Ваши загруженные файлы</h4>
                            <ul class="file-list">
                                @foreach($submission->files as $file)
                                    <li class="file-item">
                                        <div>
                                            <strong>{{ $file->file_name }}</strong><br>
                                            <small>{{ number_format($file->file_size / 1024, 2) }} KB</small>
                                        </div>
                                        <div>
                                            <a href="{{ route('assignments.download-submission-file', [$course, $assignment, $submission, $file]) }}" class="btn-primary" style="margin: 0; margin-right: 5px;">Скачать</a>
                                            <form action="{{ route('assignments.delete-submission-file', [$course, $assignment, $submission, $file]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger" onclick="return confirm('Удалить файл?')">Удалить</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit" class="btn-primary">Отправить ответ</button>
                </form>
            </div>
        @else
            <div class="assignment-section">
                <p style="color: #dc3545;">⛔ У вас нет доступа к этому заданию. Доступ есть только у студентов и преподавателей.</p>
            </div>
        @endif
    @else
        <div class="assignment-section">
            <p><a href="{{ route('login') }}">Войдите</a>, чтобы отправить ответ на это задание</p>
        </div>
    @endauth
</div>

<script>
function toggleSubmissionDetails(submissionId) {
    const details = document.getElementById('submission-details-' + submissionId);
    if (details.style.display === 'none') {
        details.style.display = 'table-row';
    } else {
        details.style.display = 'none';
    }
}
</script>
</body>
</html>
