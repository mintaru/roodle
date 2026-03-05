{{-- Меню --}}
@include('components.menu')
    <div class="mb-4">
        <x-back-button :url="route('courses.show', $course)" text="К курсу" />
    </div>
    <h1>Добавить материал к курсу: {{ $course->title }}</h1>

    <form action="{{ route('materials.store', $course) }}" method="POST" enctype="multipart/form-data" id="material-form">
        @csrf
        <div>
            <label for="title">Название материала</label>
            <input type="text" id="title" name="title" required placeholder="Например: Конспект лекции">
        </div>

        <div>
            <label for="file">Выберите файл</label>
            <input type="file" id="file" name="file" required>
            <small style="display: block; margin-top: 5px; color: #666;">
                Поддерживаемые форматы: PDF, Word (DOC, DOCX), Excel (XLS, XLSX), PowerPoint (PPT, PPTX), текстовые файлы и другие.<br>
                Максимальный размер: 100 МБ
            </small>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Загрузить материал</button>
        </div>
    </form>

    <style>
        #material-form {
            max-width: 500px;
            margin-top: 30px;
        }

        #material-form div {
            margin-bottom: 20px;
        }

        #material-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        #material-form input[type="text"],
        #material-form input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        #material-form input[type="file"] {
            padding: 8px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
