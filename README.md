# Eisenhower Tracker

A productivity-focused To-Do List application built around the **Eisenhower Matrix** — a time management method that organizes tasks by urgency and importance into four quadrants.

## Eisenhower Matrix Quadrants

| | **Urgent** | **Not Urgent** |
|---|---|---|
| **Important** | **Do First** - Crisis, deadlines, problems | **Schedule** - Planning, improvement, learning |
| **Not Important** | **Delegate** - Interruptions, some meetings | **Eliminate** - Time wasters, distractions |

## Tech Stack

- **Backend:** Laravel (PHP 8.4)
- **Frontend:** TypeScript + CSS (Vite build)
- **Database:** MySQL

## Features

- Four-quadrant Eisenhower Matrix layout
- Create, edit, and delete tasks
- Drag-and-drop tasks between quadrants
- Mark tasks as complete/incomplete
- Task categories with custom colors
- Due date tracking with overdue indicators
- Filter by active, completed, or all tasks
- Dashboard stats (total, completed, overdue, due today)
- Responsive design (desktop + mobile)
- Dark theme UI

## Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Installation

```bash
# Clone the repository
git clone <repo-url> eisenhower-tracker
cd eisenhower-tracker

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Create MySQL database
mysql -u root -e "CREATE DATABASE eisenhower_tracker;"

# Update .env with your database credentials
# DB_DATABASE=eisenhower_tracker
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed

# Build frontend assets
npm run build

# Start the dev server
php artisan serve
```

The application will be available at `http://localhost:8000`.

### Development

```bash
# Watch for frontend changes
npm run dev

# Run Laravel dev server
php artisan serve
```

## Project Structure

```
app/
├── Http/Controllers/
│   ├── TaskController.php       # Task CRUD + matrix view
│   └── CategoryController.php   # Category management
├── Models/
│   ├── Task.php                 # Task model (quadrant, completion, due dates)
│   └── Category.php             # Category model (name, color)
database/
├── migrations/                  # tasks + categories tables
├── seeders/                     # Sample data seeder
resources/
├── css/app.css                  # Full application styles
├── ts/
│   ├── app.ts                   # Entry point
│   ├── api.ts                   # API client + TypeScript interfaces
│   ├── drag-and-drop.ts         # Drag-and-drop between quadrants
│   └── modals.ts                # Task/category/delete modal logic
├── views/
│   ├── layouts/app.blade.php    # Main layout + modals
│   ├── tasks/index.blade.php    # Eisenhower Matrix view
│   └── partials/task-card.blade.php  # Reusable task card component
```

## API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| `GET` | `/` | Main matrix view |
| `POST` | `/api/tasks` | Create task |
| `PUT` | `/api/tasks/{id}` | Update task |
| `DELETE` | `/api/tasks/{id}` | Delete task |
| `POST` | `/api/tasks/reorder` | Reorder/move tasks |
| `GET` | `/api/stats` | Dashboard statistics |
| `GET` | `/api/categories` | List categories |
| `POST` | `/api/categories` | Create category |
| `DELETE` | `/api/categories/{id}` | Delete category |

## License

[MIT](LICENSE)
