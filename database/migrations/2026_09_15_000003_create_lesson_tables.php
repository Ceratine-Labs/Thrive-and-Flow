<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_week_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('modifications')->nullable(); // stage-specific cues shown in the player
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_published')->default(true)->index();
            $table->string('video_provider')->nullable(); // bunny | youtube | vimeo | file
            $table->string('video_ref')->nullable();      // signed-URL id / embed id / path
            $table->string('resource_path')->nullable();  // downloadable PDF etc.
            $table->timestamps();
            $table->index(['program_id', 'position']);
        });

        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('seconds_watched')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('lessons');
    }
};
