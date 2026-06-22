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
        Schema::table('analysis_reports', function (Blueprint $table) {
            $table->string('primary_language')->default('English')->after('total_tests_taken');
            $table->string('primary_accent')->default('Standard Accent')->after('primary_language');
            $table->string('primary_tone')->default('Professional')->after('primary_accent');
            $table->integer('confidence_average')->default(0)->after('primary_tone');
            $table->integer('pronunciation_average')->default(0)->after('confidence_average');
            $table->integer('fluency_average')->default(0)->after('pronunciation_average');
            $table->integer('accuracy_average')->default(0)->after('fluency_average');
            $table->integer('grammar_average')->default(0)->after('accuracy_average');
            $table->integer('vocabulary_average')->default(0)->after('grammar_average');
            $table->integer('content_average')->default(0)->after('vocabulary_average');
            $table->integer('communication_average')->default(0)->after('content_average');
            $table->integer('wpm_average')->default(0)->after('communication_average');
            $table->integer('pause_count_average')->default(0)->after('wpm_average');
            $table->float('pause_duration_average')->default(0.0)->after('pause_count_average');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_reports', function (Blueprint $table) {
            $table->dropColumn([
                'primary_language',
                'primary_accent',
                'primary_tone',
                'confidence_average',
                'pronunciation_average',
                'fluency_average',
                'accuracy_average',
                'grammar_average',
                'vocabulary_average',
                'content_average',
                'communication_average',
                'wpm_average',
                'pause_count_average',
                'pause_duration_average',
            ]);
        });
    }
};
