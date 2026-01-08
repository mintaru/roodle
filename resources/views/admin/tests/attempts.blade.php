@extends('layout')

@section('content')
<h1>Попытки теста: {{ $test->title }}</h1>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Пользователь</th>
            <th>Оценка</th>
            <th>Дата</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($test->attempts as $attempt)
            <tr>
                <td>{{ $attempt->user->name }}</td> <!-- можно заменить на $attempt->user->name если есть связь с users -->
                <td>{{ $attempt->score }}</td>
                <td>{{ $attempt->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<a href="{{  url()->previous()}}">Назад</a>

@endsection
