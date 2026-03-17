<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать задание</title>
    <link rel="stylesheet" href="{{ asset('css/courses-show.css') }}">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            background-color: #f9f9f9;
            margin-bottom: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
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
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
@include('components.menu')

<div class="container" style="max-width: 700px; margin: 40px auto;">
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $course)" text="К курсу" />
    </div>
    <h1>Редактировать задание</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('assignments.update', [$course, $assignment]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Название задания *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $assignment->title) }}" required>
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description">{{ old('description', $assignment->description) }}</textarea>
            @error('description')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="instructions">Инструкции</label>
            <textarea id="instructions" name="instructions" placeholder="Опишите, что должны сделать ученики...">{{ old('instructions', $assignment->instructions) }}</textarea>
            @error('instructions')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="due_date">Срок сдачи</label>
            <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}">
            @error('due_date')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        @if($assignment->files->count())
            <div class="form-group">
                <label>Текущие файлы:</label>
                <div>
                    @foreach($assignment->files as $file)
                        <div class="file-item">
                            <div>
                                <strong>{{ $file->title }}</strong><br>
                                <small>{{ number_format($file->file_size / 1024, 2) }} KB</small>
                            </div>
                            <form action="{{ route('assignments.delete-file', [$course, $assignment, $file]) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" onclick="return confirm('Удалить файл?')">Удалить</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="form-group">
            <label for="files">Добавить новые файлы (максимум 100MB каждый)</label>
            <input type="file" id="files" name="files[]" multiple>
            <small>Вы можете загрузить несколько файлов одновременно</small>
            @error('files.*')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Обновить задание</button>
    </form>
</div>
</body>
</html>
