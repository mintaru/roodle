{{-- Меню --}}
@include('components.menu')
    <h1>Добавить лекцию к курсу: {{ $course->title }}</h1>
    <link rel="stylesheet" href="{{ asset('css/lecture-create.css') }}">
    <form action="{{ route('lectures.store', $course) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label>Название</label>
            <input type="text" name="title" required>
        </div>

        <div>
            <label>PDF файл</label>
            <input type="file" name="pdf" accept="application/pdf" required>
        </div>

        <div>
            <label>С какой страницы</label>
            <input type="number" name="from_page" min="1">
        </div>

        <div>
            <label>По какую страницу</label>
            <input type="number" name="to_page" min="1">
        </div>

        <button type="submit">Создать лекцию</button>
    </form>
