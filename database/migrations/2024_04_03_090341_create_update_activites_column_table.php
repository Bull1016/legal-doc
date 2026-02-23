<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateActivitesColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->binary('banner')->after('name');
            $table->text('place')->after('banner');
            $table->text('daterange')->after('place');
            $table->text('description')->after('daterange');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('banner');
            $table->dropColumn('place');
            $table->dropColumn('daterange');
            $table->dropColumn('description');
        });
    }
}
