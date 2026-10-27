<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('stage')->index();          // pregnancy | postpartum | mom_baby | any
            $table->string('kind')->default('program'); // program | collection
            $table->unsignedTinyInteger('weeks_from')->nullable(); // pregnancy week range, e.g. 14
            $table->unsignedTinyInteger('weeks_to')->nullable();   // e.g. 27
            $table->unsignedTinyInteger('classes_per_week')->nullable();
            $table->string('level')->nullable();
            $table->boolean('drip_by_week')->default(false); // unlock weeks as the member reaches them
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->string('color')->default('slate'); // cover art key: slate | blush | sand
            $table->string('icon')->default('petal');  // cover glyph key
            $table->json('equipment')->nullable();
            $table->timestamps();
        });

        Schema::create('program_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number'); // pregnancy week for drip programs, sequence otherwise
            $table->string('theme');
            $table->string('phase')->nullable(); // e.g. "Settle in · weeks 14–17"
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['program_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_weeks');
        Schema::dropIfExists('programs');
    }
};
