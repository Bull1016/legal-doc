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
            $table->string('title')->nullable(false)->after('exercice_id')->change();
            $table->date('begin_at')->nullable(false)->after('title')->change();
            $table->date('end_at')->nullable(false)->after('begin_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->string('title')->nullable()->after('exercice_id')->change();
            $table->date('begin_at')->nullable()->after('title')->change();
            $table->date('end_at')->nullable()->after('begin_at')->change();
        });
    }
};
