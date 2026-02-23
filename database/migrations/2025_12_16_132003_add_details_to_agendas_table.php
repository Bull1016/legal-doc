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
        Schema::table('agendas', function (Blueprint $table) {
            $table->string('type')->default('activity')->after('exercice_id'); // 'project', 'activity'
            $table->text('description')->nullable()->after('type');
            $table->string('location')->nullable()->after('description');
            $table->string('url')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn(['type', 'description', 'location', 'url']);
        });
    }
};
