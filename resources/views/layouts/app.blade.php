<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Task Manager' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Main navigation bar -->
    <x-nav sticky class="bg-base-100 shadow-sm">
        <x-slot:brand>
            <!-- Replace the icon component with a simple emoji or text -->
            <span class="text-2xl mr-2">✅</span>            
            <div class="font-bold text-xl">Task Manager</div>
        </x-slot:brand>
        <x-slot:actions>
            <x-button label="Tasks" link="/" class="btn-ghost" />
        </x-slot:actions>
    </x-nav>

    <!-- Main content -->
    <main class="p-6">
        {{ $slot }}
    </main>
</body>
</html>