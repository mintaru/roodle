<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }} - Ответы учеников</title>
    <link rel="stylesheet" href="{{ asset('css/courses-show.css') }}">
    <style>
        .assignment-header {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .submission-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .submission-table th,
        .submission-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .submission-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .submission-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-submitted {
            background-color: #28a745;
            color: white;
        }
        .status-graded {
            background-color: #17a2b8;
            color: white;
        }
        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        .grade-display {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .submission-details {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin-top: 20px;
        }
        .file-list {
            list-style: none;
            padding: 0;
            margin: 10px 0;
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .teacher-comment {
            background-color: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
@include('components.menu')

<div class="container" style="max-width: 1000px; margin: 40px auto;">
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $course)" text="К курсу" />
    </div>

    <div class="assignment-header">
        <h1 style="margin: 0;">{{ $assignment->title }}</h1>
        <p style="color: #666; margin-top: 10px;">Управление ответами учеников</p>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-200 text-green-800 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($submissions->count() > 0)
        <table class="submission-table">
            <thead>
                <tr>
                    <th>Ученик</th>
                    <th>Статус</th>
                    <th>Оценка</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    <tr>
                        <td>
                            <strong>{{ $submission->user->name }}</strong><br>
                            <small>{{ $submission->user->email }}</small>
                        </td>
                        <td>
                            @if($submission->submitted_at)
                                <span class="status-badge status-submitted">Сдано</span>
                                <br><small>{{ $submission->submitted_at->format('d.m.Y H:i') }}</small>
                            @else
                                <span style="color: #999;">Не сдано</span>
                            @endif
                        </td>
                        <td>
                            @if($submission->isGraded())
                                <span class="grade-display">{{ $submission->score }}</span>
                            @elseif($submission->submitted_at)
                                <span style="color: #ff6b6b;">Ожидает</span>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td>
                            <button onclick="toggleSubmissionDetails({{ $submission->id }})" class="btn-primary">Просмотр</button>
                        </td>
                    </tr>
                    <tr id="submission-details-{{ $submission->id }}" style="display: none;">
                        <td colspan="4" class="submission-details">
                            <h4>Ответ от {{ $submission->user->name }}</h4>
                            
                            @if($submission->answer_text)
                                <div style="background-color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                                    <strong>Текст ответа:</strong><br>
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
                                                <small>{{ number_format($file->file_size / 1024, 2) }} KB • {{ $file->created_at->format('d.m.Y H:i') }}</small>
                                            </div>
                                            <a href="{{ route('assignments.download-submission-file', [$course, $assignment, $submission, $file]) }}" class="btn-primary">Скачать</a>
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
                                            <strong>Ваш комментарий:</strong><br>
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
    @else
        <p style="color: #999; padding: 20px; background-color: #f9f9f9; border-radius: 4px;">На данный момент нет ответов от учеников</p>
    @endif
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
