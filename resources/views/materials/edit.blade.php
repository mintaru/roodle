@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;margin:2rem auto;">
    <h1 style="font-size:20px;margin-bottom:1rem;">Редактировать материал</h1>

    <form action="{{ route('materials.update', ['course' => $course, 'material' => $material]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom:12px;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Название</label>
            <input type="text" name="title" value="{{ old('title', $material->title) }}" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
            @error('title') <div style="color:#dc2626;margin-top:6px;">{{ $message }}</div> @enderror
        </div>

        <div style="margin-bottom:12px;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Файл (заменит текущий, если выбран)</label>
            <input type="file" name="file">
            @error('file') <div style="color:#dc2626;margin-top:6px;">{{ $message }}</div> @enderror
            <div style="margin-top:8px;color:#6b7280;font-size:13px;">Текущий файл: {{ $material->file_name }}</div>
        </div>

        <div style="display:flex;gap:8px;justify-content:flex-end;">
            <a href="{{ route('courses.show', $course) }}" class="btn btn-ghost" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#374151;">Отмена</a>
            <button type="submit" class="btn btn-primary" style="padding:8px 14px;background:#0ea5a0;color:#fff;border:none;border-radius:6px;">Сохранить</button>
        </div>
    </form>
</div>
@endsection
