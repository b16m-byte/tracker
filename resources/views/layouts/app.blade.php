<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Eisenhower Tracker') }}</title>
    @vite(['resources/css/app.css', 'resources/ts/app.ts'])
</head>
<body>
    <div id="app">
        <header class="app-header">
            <div class="header-left">
                <h1 class="app-title">
                    <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    Eisenhower Tracker
                </h1>
                <p class="app-subtitle">Prioritize what matters</p>
            </div>
            <div class="header-right">
                <div class="stats-bar" id="stats-bar">
                    @isset($stats)
                    <div class="stat-item">
                        <span class="stat-value" id="stat-total">{{ $stats['total'] }}</span>
                        <span class="stat-label">Total</span>
                    </div>
                    <div class="stat-item stat-completed">
                        <span class="stat-value" id="stat-completed">{{ $stats['completed'] }}</span>
                        <span class="stat-label">Done</span>
                    </div>
                    <div class="stat-item stat-overdue">
                        <span class="stat-value" id="stat-overdue">{{ $stats['overdue'] }}</span>
                        <span class="stat-label">Overdue</span>
                    </div>
                    <div class="stat-item stat-today">
                        <span class="stat-value" id="stat-today">{{ $stats['today'] }}</span>
                        <span class="stat-label">Today</span>
                    </div>
                    @endisset
                </div>
            </div>
        </header>

        <main class="app-main">
            @yield('content')
        </main>
    </div>

    <!-- Task Modal -->
    <div class="modal-overlay" id="task-modal" hidden>
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title" id="modal-title">New Task</h2>
                <button class="modal-close" id="modal-close" aria-label="Close">&times;</button>
            </div>
            <form id="task-form">
                <input type="hidden" id="task-id" value="">
                <input type="hidden" id="task-quadrant" value="">

                <div class="form-group">
                    <label for="task-title">Title</label>
                    <input type="text" id="task-title" class="form-input" placeholder="What needs to be done?" required maxlength="255" autofocus>
                </div>

                <div class="form-group">
                    <label for="task-description">Description</label>
                    <textarea id="task-description" class="form-input" placeholder="Add details..." rows="3" maxlength="1000"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="task-due-date">Due Date</label>
                        <input type="date" id="task-due-date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="task-category">Category</label>
                        <select id="task-category" class="form-input">
                            <option value="">No Category</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Quadrant</label>
                    <div class="quadrant-selector">
                        <label class="quadrant-option q-do-first">
                            <input type="radio" name="quadrant" value="do_first">
                            <span>Do First</span>
                        </label>
                        <label class="quadrant-option q-schedule">
                            <input type="radio" name="quadrant" value="schedule">
                            <span>Schedule</span>
                        </label>
                        <label class="quadrant-option q-delegate">
                            <input type="radio" name="quadrant" value="delegate">
                            <span>Delegate</span>
                        </label>
                        <label class="quadrant-option q-eliminate">
                            <input type="radio" name="quadrant" value="eliminate">
                            <span>Eliminate</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modal-submit">Save Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal-overlay" id="category-modal" hidden>
        <div class="modal modal-small">
            <div class="modal-header">
                <h2 class="modal-title">New Category</h2>
                <button class="modal-close" id="category-modal-close" aria-label="Close">&times;</button>
            </div>
            <form id="category-form">
                <div class="form-group">
                    <label for="category-name">Name</label>
                    <input type="text" id="category-name" class="form-input" placeholder="Category name" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="category-color">Color</label>
                    <input type="color" id="category-color" class="form-input form-color" value="#6366f1">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="category-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="delete-modal" hidden>
        <div class="modal modal-small">
            <div class="modal-header">
                <h2 class="modal-title">Delete Task</h2>
            </div>
            <p class="modal-body-text">Are you sure you want to delete this task? This action cannot be undone.</p>
            <div class="form-actions">
                <button class="btn btn-secondary" id="delete-cancel">Cancel</button>
                <button class="btn btn-danger" id="delete-confirm">Delete</button>
            </div>
        </div>
    </div>
</body>
</html>
