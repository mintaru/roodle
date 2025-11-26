<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of all questions.
     */
    public function index(Request $request)
    {
        $searchColumn = $request->input('search_column', 'question_text');
        $searchValue = $request->input('search_value', '');

        $query = Question::with('options', 'tests');

        // Apply search filter
        if ($searchValue) {
            if ($searchColumn === 'question_text') {
                $query->where('question_text', 'like', '%' . $searchValue . '%');
            } elseif ($searchColumn === 'id') {
                $query->where('id', 'like', '%' . $searchValue . '%');
            } elseif ($searchColumn === 'question_type') {
                $query->where('question_type', 'like', '%' . $searchValue . '%');
            }
        }

        $questions = $query->paginate(15);

        return view('question-bank.index', compact('questions', 'searchColumn', 'searchValue'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question)
    {
        $question->load('options');
        return view('question-bank.edit', compact('question'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'options' => 'array',
            'options.*.id' => 'nullable|integer',
            'options.*.option_text' => 'required_with:options|string|max:500',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        // Обновляем вопрос
        $question->update([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
        ]);

        // Обновляем опции
        if ($request->has('options')) {
            $optionIds = [];
            foreach ($request->options as $optionData) {
                if (isset($optionData['id']) && $optionData['id']) {
                    // Обновляем существующую опцию
                    $option = Option::find($optionData['id']);
                    if ($option && $option->question_id === $question->id) {
                        $option->update([
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $optionData['is_correct'] ?? false,
                        ]);
                        $optionIds[] = $option->id;
                    }
                } else {
                    // Создаем новую опцию
                    $option = $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                    ]);
                    $optionIds[] = $option->id;
                }
            }

            // Удаляем опции, которые не были переданы
            $question->options()
                ->whereNotIn('id', $optionIds)
                ->delete();
        }

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Вопрос успешно обновлён!');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        // Удаляем все связи с тестами
        $question->tests()->detach();

        // Удаляем все опции
        $question->options()->delete();

        // Удаляем все временные ответы
        $question->temporaryAnswers()->delete();

        // Удаляем сам вопрос
        $question->delete();

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Вопрос успешно удалён!');
    }
}
