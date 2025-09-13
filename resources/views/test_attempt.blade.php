@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <h2 class="text-2xl font-bold text-center">{{ $test->title }}</h2>
        <p class="text-center text-gray-600 mt-2 mb-8">{{ $test->description }}</p>

        <form action="/tests/{{ $test->id }}/result" method="POST">
            @csrf
            <div class="space-y-8">
                @foreach($test->questions as $question)
                    <div class="border-t pt-6">
                        <p class="font-semibold text-lg mb-4">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                        <div class="space-y-3 pl-4">
                            @foreach($question->options as $option)
                                <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors">
                                    <input type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $option->id }}"
                                        class="h-5 w-5 answer-radio"
                                        data-question-id="{{ $question->id }}"
                                        {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'checked' : '' }}>
                                    <span>{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <button type="submit" class="btn btn-primary text-lg">Завершить тест и узнать результат</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('.answer-radio');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', async function() {
            try {
                const questionId = this.dataset.questionId;
                const optionId = this.value;

                const response = await fetch(`/tests/{{ $test->id }}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        option_id: optionId
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
            } catch (error) {
                console.error('Error saving answer:', error);
            }
        });
    });
});
</script>
@endsection
