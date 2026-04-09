<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $work = Category::create(['name' => 'Work', 'color' => '#3b82f6']);
        $personal = Category::create(['name' => 'Personal', 'color' => '#22c55e']);
        $health = Category::create(['name' => 'Health', 'color' => '#ef4444']);
        $learning = Category::create(['name' => 'Learning', 'color' => '#f59e0b']);
        $finance = Category::create(['name' => 'Finance', 'color' => '#8b5cf6']);

        // Q1: Do First (Urgent & Important)
        Task::create([
            'title' => 'Fix critical production bug',
            'description' => 'Users are unable to complete checkout - investigate and resolve immediately.',
            'quadrant' => 'do_first',
            'category_id' => $work->id,
            'due_date' => now()->toDateString(),
            'sort_order' => 0,
        ]);
        Task::create([
            'title' => 'Submit quarterly tax filing',
            'description' => 'Deadline is tomorrow. Prepare and file quarterly estimated taxes.',
            'quadrant' => 'do_first',
            'category_id' => $finance->id,
            'due_date' => now()->addDay()->toDateString(),
            'sort_order' => 1,
        ]);
        Task::create([
            'title' => 'Doctor appointment follow-up',
            'description' => 'Call to schedule follow-up for lab results.',
            'quadrant' => 'do_first',
            'category_id' => $health->id,
            'due_date' => now()->toDateString(),
            'sort_order' => 2,
        ]);

        // Q2: Schedule (Not Urgent & Important)
        Task::create([
            'title' => 'Design new feature architecture',
            'description' => 'Plan the architecture for the notification system redesign.',
            'quadrant' => 'schedule',
            'category_id' => $work->id,
            'due_date' => now()->addWeek()->toDateString(),
            'sort_order' => 0,
        ]);
        Task::create([
            'title' => 'Complete TypeScript course',
            'description' => 'Finish remaining modules on advanced generics and utility types.',
            'quadrant' => 'schedule',
            'category_id' => $learning->id,
            'due_date' => now()->addWeeks(2)->toDateString(),
            'sort_order' => 1,
        ]);
        Task::create([
            'title' => 'Weekly exercise routine',
            'description' => 'Establish and maintain a consistent 4-day workout plan.',
            'quadrant' => 'schedule',
            'category_id' => $health->id,
            'sort_order' => 2,
        ]);
        Task::create([
            'title' => 'Review investment portfolio',
            'description' => 'Quarterly rebalancing of retirement and brokerage accounts.',
            'quadrant' => 'schedule',
            'category_id' => $finance->id,
            'due_date' => now()->addWeeks(3)->toDateString(),
            'sort_order' => 3,
        ]);

        // Q3: Delegate (Urgent & Not Important)
        Task::create([
            'title' => 'Reply to vendor inquiry emails',
            'description' => 'Three vendors awaiting pricing confirmation.',
            'quadrant' => 'delegate',
            'category_id' => $work->id,
            'due_date' => now()->addDays(2)->toDateString(),
            'sort_order' => 0,
        ]);
        Task::create([
            'title' => 'Schedule team meeting room',
            'description' => 'Book conference room for Friday sprint retrospective.',
            'quadrant' => 'delegate',
            'category_id' => $work->id,
            'due_date' => now()->addDays(3)->toDateString(),
            'sort_order' => 1,
        ]);
        Task::create([
            'title' => 'Grocery shopping',
            'description' => 'Weekly groceries - could use delivery service.',
            'quadrant' => 'delegate',
            'category_id' => $personal->id,
            'sort_order' => 2,
        ]);

        // Q4: Eliminate (Not Urgent & Not Important)
        Task::create([
            'title' => 'Organize old bookmarks',
            'description' => 'Clean up browser bookmarks folder.',
            'quadrant' => 'eliminate',
            'category_id' => $personal->id,
            'sort_order' => 0,
        ]);
        Task::create([
            'title' => 'Browse tech deal sites',
            'description' => 'Check for discounts on peripherals - not needed now.',
            'quadrant' => 'eliminate',
            'category_id' => $personal->id,
            'sort_order' => 1,
        ]);

        // One completed task for demo
        Task::create([
            'title' => 'Set up CI/CD pipeline',
            'description' => 'GitHub Actions workflow for automated testing and deployment.',
            'quadrant' => 'do_first',
            'category_id' => $work->id,
            'completed' => true,
            'completed_at' => now()->subDay(),
            'sort_order' => 3,
        ]);
    }
}
