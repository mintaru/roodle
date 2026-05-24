<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
class QuestionController extends Controller
{
    public function upload(Request $request)
    {
        if (! $request->hasFile('file')) {
            return response()->json(['error' => 'Файл не найден'], 400);
        }

        $path = $request->file('file')->store('questions', 'public');

        return response()->json([
            'location' => Storage::url($path),
        ]);
    }

    public function generate(Request $request)
{
    $request->validate([
        'prompt'        => 'required|string|max:500',
        'question_type' => 'required|string',
    ]);

    $type = $request->question_type;
    $prompt = $request->prompt;

    // Инструкция для модели
    $typeDesc = match($type) {
        'single_choice'   => 'один правильный ответ (4 варианта, один верный)',
        'multiple_choice' => 'несколько правильных ответов (4 варианта, несколько верных)',
        'short_answer'    => 'короткий текстовый ответ',
        default           => 'один правильный ответ (4 варианта)',
    };

    $systemText = "Ты генератор учебных вопросов. Отвечай ТОЛЬКО валидным JSON без markdown, без пояснений.";

    $userText = "Создай учебный вопрос на тему: «{$prompt}».
Тип: {$typeDesc}.
Верни JSON строго такого вида:
{
  \"question_text\": \"текст вопроса\",
  \"options\": [
    {\"text\": \"вариант\", \"is_correct\": true},
    {\"text\": \"вариант\", \"is_correct\": false}
  ]
}
Для short_answer поле options содержит только правильные варианты ответа с is_correct: true.";

    $response = Http::withHeaders([
        'Authorization' => 'Api-Key ' . config('services.yandex.api_key'),
        'Content-Type'  => 'application/json',
    ])->post('https://llm.api.cloud.yandex.net/foundationModels/v1/completion', [
        'modelUri'          => 'gpt://' . config('services.yandex.folder_id') . '/yandexgpt/latest',
        'completionOptions' => ['stream' => false, 'temperature' => 0.4, 'maxTokens' => 800],
        'messages' => [
            ['role' => 'system', 'text' => $systemText],
            ['role' => 'user',   'text' => $userText],
        ],
    ]);

    if (!$response->ok()) {
        return response()->json(['error' => 'YandexGPT error: ' . $response->body()], 502);
    }

    $text = $response->json('result.alternatives.0.message.text') ?? '';

    // Вырезаем JSON даже если модель добавила ```json
    $text = preg_replace('/```json|```/i', '', $text);
    $data = json_decode(trim($text), true);

    if (!$data || !isset($data['question_text'])) {
        return response()->json(['error' => 'Не удалось разобрать ответ модели', 'raw' => $text], 422);
    }

    return response()->json($data);
}
}
