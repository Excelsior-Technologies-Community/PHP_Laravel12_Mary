<div>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <x-header
        title="Task Manager"
        subtitle="Create, search, filter and manage your tasks."
        separator
        progress-indicator>

        <x-slot:middle class="!justify-end">

            <x-input
                placeholder="Search tasks..."
                wire:model.live.debounce.400ms="search"
                icon="o-magnifying-glass"
                clearable />

        </x-slot:middle>

        <x-slot:actions>

            {{-- Filter --}}
            <x-button
                label="Filters"
                icon="o-funnel"
                @click="$wire.filterDrawer = true"
                class="btn-outline" />

            {{-- Export --}}
            <x-button
                label="Export CSV"
                icon="o-arrow-down-tray"
                wire:click="export"
                spinner
                class="btn-outline btn-success" />

            {{-- Add Task --}}
            <x-button
                label="Add Task"
                icon="o-plus"
                wire:click="openCreateDrawer"
                class="btn-primary" />

        </x-slot:actions>

    </x-header>


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- Total --}}
        <x-card shadow>

            <div class="flex items-center justify-between">

                <div>
                    <div class="text-sm text-gray-500">
                        Total Tasks
                    </div>

                    <div class="text-3xl font-bold">
                        {{ \App\Models\Task::count() }}
                    </div>
                </div>

                <div class="text-3xl">
                    📋
                </div>

            </div>

        </x-card>


        {{-- Pending --}}
        <x-card shadow>

            <div class="flex items-center justify-between">

                <div>
                    <div class="text-sm text-gray-500">
                        Pending
                    </div>

                    <div class="text-3xl font-bold text-warning">
                        {{ \App\Models\Task::where('is_completed', false)->count() }}
                    </div>
                </div>

                <div class="text-3xl">
                    ⏳
                </div>

            </div>

        </x-card>


        {{-- Completed --}}
        <x-card shadow>

            <div class="flex items-center justify-between">

                <div>
                    <div class="text-sm text-gray-500">
                        Completed
                    </div>

                    <div class="text-3xl font-bold text-success">
                        {{ \App\Models\Task::where('is_completed', true)->count() }}
                    </div>
                </div>

                <div class="text-3xl">
                    ✅
                </div>

            </div>

        </x-card>

    </div>


    {{-- ========================================================= --}}
    {{-- ACTIVE FILTERS --}}
    {{-- ========================================================= --}}

    @if($search || $status || $dateFilter || $priorityFilter)

        <div class="mb-4">

            <div class="flex flex-wrap items-center gap-2">

                <span class="text-sm font-semibold">
                    Active Filters:
                </span>


                @if($search)

                    <x-badge
                        value="Search: {{ $search }}"
                        class="badge-info" />

                @endif


                @if($status)

                    <x-badge
                        value="Status: {{ ucfirst($status) }}"
                        class="badge-warning" />

                @endif


                @if($priorityFilter)

                    <x-badge
                        value="Priority: {{ ucfirst($priorityFilter) }}"
                        class="badge-error" />

                @endif


                @if($dateFilter)

                    <x-badge
                        value="Date: {{ ucfirst($dateFilter) }}"
                        class="badge-primary" />

                @endif


                <x-button
                    label="Clear Filters"
                    icon="o-x-mark"
                    wire:click="clearFilters"
                    spinner
                    class="btn-ghost btn-sm" />

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- TASK TABLE --}}
    {{-- ========================================================= --}}

    <x-card shadow>

        <x-table
            :headers="[
                [
                    'key' => 'id',
                    'label' => '#',
                    'sortable' => true,
                    'class' => 'w-16'
                ],

                [
                    'key' => 'title',
                    'label' => 'Title',
                    'sortable' => true
                ],

                [
                    'key' => 'description',
                    'label' => 'Description'
                ],

                [
                    'key' => 'priority',
                    'label' => 'Priority',
                    'sortable' => true
                ],

                [
                    'key' => 'due_date',
                    'label' => 'Due Date',
                    'sortable' => true
                ],

                [
                    'key' => 'is_completed',
                    'label' => 'Status',
                    'sortable' => true
                ],

                [
                    'key' => 'created_at',
                    'label' => 'Created',
                    'sortable' => true
                ],

                [
                    'key' => 'actions',
                    'label' => 'Actions'
                ]
            ]"
            :rows="$tasks"
            :sort-by="$sortBy"
            with-pagination>


            {{-- ================================================= --}}
            {{-- PRIORITY --}}
            {{-- ================================================= --}}

            @scope('cell_priority', $task)

                @if($task->priority === 'high')

                    <x-badge
                        value="High"
                        class="badge-error" />

                @elseif($task->priority === 'medium')

                    <x-badge
                        value="Medium"
                        class="badge-warning" />

                @else

                    <x-badge
                        value="Low"
                        class="badge-success" />

                @endif

            @endscope


            {{-- ================================================= --}}
            {{-- DUE DATE --}}
            {{-- ================================================= --}}

            @scope('cell_due_date', $task)

                @if($task->due_date)

                    @if(!$task->is_completed && $task->due_date->isPast())

                        <div class="flex flex-col gap-1">

                            <x-badge
                                value="Overdue"
                                class="badge-error" />

                            <span class="text-xs">
                                {{ $task->due_date->format('d M Y') }}
                            </span>

                        </div>

                    @elseif(!$task->is_completed && $task->due_date->isToday())

                        <div class="flex flex-col gap-1">

                            <x-badge
                                value="Due Today"
                                class="badge-warning" />

                            <span class="text-xs">
                                {{ $task->due_date->format('d M Y') }}
                            </span>

                        </div>

                    @else

                        <span class="text-sm">
                            {{ $task->due_date->format('d M Y') }}
                        </span>

                    @endif

                @else

                    <span class="text-gray-400">
                        -
                    </span>

                @endif

            @endscope


            {{-- ================================================= --}}
            {{-- STATUS --}}
            {{-- ================================================= --}}

            @scope('cell_is_completed', $task)

                @if($task->is_completed)

                    <x-badge
                        value="Completed"
                        class="badge-success" />

                @else

                    <x-badge
                        value="Pending"
                        class="badge-warning" />

                @endif

            @endscope


            {{-- ================================================= --}}
            {{-- DESCRIPTION --}}
            {{-- ================================================= --}}

            @scope('cell_description', $task)

                <div class="max-w-md">

                    {{ \Illuminate\Support\Str::limit(
                        $task->description ?? '-',
                        80
                    ) }}

                </div>

            @endscope


            {{-- ================================================= --}}
            {{-- CREATED --}}
            {{-- ================================================= --}}

            @scope('cell_created_at', $task)

                <div class="text-sm">

                    {{ $task->created_at?->format('d M Y') }}

                    <div class="text-xs text-gray-500">
                        {{ $task->created_at?->format('h:i A') }}
                    </div>

                </div>

            @endscope


            {{-- ================================================= --}}
            {{-- ACTIONS --}}
            {{-- ================================================= --}}

            @scope('cell_actions', $task)

                <div class="flex gap-1">

                    {{-- Edit --}}
                    <x-button
                        icon="o-pencil"
                        wire:click="edit({{ $task->id }})"
                        spinner
                        class="btn-sm btn-ghost"
                        tooltip="Edit" />


                    {{-- Toggle Complete --}}
                    @if($task->is_completed)

                        <x-button
                            icon="o-arrow-path"
                            wire:click="toggleComplete({{ $task->id }})"
                            spinner
                            class="btn-sm btn-ghost text-warning"
                            tooltip="Mark Pending" />

                    @else

                        <x-button
                            icon="o-check"
                            wire:click="toggleComplete({{ $task->id }})"
                            spinner
                            class="btn-sm btn-ghost text-success"
                            tooltip="Mark Completed" />

                    @endif


                    {{-- Delete --}}
                    <x-button
                        icon="o-trash"
                        wire:click="delete({{ $task->id }})"
                        wire:confirm="Are you sure you want to delete this task?"
                        spinner
                        class="btn-sm btn-ghost text-error"
                        tooltip="Delete" />

                </div>

            @endscope

        </x-table>

    </x-card>


    {{-- ========================================================= --}}
    {{-- FILTER DRAWER --}}
    {{-- ========================================================= --}}

    <x-drawer
        wire:model="filterDrawer"
        title="Task Filters"
        right
        separator
        with-close-button
        class="lg:w-1/3">

        <div class="space-y-5">


            {{-- Search --}}
            <x-input
                label="Search"
                placeholder="Search title or description..."
                wire:model.live.debounce.400ms="search"
                icon="o-magnifying-glass"
                clearable />


            {{-- Status --}}
            <x-select
                label="Status"
                wire:model.live="status"
                :options="[
                    [
                        'id' => '',
                        'name' => 'All Status'
                    ],
                    [
                        'id' => 'pending',
                        'name' => 'Pending'
                    ],
                    [
                        'id' => 'completed',
                        'name' => 'Completed'
                    ],
                ]"
                option-value="id"
                option-label="name" />


            {{-- Priority --}}
            <x-select
                label="Priority"
                wire:model.live="priorityFilter"
                :options="[
                    [
                        'id' => '',
                        'name' => 'All Priorities'
                    ],
                    [
                        'id' => 'low',
                        'name' => 'Low'
                    ],
                    [
                        'id' => 'medium',
                        'name' => 'Medium'
                    ],
                    [
                        'id' => 'high',
                        'name' => 'High'
                    ],
                ]"
                option-value="id"
                option-label="name" />


            {{-- Created Date --}}
            <x-select
                label="Created Date"
                wire:model.live="dateFilter"
                :options="[
                    [
                        'id' => '',
                        'name' => 'All Dates'
                    ],
                    [
                        'id' => 'today',
                        'name' => 'Today'
                    ],
                    [
                        'id' => 'week',
                        'name' => 'This Week'
                    ],
                    [
                        'id' => 'month',
                        'name' => 'This Month'
                    ],
                ]"
                option-value="id"
                option-label="name" />


            {{-- Per Page --}}
            <x-select
                label="Tasks Per Page"
                wire:model.live="perPage"
                :options="[
                    [
                        'id' => 5,
                        'name' => '5'
                    ],
                    [
                        'id' => 10,
                        'name' => '10'
                    ],
                    [
                        'id' => 25,
                        'name' => '25'
                    ],
                    [
                        'id' => 50,
                        'name' => '50'
                    ],
                ]"
                option-value="id"
                option-label="name" />

        </div>


        {{-- Filter Actions --}}
        <x-slot:actions>

            <x-button
                label="Reset"
                icon="o-x-mark"
                wire:click="clearFilters"
                spinner />

            <x-button
                label="Apply"
                icon="o-check"
                class="btn-primary"
                @click="$wire.filterDrawer = false" />

        </x-slot:actions>

    </x-drawer>


    {{-- ========================================================= --}}
    {{-- ADD / EDIT TASK DRAWER --}}
    {{-- ========================================================= --}}

    <x-drawer
        wire:model="showDrawer"
        title="{{ $editingTask ? 'Edit Task' : 'Add New Task' }}"
        right
        separator
        with-close-button>

        <div class="space-y-4">


            {{-- Title --}}
            <x-input
                label="Title"
                wire:model="title"
                placeholder="Enter task title"
                error="{{ $errors->first('title') }}" />


            {{-- Description --}}
            <x-textarea
                label="Description"
                wire:model="description"
                placeholder="Enter task description"
                rows="5"
                error="{{ $errors->first('description') }}" />


            {{-- Priority --}}
            <x-select
                label="Priority"
                wire:model="priority"
                :options="[
                    [
                        'id' => 'low',
                        'name' => 'Low'
                    ],
                    [
                        'id' => 'medium',
                        'name' => 'Medium'
                    ],
                    [
                        'id' => 'high',
                        'name' => 'High'
                    ],
                ]"
                option-value="id"
                option-label="name"
                error="{{ $errors->first('priority') }}" />


            {{-- Due Date --}}
            <x-input
                type="date"
                label="Due Date"
                wire:model="due_date"
                error="{{ $errors->first('due_date') }}" />


            {{-- Buttons --}}
            <div class="flex justify-end gap-2 pt-4">

                <x-button
                    label="Cancel"
                    wire:click="$set('showDrawer', false)" />

                <x-button
                    label="{{ $editingTask ? 'Update Task' : 'Create Task' }}"
                    icon="{{ $editingTask ? 'o-pencil' : 'o-plus' }}"
                    wire:click="save"
                    spinner
                    class="btn-primary" />

            </div>

        </div>

    </x-drawer>


    {{-- ========================================================= --}}
    {{-- TOAST --}}
    {{-- ========================================================= --}}

    <x-toast />

</div>