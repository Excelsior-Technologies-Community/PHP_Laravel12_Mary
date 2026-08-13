<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Search / Filter / Sort
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $status = '';

    public string $dateFilter = '';

    public int $perPage = 5;

    public array $sortBy = [
        'column' => 'created_at',
        'direction' => 'asc',
    ];

    /*
    |--------------------------------------------------------------------------
    | Drawers
    |--------------------------------------------------------------------------
    */

    public bool $showDrawer = false;

    public bool $filterDrawer = false;

    public $editingTask = null;

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public string $title = '';

    public string $description = '';

    protected $rules = [
        'title' => 'required|min:3',
        'description' => 'nullable',
    ];

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $tasks = $this->taskQuery()
            ->orderBy(
                $this->sortBy['column'],
                $this->sortBy['direction']
            )
            ->paginate($this->perPage);

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
        ])->layout('layouts.app');
    }

    /*
    |--------------------------------------------------------------------------
    | Task Query
    |--------------------------------------------------------------------------
    */

    protected function taskQuery()
    {
        return Task::query()

            // Search by title or description
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where(
                        'title',
                        'like',
                        '%' . $this->search . '%'
                    )
                        ->orWhere(
                            'description',
                            'like',
                            '%' . $this->search . '%'
                        );
                });
            })

            // Status filter
            ->when($this->status !== '', function ($query) {

                if ($this->status === 'completed') {
                    $query->where('is_completed', true);
                }

                if ($this->status === 'pending') {
                    $query->where('is_completed', false);
                }
            })

            // Date filter
            ->when($this->dateFilter !== '', function ($query) {

                if ($this->dateFilter === 'today') {
                    $query->whereDate(
                        'created_at',
                        today()
                    );
                }

                if ($this->dateFilter === 'week') {
                    $query->whereBetween(
                        'created_at',
                        [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]
                    );
                }

                if ($this->dateFilter === 'month') {
                    $query->whereMonth(
                        'created_at',
                        now()->month
                    )->whereYear(
                        'created_at',
                        now()->year
                    );
                }
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Open Add Task Drawer
    |--------------------------------------------------------------------------
    */

    public function openCreateDrawer(): void
    {
        $this->resetForm();

        $this->showDrawer = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Save Task
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $this->validate();

        if ($this->editingTask) {

            $task = Task::findOrFail(
                $this->editingTask
            );

            $task->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);

            $this->dispatch(
                'task-updated',
                'Task updated successfully!'
            );
        } else {

            Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'is_completed' => false,
            ]);

            $this->dispatch(
                'task-created',
                'Task created successfully!'
            );
        }

        $this->resetForm();

        $this->showDrawer = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Task
    |--------------------------------------------------------------------------
    */

    public function edit($id): void
    {
        $task = Task::findOrFail($id);

        $this->editingTask = $task->id;

        $this->title = $task->title;

        $this->description = $task->description ?? '';

        $this->showDrawer = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Complete
    |--------------------------------------------------------------------------
    */

    public function toggleComplete($id): void
    {
        $task = Task::findOrFail($id);

        $task->update([
            'is_completed' => !$task->is_completed,
        ]);

        $this->dispatch(
            'task-updated',
            'Task status updated!'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Task
    |--------------------------------------------------------------------------
    */

    public function delete($id): void
    {
        Task::destroy($id);

        $this->dispatch(
            'task-deleted',
            'Task deleted successfully!'
        );

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    public function resetForm(): void
    {
        $this->editingTask = null;

        $this->title = '';

        $this->description = '';

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Filters
    |--------------------------------------------------------------------------
    */

    public function clearFilters(): void
    {
        $this->search = '';

        $this->status = '';

        $this->dateFilter = '';

        $this->perPage = 10;

        $this->resetPage();

        $this->filterDrawer = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    public function updatedSortBy(): void
    {
        $allowedColumns = [
            'id',
            'title',
            'is_completed',
            'created_at',
        ];

        if (
            !in_array(
                $this->sortBy['column'],
                $allowedColumns
            )
        ) {
            $this->sortBy = [
                'column' => 'created_at',
                'direction' => 'desc',
            ];
        }

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    */

    public function export()
    {
        $tasks = $this->taskQuery()
            ->orderBy(
                $this->sortBy['column'],
                $this->sortBy['direction']
            )
            ->get();

        $fileName =
            'tasks-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($tasks) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                // CSV Header
                fputcsv($handle, [
                    'ID',
                    'Title',
                    'Description',
                    'Status',
                    'Created At',
                ]);

                // CSV Data
                foreach ($tasks as $task) {

                    fputcsv($handle, [
                        $task->id,
                        $task->title,
                        $task->description,
                        $task->is_completed
                            ? 'Completed'
                            : 'Pending',
                        $task->created_at
                            ? $task->created_at->format(
                                'Y-m-d H:i:s'
                            )
                            : '',
                    ]);
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }
}
