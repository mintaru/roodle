<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <div class="card mb-6">
            <h2 class="text-2xl font-bold">{{ $test->title }}</h2>
            <p class="text-gray-600 mt-2">{{ $test->description }}</p>
        </div>
        <div class="card">
            <h3 class="text-xl font-bold mb-4">Вопросы в тесте ({{ $test->questions->count() }})</h3>
            <div class="space-y-4">
                @forelse($test->questions as $question)
                    <div class="border p-4 rounded-lg bg-gray-50">
                        <p class="font-semibold">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                        <ul class="list-disc pl-5 mt-2">
                            @foreach($question->options as $option)
                                <li class="{{ $option->is_correct ? 'font-bold text-green-600' : '' }}">
                                    {{ $option->option_text }}
                                    @if($option->is_correct)
                                        (Верный ответ)
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p>В этом тесте еще нет вопросов.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div>
        <div class="card">
            <h3 class="text-xl font-bold mb-4">Добавить новый вопрос</h3>
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            <form action="/tests/{{ $test->id }}/questions" method="POST" id="question-form">
                @csrf
                <div class="mb-4">
                    <label for="question_text" class="block font-semibold mb-2">Текст вопроса</label>
                    <textarea name="question_text" id="question_text" rows="3" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Варианты ответов</label>
                    <div id="options-container" class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="correct_option" value="0" class="h-5 w-5" required>
                            <input type="text" name="options[0]" class="w-full px-4 py-2 border rounded-lg" placeholder="Вариант 1" required>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="correct_option" value="1" class="h-5 w-5">
                            <input type="text" name="options[1]" class="w-full px-4 py-2 border rounded-lg" placeholder="Вариант 2" required>
                        </div>
                    </div>
                    <button type="button" id="add-option" class="mt-2 text-sm text-blue-600 hover:underline">+ Добавить вариант</button>
                </div>
                <button type="submit" class="btn btn-primary w-full">Добавить вопрос</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById("add-option").addEventListener("click", function() {
        const container = document.getElementById("options-container");
        const index = container.children.length;
        const newOption = document.createElement("div");
        newOption.className = "flex items-center space-x-2";
        newOption.innerHTML = `
            <input type="radio" name="correct_option" value="${index}" class="h-5 w-5">
            <input type="text" name="options[${index}]" class="w-full px-4 py-2 border rounded-lg" placeholder="Вариант ${index + 1}" required>
        `;
        container.appendChild(newOption);
    });
</script>
