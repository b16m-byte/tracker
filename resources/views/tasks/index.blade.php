@extends('layouts.app')

@section('content')
<div class="toolbar">
    <div class="toolbar-left">
        <div class="filter-group">
            <a href="?filter=active" class="filter-btn {{ $filter === 'active' ? 'active' : '' }}">Active</a>
            <a href="?filter=completed" class="filter-btn {{ $filter === 'completed' ? 'active' : '' }}">Completed</a>
            <a href="?filter=all" class="filter-btn {{ $filter === 'all' ? 'active' : '' }}">All</a>
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn btn-outline" id="manage-categories-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            Categories
        </button>
        <button class="btn btn-primary" id="add-task-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            New Task
        </button>
    </div>
</div>

<div class="matrix-labels">
    <div class="matrix-label-row">
        <div class="matrix-corner"></div>
        <div class="matrix-col-label urgent-label">Urgent</div>
        <div class="matrix-col-label not-urgent-label">Not Urgent</div>
    </div>
</div>

<div class="matrix-container">
    <div class="matrix-row-label important-label">
        <span>Important</span>
    </div>

    <div class="matrix">
        <!-- Q1: Urgent & Important -->
        <div class="quadrant quadrant-do-first" data-quadrant="do_first">
            <div class="quadrant-header">
                <div class="quadrant-title-group">
                    <span class="quadrant-icon">🔥</span>
                    <h2 class="quadrant-title">Do First</h2>
                </div>
                <span class="quadrant-badge" data-count="do_first">{{ isset($tasks['do_first']) ? $tasks['do_first']->count() : 0 }}</span>
            </div>
            <p class="quadrant-desc">Urgent & Important</p>
            <div class="task-list" data-quadrant="do_first">
                @foreach(($tasks['do_first'] ?? []) as $task)
                    @include('partials.task-card', ['task' => $task])
                @endforeach
            </div>
            <button class="add-task-inline" data-quadrant="do_first">+ Add task</button>
        </div>

        <!-- Q2: Not Urgent & Important -->
        <div class="quadrant quadrant-schedule" data-quadrant="schedule">
            <div class="quadrant-header">
                <div class="quadrant-title-group">
                    <span class="quadrant-icon">📅</span>
                    <h2 class="quadrant-title">Schedule</h2>
                </div>
                <span class="quadrant-badge" data-count="schedule">{{ isset($tasks['schedule']) ? $tasks['schedule']->count() : 0 }}</span>
            </div>
            <p class="quadrant-desc">Not Urgent & Important</p>
            <div class="task-list" data-quadrant="schedule">
                @foreach(($tasks['schedule'] ?? []) as $task)
                    @include('partials.task-card', ['task' => $task])
                @endforeach
            </div>
            <button class="add-task-inline" data-quadrant="schedule">+ Add task</button>
        </div>
    </div>

    <div class="matrix-row-label not-important-label">
        <span>Not Important</span>
    </div>

    <div class="matrix">
        <!-- Q3: Urgent & Not Important -->
        <div class="quadrant quadrant-delegate" data-quadrant="delegate">
            <div class="quadrant-header">
                <div class="quadrant-title-group">
                    <span class="quadrant-icon">👥</span>
                    <h2 class="quadrant-title">Delegate</h2>
                </div>
                <span class="quadrant-badge" data-count="delegate">{{ isset($tasks['delegate']) ? $tasks['delegate']->count() : 0 }}</span>
            </div>
            <p class="quadrant-desc">Urgent & Not Important</p>
            <div class="task-list" data-quadrant="delegate">
                @foreach(($tasks['delegate'] ?? []) as $task)
                    @include('partials.task-card', ['task' => $task])
                @endforeach
            </div>
            <button class="add-task-inline" data-quadrant="delegate">+ Add task</button>
        </div>

        <!-- Q4: Not Urgent & Not Important -->
        <div class="quadrant quadrant-eliminate" data-quadrant="eliminate">
            <div class="quadrant-header">
                <div class="quadrant-title-group">
                    <span class="quadrant-icon">🗑️</span>
                    <h2 class="quadrant-title">Eliminate</h2>
                </div>
                <span class="quadrant-badge" data-count="eliminate">{{ isset($tasks['eliminate']) ? $tasks['eliminate']->count() : 0 }}</span>
            </div>
            <p class="quadrant-desc">Not Urgent & Not Important</p>
            <div class="task-list" data-quadrant="eliminate">
                @foreach(($tasks['eliminate'] ?? []) as $task)
                    @include('partials.task-card', ['task' => $task])
                @endforeach
            </div>
            <button class="add-task-inline" data-quadrant="eliminate">+ Add task</button>
        </div>
    </div>
</div>

<script>
    window.__categories = @json($categories);
</script>
@endsection
