<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableAgendaAndActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dateTime('datetime')->nullable();
            $table->string('state')->default('pending');
            $table->bigInteger('organisation_id')->nullable();
            $table->bigInteger('agenda_id')->nullable();
        });

        Schema::table('agendas', function (Blueprint $table) {
            $table->date('begin_at')->nullable();
            $table->date('end_at')->nullable();
            $table->string('title')->nullable();
            $table->boolean('active')->default(true);
            $table->dropColumn('organisation_id');
            $table->dropColumn('activity_id');
            $table->dropColumn('date');
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
            $table->dropColumn('datetime');
            $table->dropColumn('state');
            $table->dropColumn('organisation_id');
            $table->dropColumn('agenda_id');
        });

        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn('begin_at');
            $table->dropColumn('end_at');
            $table->dropColumn('title');
            $table->dropColumn('active');
        });
    }
}
