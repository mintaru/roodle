<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CourseSearch extends BaseAdminSearch
{
    public string $sortColumn = 'id';
    public string $sortDirection = 'asc';
    public int $page = 1;

    protected function getQuery(): Builder
    {
        return Course::with('groups', 'author');
    }

    public function getSearchColumns(): array
    {
        return [
            'title'  => 'Название',
            'status' => 'Статус',
            'author' => 'Автор',
            'group'  => 'Группа',
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
        return 'livewire.admin.courses';
    }

    public function render()
    {
        $query = $this->getQuery();

        if ($this->searchValue) {
            if ($this->searchColumn === 'status') {
                $statusMap = ['активен' => 'active', 'активный' => 'active', 'архив' => 'archived', 'в архиве' => 'archived'];
                $found = false;
                foreach ($statusMap as $ru => $en) {
                    if (str_contains(mb_strtolower($this->searchValue), $ru)) {
                        $query->where('status', $en);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $query->where('status', 'like', "%{$this->searchValue}%");
                }
            } else {
                match($this->searchColumn) {
                    'title' => $query->where('title', 'like', "%{$this->searchValue}%"),
                    'author' => $query->whereHas('author', fn($q) => $q->where('name', 'like', "%{$this->searchValue}%")),
                    'group'  => $query->whereHas('groups', fn($q) => $q->where('name', 'like', "%{$this->searchValue}%")),
                    default => null,
                };
            }
        }

        $allItems = $query->get();

        $sortCol = $this->sortColumn;
        $sortDir = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        if ($sortCol === 'author') {
            $allItems = $allItems->sortBy(fn($c) => $c->author?->name ?? '', SORT_REGULAR, $sortDir === 'desc');
        } elseif ($sortCol === 'group') {
            $allItems = $allItems->sortBy(fn($c) => $c->groups->pluck('name')->join(', '), SORT_REGULAR, $sortDir === 'desc');
        } elseif (in_array($sortCol, ['title', 'status', 'id'])) {
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
