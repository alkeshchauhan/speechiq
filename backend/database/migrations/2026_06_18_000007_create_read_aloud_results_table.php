<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('read_aloud_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audio_recording_id')->constrained()->onDelete('cascade');
            $table->text('transcript')->nullable();
            $table->integer('pronunciation_score')->default(0);
            $table->integer('fluency_score')->default(0);
            $table->integer('accuracy_score')->default(0);
            $table->integer('wpm')->default(0);
            $table->integer('pause_count')->default(0);
            $table->decimal('pause_duration', 8, 2)->default(0.00);
            $table->json('missing_words')->nullable();
            $table->json('extra_words')->nullable();
            $table->string('accent')->nullable();
            $table->integer('overall_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('read_aloud_results');
    }
};
