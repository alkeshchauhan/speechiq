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
        Schema::table('read_aloud_results', function (Blueprint $table) {
            $table->decimal('speech_rate', 5, 2)->default(0.00);
            $table->integer('long_pauses')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('read_aloud_results', function (Blueprint $table) {
            $table->dropColumn(['speech_rate', 'long_pauses']);
        });
    }
};
