<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = ['column' => 'created_at', 'direction' => 'desc'];
    public $showDrawer = false;
    public $editingTask = null;
    
    // Form properties
    public $title = '';
    public $description = '';
    
    protected $rules = [
        'title' => 'required|min:3',
        'description' => 'nullable'
    ];

    public function render()
    {
        $tasks = Task::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);

        return view('livewire.tasks.index', [
            'tasks' => $tasks
        ])->layout('layouts.app');
    }

    public function save()
    {
        $this->validate();

        if ($this->editingTask) {
            // Update existing task
            $task = Task::find($this->editingTask);
            $task->update([
                'title' => $this->title,
                'description' => $this->description
            ]);
            $this->dispatch('task-updated', 'Task updated successfully!');
        } else {
            // Create new task
            Task::create([
                'title' => $this->title,
                'description' => $this->description
            ]);
            $this->dispatch('task-created', 'Task created successfully!');
        }

        $this->resetForm();
        $this->showDrawer = false;
    }

    public function edit($id)
    {
        $this->editingTask = $id;
        $task = Task::find($id);
        $this->title = $task->title;
        $this->description = $task->description;
        $this->showDrawer = true;
    }

    public function toggleComplete($id)
    {
        $task = Task::find($id);
        $task->update([
            'is_completed' => !$task->is_completed
        ]);
    }

    public function delete($id)
    {
        Task::destroy($id);
        $this->dispatch('task-deleted', 'Task deleted successfully!');
    }

    public function resetForm()
    {
        $this->editingTask = null;
        $this->title = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}