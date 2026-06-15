<?php

namespace App\Livewire\Admin;

use App\Models\Lecture;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class LectureSearch extends BaseAdminSearch
{
    public string $sortColumn = 'id';
    public string $sortDirection = 'asc';
    public int $page = 1;

    protected function getQuery(): Builder
    {
        return Lecture::with('course');
    }

    public function getSearchColumns(): array
    {
        return [
            'title'   => 'Название',
            'course'  => 'Курс',
            'content' => 'Содержание',
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
        return 'livewire.admin.lectures';
    }

    public function render()
    {
        $query = $this->getQuery();

        if ($this->searchValue) {
            match($this->searchColumn) {
                'title' => $query->where('title', 'like', "%{$this->searchValue}%"),
                'course' => $query->whereHas('course', fn($q) => $q->where('title', 'like', "%{$this->searchValue}%")),
                'content' => $query->where('content', 'like', "%{$this->searchValue}%"),
                default => null,
            };
        }

        $allItems = $query->get();

        $sortCol = $this->sortColumn;
        $sortDir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        if ($sortCol === 'course') {
            $allItems = $allItems->sortBy(fn($l) => $l->course?->title ?? '', SORT_REGULAR, $sortDir === 'desc');
        } elseif (in_array($sortCol, ['title', 'id'])) {
            $allItems = $allItems->sortBy($sortCol, SORT_REGULAR, $sortDir === 'desc');
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
