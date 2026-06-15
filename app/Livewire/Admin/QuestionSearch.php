<?php

namespace App\Livewire\Admin;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class QuestionSearch extends BaseAdminSearch
{
    public string $sortColumn = 'id';
    public string $sortDirection = 'asc';
    public int $page = 1;

    protected function getQuery(): Builder
    {
        return Question::with('options', 'tests');
    }

    public function getSearchColumns(): array
    {
        return [
            'question_text' => 'Текст вопроса',
            'question_type' => 'Тип вопроса',
            'id'            => 'ID',
        ];
    }

    public function updatedSearchColumn(): void
    {
        $this->page = 1;
    }

    public function updatedSearchValue(): void
    {
        $this->page = 1;
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = $page;
    }

    protected function getView(): string
    {
        return 'livewire.admin.questions';
    }

    public function render()
    {
        $query = $this->getQuery();

        if ($this->searchValue) {
            match($this->searchColumn) {
                'question_text' => $query->where('question_text', 'like', "%{$this->searchValue}%"),
                'question_type' => $query->where('question_type', 'like', "%{$this->searchValue}%"),
                'id'            => $query->where('id', 'like', "%{$this->searchValue}%"),
                default => null,
            };
        }

        $allItems = $query->get();

        $sortCol = $this->sortColumn;
        $sortDir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        if (in_array($sortCol, ['id', 'question_text', 'question_type'])) {
            $allItems = $allItems->sortBy($sortCol, SORT_REGULAR, $sortDir === 'desc');
        } elseif ($sortCol === 'options_count') {
            $allItems = $allItems->sortBy(fn($q) => $q->options->count(), SORT_REGULAR, $sortDir === 'desc');
        } elseif ($sortCol === 'tests_count') {
            $allItems = $allItems->sortBy(fn($q) => $q->tests->count(), SORT_REGULAR, $sortDir === 'desc');
        }

        $perPage = 15;
        $currentPage = max(1, $this->page);
        $items = new LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage)->values(),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view($this->getView(), ['items' => $items]);
    }
}
