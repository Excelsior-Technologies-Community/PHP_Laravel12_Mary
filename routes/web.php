<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Tasks\Index;

Route::get('/', Index::class)->name('tasks.index');