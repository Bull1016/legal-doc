<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateModelsWithExerciceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->bigInteger('exercice_id');
        });
        
        Schema::table('activities', function (Blueprint $table) {
            $table->bigInteger('exercice_id');
        });
        
        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('exercice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('exercice_id');
        });
        
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('exercice_id');
        });
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('exercice_id');
        });
    }
}
