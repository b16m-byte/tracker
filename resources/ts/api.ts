export interface Task {
    id: number;
    title: string;
    description: string | null;
    quadrant: Quadrant;
    category_id: number | null;
    category: Category | null;
    due_date: string | null;
    completed: boolean;
    completed_at: string | null;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    color: string;
}

export type Quadrant = 'do_first' | 'schedule' | 'delegate' | 'eliminate';

export interface TaskPayload {
    title: string;
    description?: string;
    quadrant: Quadrant;
    category_id?: number | null;
    due_date?: string | null;
}

export interface ReorderPayload {
    tasks: { id: number; quadrant: Quadrant; sort_order: number }[];
}

const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

async function request<T>(url: string, options: RequestInit = {}): Promise<T> {
    const headers: Record<string, string> = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        ...(options.headers as Record<string, string> ?? {}),
    };

    if (options.body && typeof options.body === 'string') {
        headers['Content-Type'] = 'application/json';
    }

    const res = await fetch(url, { ...options, headers });

    if (!res.ok) {
        const error = await res.json().catch(() => ({ message: res.statusText }));
        throw new Error(error.message || `Request failed: ${res.status}`);
    }

    if (res.status === 204) return null as T;
    return res.json();
}

export const api = {
    createTask(data: TaskPayload): Promise<Task> {
        return request('/api/tasks', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    },

    updateTask(id: number, data: Partial<TaskPayload & { completed: boolean }>): Promise<Task> {
        return request(`/api/tasks/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    },

    deleteTask(id: number): Promise<void> {
        return request(`/api/tasks/${id}`, { method: 'DELETE' });
    },

    reorderTasks(data: ReorderPayload): Promise<void> {
        return request('/api/tasks/reorder', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    },

    getCategories(): Promise<Category[]> {
        return request('/api/categories');
    },

    createCategory(data: { name: string; color: string }): Promise<Category> {
        return request('/api/categories', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    },

    deleteCategory(id: number): Promise<void> {
        return request(`/api/categories/${id}`, { method: 'DELETE' });
    },
};
