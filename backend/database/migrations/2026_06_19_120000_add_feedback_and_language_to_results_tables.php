<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('read_aloud_results', function (Blueprint $table) {
            $table->string('language')->nullable()->after('transcript');
            $table->text('feedback')->nullable()->after('overall_score');
            $table->json('improvement_suggestions')->nullable()->after('feedback');
        });

        Schema::table('interview_results', function (Blueprint $table) {
            $table->string('language')->nullable()->after('transcript');
            $table->integer('pause_count')->default(0)->after('wpm');
            $table->float('pause_duration')->default(0)->after('pause_count');
            $table->json('improvement_suggestions')->nullable()->after('feedback');
        });
    }

    public function down(): void
    {
        Schema::table('read_aloud_results', function (Blueprint $table) {
            $table->dropColumn(['language', 'feedback', 'improvement_suggestions']);
        });

        Schema::table('interview_results', function (Blueprint $table) {
            $table->dropColumn(['language', 'pause_count', 'pause_duration', 'improvement_suggestions']);
        });
    }
};
