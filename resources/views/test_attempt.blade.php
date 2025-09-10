<div class="max-w-3xl mx-auto">
    <div class="card">
        <h2 class="text-2xl font-bold text-center">{{ $test->title }}</h2>
        <p class="text-center text-gray-600 mt-2 mb-8">{{ $test->description }}</p>

        <form action="/tests/{{ $test->id }}/result" method="POST">
            @csrf
            <div class="space-y-8">
                @foreach($questions as $question)
                    <div class="border-t pt-6">
                        <p class="font-semibold text-lg mb-4">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                        <div class="space-y-3 pl-4">
                            @foreach($question->options as $option)
                                <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="h-5 w-5">
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
