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
        Schema::create('interview_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audio_recording_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->text('transcript')->nullable();
            $table->integer('grammar_score')->default(0);
            $table->integer('vocabulary_score')->default(0);
            $table->integer('content_score')->default(0);
            $table->integer('confidence_score')->default(0);
            $table->integer('pronunciation_score')->default(0);
            $table->integer('fluency_score')->default(0);
            $table->string('accent')->nullable();
            $table->integer('overall_score')->default(0);
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_results');
    }
};
