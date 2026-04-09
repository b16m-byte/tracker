<div class="task-card {{ $task->completed ? 'task-completed' : '' }} {{ $task->isOverdue() ? 'task-overdue' : '' }}"
     data-task-id="{{ $task->id }}"
     draggable="true">
    <div class="task-card-top">
        <button class="task-check {{ $task->completed ? 'checked' : '' }}"
                data-task-id="{{ $task->id }}"
                aria-label="Toggle complete">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="14" height="14">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </button>
        <span class="task-title">{{ $task->title }}</span>
        <div class="task-actions">
            <button class="task-action-btn task-edit-btn" data-task-id="{{ $task->id }}" aria-label="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
            <button class="task-action-btn task-delete-btn" data-task-id="{{ $task->id }}" aria-label="Delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                </svg>
            </button>
        </div>
    </div>
    @if($task->description)
        <p class="task-desc">{{ $task->description }}</p>
    @endif
    <div class="task-meta">
        @if($task->category)
            <span class="task-category" style="--cat-color: {{ $task->category->color }}">{{ $task->category->name }}</span>
        @endif
        @if($task->due_date)
            <span class="task-due {{ $task->isOverdue() ? 'overdue' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ $task->due_date->format('M d, Y') }}
            </span>
        @endif
    </div>
</div>
