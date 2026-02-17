<div>
    <!-- Header with title and add button -->
    <x-header title="Tasks" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search tasks..." wire:model.live.debounce="search" icon="o-magnifying-glass" clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Task" icon="o-plus" class="btn-primary" @click="$wire.showDrawer = true" />
        </x-slot:actions>
    </x-header>

    <!-- Tasks Table -->
    <x-card>
        <x-table :headers="[
            ['key' => 'id', 'label' => '#', 'sortable' => true],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'is_completed', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions']
        ]" :rows="$tasks" :sort-by="$sortBy" with-pagination>
            
            @scope('cell_is_completed', $task)
                <x-badge :value="$task->is_completed ? 'Completed' : 'Pending'" 
                    :class="$task->is_completed ? 'badge-success' : 'badge-warning'" />
            @endscope

            @scope('cell_actions', $task)
                <div class="flex gap-2">
                    <x-button icon="o-pencil" wire:click="edit({{ $task->id }})" spinner class="btn-sm btn-ghost" />
                    <x-button icon="o-check" wire:click="toggleComplete({{ $task->id }})" spinner class="btn-sm btn-ghost text-success" />
                    <x-button icon="o-trash" wire:click="delete({{ $task->id }})" 
                        wire:confirm="Are you sure you want to delete this task?" 
                        spinner class="btn-sm btn-ghost text-error" />
                </div>
            @endscope
        </x-table>
    </x-card>

    <!-- Task Form Drawer -->
    <x-drawer wire:model="showDrawer" title="{{ $editingTask ? 'Edit Task' : 'Add New Task' }}" right separator with-close-button>
        <div class="space-y-4">
            <x-input label="Title" wire:model="title" placeholder="Enter task title" />
            <x-textarea label="Description" wire:model="description" placeholder="Enter task description" rows="3" />
            
            <div class="flex gap-2">
                <x-button label="Cancel" @click="$wire.showDrawer = false" />
                <x-button label="{{ $editingTask ? 'Update' : 'Create' }}" wire:click="save" class="btn-primary" />
            </div>
        </div>
    </x-drawer>

    <!-- Toast Notifications -->
    <x-toast />
</div>