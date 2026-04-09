import { api, Quadrant } from './api';

let draggedCard: HTMLElement | null = null;

export function initDragAndDrop(): void {
    document.addEventListener('dragstart', onDragStart);
    document.addEventListener('dragend', onDragEnd);
    document.addEventListener('dragover', onDragOver);
    document.addEventListener('drop', onDrop);
    document.addEventListener('dragleave', onDragLeave);
}

function onDragStart(e: DragEvent): void {
    const target = e.target as HTMLElement;
    if (!target.classList.contains('task-card')) return;

    draggedCard = target;
    target.classList.add('dragging');
    e.dataTransfer!.effectAllowed = 'move';
    e.dataTransfer!.setData('text/plain', target.dataset.taskId ?? '');
}

function onDragEnd(e: DragEvent): void {
    const target = e.target as HTMLElement;
    target.classList.remove('dragging');
    draggedCard = null;

    document.querySelectorAll('.task-list').forEach(list => {
        list.classList.remove('drag-over');
    });
    document.querySelectorAll('.drop-indicator').forEach(el => el.remove());
}

function onDragOver(e: DragEvent): void {
    e.preventDefault();
    e.dataTransfer!.dropEffect = 'move';

    const taskList = (e.target as HTMLElement).closest('.task-list') as HTMLElement;
    if (!taskList) return;

    document.querySelectorAll('.task-list').forEach(list => {
        list.classList.remove('drag-over');
    });
    taskList.classList.add('drag-over');

    // Determine position among siblings
    const afterElement = getDragAfterElement(taskList, e.clientY);
    document.querySelectorAll('.drop-indicator').forEach(el => el.remove());

    const indicator = document.createElement('div');
    indicator.classList.add('drop-indicator');

    if (afterElement) {
        taskList.insertBefore(indicator, afterElement);
    } else {
        taskList.appendChild(indicator);
    }
}

function onDragLeave(e: DragEvent): void {
    const taskList = (e.target as HTMLElement).closest('.task-list');
    if (!taskList) return;
    if (!taskList.contains(e.relatedTarget as Node)) {
        taskList.classList.remove('drag-over');
        taskList.querySelectorAll('.drop-indicator').forEach(el => el.remove());
    }
}

function onDrop(e: DragEvent): void {
    e.preventDefault();
    const taskList = (e.target as HTMLElement).closest('.task-list') as HTMLElement;
    if (!taskList || !draggedCard) return;

    taskList.classList.remove('drag-over');
    document.querySelectorAll('.drop-indicator').forEach(el => el.remove());

    const afterElement = getDragAfterElement(taskList, e.clientY);
    if (afterElement) {
        taskList.insertBefore(draggedCard, afterElement);
    } else {
        taskList.appendChild(draggedCard);
    }

    const newQuadrant = taskList.dataset.quadrant as Quadrant;
    persistReorder(taskList, newQuadrant);
    updateBadgeCounts();
}

function getDragAfterElement(container: HTMLElement, y: number): HTMLElement | null {
    const draggableElements = [...container.querySelectorAll<HTMLElement>('.task-card:not(.dragging)')];

    let closest: { element: HTMLElement | null; offset: number } = { element: null, offset: Number.NEGATIVE_INFINITY };

    for (const child of draggableElements) {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            closest = { element: child, offset };
        }
    }

    return closest.element;
}

function persistReorder(taskList: HTMLElement, quadrant: Quadrant): void {
    const cards = taskList.querySelectorAll<HTMLElement>('.task-card');
    const tasks = Array.from(cards).map((card, index) => ({
        id: parseInt(card.dataset.taskId!, 10),
        quadrant,
        sort_order: index,
    }));

    api.reorderTasks({ tasks }).catch(err => {
        console.error('Failed to reorder:', err);
    });
}

export function updateBadgeCounts(): void {
    const quadrants: Quadrant[] = ['do_first', 'schedule', 'delegate', 'eliminate'];
    for (const q of quadrants) {
        const list = document.querySelector(`.task-list[data-quadrant="${q}"]`);
        const badge = document.querySelector(`[data-count="${q}"]`);
        if (list && badge) {
            badge.textContent = String(list.querySelectorAll('.task-card').length);
        }
    }
}
