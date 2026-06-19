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
        Schema::table('interview_results', function (Blueprint $table) {
            $table->string('tone')->nullable()->after('accent');
            $table->integer('communication_score')->default(0)->after('vocabulary_score');
            $table->integer('wpm')->default(0)->after('fluency_score');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_results', function (Blueprint $table) {
            $table->dropColumn(['tone', 'communication_score', 'wpm']);
        });
    }
};
