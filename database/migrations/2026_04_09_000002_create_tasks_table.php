<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('quadrant', ['do_first', 'schedule', 'delegate', 'eliminate']);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('quadrant');
            $table->index('completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
