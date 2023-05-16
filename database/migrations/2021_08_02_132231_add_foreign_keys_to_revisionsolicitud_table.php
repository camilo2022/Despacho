<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToRevisionsolicitudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('revisionsolicitud', function (Blueprint $table) {
            $table->foreign('solicitud_id', 'revisionsolicitud_ibfk_1')->references('id')->on('solicitud')->onDelete('CASCADE');
            $table->foreign('user_id', 'revisionsolicitud_ibfk_2')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('revisionsolicitud', function (Blueprint $table) {
            $table->dropForeign('revisionsolicitud_ibfk_1');
            $table->dropForeign('revisionsolicitud_ibfk_2');
        });
    }
}
