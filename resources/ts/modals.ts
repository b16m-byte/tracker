import { api, Category, Quadrant, Task } from './api';
import { updateBadgeCounts } from './drag-and-drop';

declare global {
    interface Window {
        __categories: Category[];
    }
}

let pendingDeleteId: number | null = null;

export function initModals(): void {
    initTaskModal();
    initCategoryModal();
    initDeleteModal();
    initInlineAdds();
    initTaskActions();
}

// ── Task Modal ──────────────────────────────────────────────────────

function initTaskModal(): void {
    const modal = document.getElementById('task-modal')!;
    const form = document.getElementById('task-form') as HTMLFormElement;
    const closeBtn = document.getElementById('modal-close')!;
    const cancelBtn = document.getElementById('modal-cancel')!;
    const addBtn = document.getElementById('add-task-btn')!;

    addBtn.addEventListener('click', () => openTaskModal());
    closeBtn.addEventListener('click', () => closeModal(modal));
    cancelBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleTaskSubmit();
    });
}

function openTaskModal(task?: Task, quadrant?: Quadrant): void {
    const modal = document.getElementById('task-modal')!;
    const title = document.getElementById('modal-title')!;
    const taskId = document.getElementById('task-id') as HTMLInputElement;
    const taskTitle = document.getElementById('task-title') as HTMLInputElement;
    const taskDesc = document.getElementById('task-description') as HTMLTextAreaElement;
    const taskDue = document.getElementById('task-due-date') as HTMLInputElement;
    const taskCategory = document.getElementById('task-category') as HTMLSelectElement;

    // Populate category dropdown
    populateCategorySelect(taskCategory);

    if (task) {
        title.textContent = 'Edit Task';
        taskId.value = String(task.id);
        taskTitle.value = task.title;
        taskDesc.value = task.description ?? '';
        taskDue.value = task.due_date ? task.due_date.split('T')[0] : '';
        taskCategory.value = task.category_id ? String(task.category_id) : '';

        const radio = document.querySelector<HTMLInputElement>(`input[name="quadrant"][value="${task.quadrant}"]`);
        if (radio) radio.checked = true;
    } else {
        title.textContent = 'New Task';
        taskId.value = '';
        taskTitle.value = '';
        taskDesc.value = '';
        taskDue.value = '';
        taskCategory.value = '';

        const q = quadrant ?? 'do_first';
        const radio = document.querySelector<HTMLInputElement>(`input[name="quadrant"][value="${q}"]`);
        if (radio) radio.checked = true;
    }

    modal.hidden = false;
    taskTitle.focus();
}

function populateCategorySelect(select: HTMLSelectElement): void {
    const current = select.value;
    select.innerHTML = '<option value="">No Category</option>';
    for (const cat of window.__categories ?? []) {
        const opt = document.createElement('option');
        opt.value = String(cat.id);
        opt.textContent = cat.name;
        select.appendChild(opt);
    }
    select.value = current;
}

async function handleTaskSubmit(): Promise<void> {
    const taskId = (document.getElementById('task-id') as HTMLInputElement).value;
    const taskTitle = (document.getElementById('task-title') as HTMLInputElement).value.trim();
    const taskDesc = (document.getElementById('task-description') as HTMLTextAreaElement).value.trim();
    const taskDue = (document.getElementById('task-due-date') as HTMLInputElement).value;
    const taskCategory = (document.getElementById('task-category') as HTMLSelectElement).value;
    const quadrant = document.querySelector<HTMLInputElement>('input[name="quadrant"]:checked')?.value as Quadrant;

    if (!taskTitle || !quadrant) return;

    const payload = {
        title: taskTitle,
        description: taskDesc || undefined,
        quadrant,
        category_id: taskCategory ? parseInt(taskCategory, 10) : null,
        due_date: taskDue || null,
    };

    try {
        if (taskId) {
            const updated = await api.updateTask(parseInt(taskId, 10), payload);
            updateTaskCard(updated);
        } else {
            const created = await api.createTask(payload);
            appendTaskCard(created);
        }

        closeModal(document.getElementById('task-modal')!);
        updateBadgeCounts();
    } catch (err) {
        console.error('Failed to save task:', err);
        alert('Failed to save task. Please try again.');
    }
}

function appendTaskCard(task: Task): void {
    const list = document.querySelector(`.task-list[data-quadrant="${task.quadrant}"]`);
    if (!list) return;

    const card = createTaskCardElement(task);
    list.appendChild(card);
}

function updateTaskCard(task: Task): void {
    const existing = document.querySelector(`.task-card[data-task-id="${task.id}"]`);
    if (existing) {
        const newCard = createTaskCardElement(task);
        const targetList = document.querySelector(`.task-list[data-quadrant="${task.quadrant}"]`);

        if (existing.closest('.task-list') !== targetList) {
            existing.remove();
            targetList?.appendChild(newCard);
        } else {
            existing.replaceWith(newCard);
        }
    }
}

function createTaskCardElement(task: Task): HTMLElement {
    const card = document.createElement('div');
    card.className = `task-card${task.completed ? ' task-completed' : ''}`;
    card.dataset.taskId = String(task.id);
    card.draggable = true;

    const dueDateStr = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
    const isOverdue = task.due_date && !task.completed && new Date(task.due_date) < new Date();
    if (isOverdue) card.classList.add('task-overdue');

    card.innerHTML = `
        <div class="task-card-top">
            <button class="task-check ${task.completed ? 'checked' : ''}" data-task-id="${task.id}" aria-label="Toggle complete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="14" height="14">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>
            <span class="task-title">${escapeHtml(task.title)}</span>
            <div class="task-actions">
                <button class="task-action-btn task-edit-btn" data-task-id="${task.id}" aria-label="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                <button class="task-action-btn task-delete-btn" data-task-id="${task.id}" aria-label="Delete">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                </button>
            </div>
        </div>
        ${task.description ? `<p class="task-desc">${escapeHtml(task.description)}</p>` : ''}
        <div class="task-meta">
            ${task.category ? `<span class="task-category" style="--cat-color: ${task.category.color}">${escapeHtml(task.category.name)}</span>` : ''}
            ${dueDateStr ? `<span class="task-due ${isOverdue ? 'overdue' : ''}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                ${dueDateStr}
            </span>` : ''}
        </div>
    `;

    return card;
}

function escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ── Category Modal ──────────────────────────────────────────────────

function initCategoryModal(): void {
    const modal = document.getElementById('category-modal')!;
    const form = document.getElementById('category-form') as HTMLFormElement;
    const closeBtn = document.getElementById('category-modal-close')!;
    const cancelBtn = document.getElementById('category-cancel')!;
    const openBtn = document.getElementById('manage-categories-btn')!;

    openBtn.addEventListener('click', () => {
        modal.hidden = false;
        (document.getElementById('category-name') as HTMLInputElement).focus();
    });
    closeBtn.addEventListener('click', () => closeModal(modal));
    cancelBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = (document.getElementById('category-name') as HTMLInputElement).value.trim();
        const color = (document.getElementById('category-color') as HTMLInputElement).value;

        if (!name) return;

        try {
            const cat = await api.createCategory({ name, color });
            window.__categories.push(cat);
            closeModal(modal);
            (document.getElementById('category-name') as HTMLInputElement).value = '';
        } catch (err) {
            console.error('Failed to create category:', err);
            alert('Failed to create category.');
        }
    });
}

// ── Delete Modal ────────────────────────────────────────────────────

function initDeleteModal(): void {
    const modal = document.getElementById('delete-modal')!;
    const cancelBtn = document.getElementById('delete-cancel')!;
    const confirmBtn = document.getElementById('delete-confirm')!;

    cancelBtn.addEventListener('click', () => {
        pendingDeleteId = null;
        closeModal(modal);
    });
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            pendingDeleteId = null;
            closeModal(modal);
        }
    });

    confirmBtn.addEventListener('click', async () => {
        if (pendingDeleteId === null) return;
        try {
            await api.deleteTask(pendingDeleteId);
            document.querySelector(`.task-card[data-task-id="${pendingDeleteId}"]`)?.remove();
            updateBadgeCounts();
        } catch (err) {
            console.error('Failed to delete task:', err);
        }
        pendingDeleteId = null;
        closeModal(modal);
    });
}

// ── Inline Add Buttons ──────────────────────────────────────────────

function initInlineAdds(): void {
    document.querySelectorAll<HTMLButtonElement>('.add-task-inline').forEach(btn => {
        btn.addEventListener('click', () => {
            const quadrant = btn.dataset.quadrant as Quadrant;
            openTaskModal(undefined, quadrant);
        });
    });
}

// ── Task Card Actions (delegated) ───────────────────────────────────

function initTaskActions(): void {
    document.addEventListener('click', async (e) => {
        const target = e.target as HTMLElement;

        // Toggle complete
        const checkBtn = target.closest('.task-check') as HTMLElement;
        if (checkBtn) {
            const taskId = parseInt(checkBtn.dataset.taskId!, 10);
            const isCompleted = checkBtn.classList.contains('checked');
            try {
                const updated = await api.updateTask(taskId, { completed: !isCompleted });
                const card = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
                if (card) {
                    card.classList.toggle('task-completed', updated.completed);
                    checkBtn.classList.toggle('checked', updated.completed);
                }
            } catch (err) {
                console.error('Failed to toggle task:', err);
            }
            return;
        }

        // Edit
        const editBtn = target.closest('.task-edit-btn') as HTMLElement;
        if (editBtn) {
            const taskId = parseInt(editBtn.dataset.taskId!, 10);
            const card = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
            if (!card) return;

            const task = extractTaskFromCard(card as HTMLElement, taskId);
            openTaskModal(task);
            return;
        }

        // Delete
        const deleteBtn = target.closest('.task-delete-btn') as HTMLElement;
        if (deleteBtn) {
            pendingDeleteId = parseInt(deleteBtn.dataset.taskId!, 10);
            document.getElementById('delete-modal')!.hidden = false;
            return;
        }
    });
}

function extractTaskFromCard(card: HTMLElement, id: number): Task {
    const quadrant = card.closest('.task-list')?.getAttribute('data-quadrant') as Quadrant;
    const title = card.querySelector('.task-title')?.textContent ?? '';
    const description = card.querySelector('.task-desc')?.textContent ?? null;
    const categoryEl = card.querySelector('.task-category');
    const dueEl = card.querySelector('.task-due');

    return {
        id,
        title,
        description,
        quadrant,
        category_id: null,
        category: categoryEl ? { id: 0, name: categoryEl.textContent ?? '', color: '' } : null,
        due_date: dueEl ? dueEl.textContent?.trim() ?? null : null,
        completed: card.classList.contains('task-completed'),
        completed_at: null,
        sort_order: 0,
        created_at: '',
        updated_at: '',
    };
}

function closeModal(modal: HTMLElement): void {
    modal.hidden = true;
}
