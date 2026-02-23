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
        Schema::table('activities', function (Blueprint $table) {
          $table->dropColumn('agenda_id');
          $table->bigInteger('organisation_id')->after('string_id')->change();
          $table->dropColumn('state');
          $table->dropColumn('datetime');
          $table->bigInteger('exercice_id')->after('organisation_id')->change();
          $table->dropColumn('daterange');
          $table->float('latitude')->after('place')->nullable();
          $table->float('longitude')->after('latitude')->nullable();
        });
      }

      /**
       * Reverse the migrations.
      */
      public function down(): void
      {
        Schema::table('activities', function (Blueprint $table) {
          $table->bigInteger('agenda_id')->nullable()->after('string_id')->change();
          $table->bigInteger('organisation_id')->nullable()->change();
          $table->string('state')->default('pending');
          $table->dateTime('datetime')->nullable();
          $table->text('daterange')->nullable();
          $table->dropColumn('latitude');
          $table->dropColumn('longitude');
        });
    }
};
