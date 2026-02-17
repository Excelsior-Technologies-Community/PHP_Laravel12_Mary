# PHP_Laravel12_Mary
## Overview

Laravel 12 maryUI Task Manager is a simple CRUD application built using Laravel 12, Livewire, and maryUI components. It demonstrates how to create modern, responsive interfaces with drawers, tables, badges, and toast notifications while maintaining clean backend logic.

This project is ideal for beginners learning Livewire UI development and component‑based design.

---

## Prerequisites

Before starting, ensure you have:

* PHP 8.2 or Higher
* Composer Installed
* Node.js and NPM Installed
* Database (SQLite Recommended)

---

## Step‑by‑Step Implementation

### Step 1 — Create New Laravel Project

```
composer create-project laravel/laravel laravel-mary-project
cd laravel-mary-project
```

### Step 2 — Install Required Packages

```
composer require livewire/livewire
composer require robsontenorio/mary
```

### Step 3 — Database Setup (SQLite)

```
touch database/database.sqlite
```

Update `.env` file:

```
DB_CONNECTION=sqlite
```

Comment or remove other DB variables.

---

### Step 4 — Install maryUI

```
php artisan mary:install
```

This command will:

* Install Tailwind CSS and DaisyUI
* Configure Vite
* Configure Livewire
* Publish maryUI configuration

---

### Step 5 — Install Frontend Dependencies

```
npm install
npm run dev
```

---

## Create Task CRUD

### Migration and Model

```
php artisan make:model Task -mf
php artisan migrate
```

### Tasks Table Fields

* id
* title
* description
* is_completed
* timestamps

---

## Livewire Component

Create component:

```
php artisan make:livewire Tasks/Index
```

Component Responsibilities:

* Search Tasks
* Pagination
* Sorting
* Drawer Form Handling
* Create / Update / Delete
* Toggle Completion Status
* Toast Event Dispatching

---

## Blade UI Features

UI Components Used:

* Header with Search Input
* maryUI Table Component
* Status Badges
* Drawer Form Panel
* Action Buttons
* Toast Notifications
* Responsive Layout

---

## Layout Structure

`resources/views/layouts/app.blade.php`

Includes:

* Tailwind Assets
* Navigation Bar
* Slot Content Area
* Livewire Scripts

---

## Routes

```
Route::get('/', Index::class)->name('tasks.index');
```

---

## Seeder (Optional)

```
php artisan make:seeder TaskSeeder
php artisan db:seed --class=TaskSeeder
```

Seeder creates multiple sample tasks using Faker.

---

## Running the Application

Terminal 1:

```
npm run dev
```

Terminal 2:

```
php artisan serve
```

Visit:

```
http://localhost:8000
```
<img width="1879" height="955" alt="image" src="https://github.com/user-attachments/assets/fd1218e0-a466-4b0e-b33e-3aa9b99ef4c5" />

---

## Key Features Implemented

* Data Table with Sorting and Pagination
* Drawer‑Based Create/Edit Forms
* Form Validation
* Status Badges
* Toast Notifications
* Responsive UI
* Livewire Reactive Components

---

## Suggested Enhancements

* User Authentication
* Task Categories
* Dark Mode Theme
* File Attachments
* Role‑Based Access
* REST API Integration

---

## Use Cases

* Task Management Apps
* Admin Dashboards
* Learning Livewire
* UI Component Prototyping
* Portfolio Demonstrations

---

## Requirements

* PHP 8.2+
* Composer
* Node.js & NPM
* SQLite or MySQL
* Laravel 12

---

## License

MIT License

