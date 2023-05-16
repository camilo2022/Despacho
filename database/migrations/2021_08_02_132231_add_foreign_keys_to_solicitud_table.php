<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSolicitudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->foreign('tiposolicitud_id', 'solicitud_ibfk_1')->references('id')->on('tiposolicitud')->onDelete('CASCADE');
            $table->foreign('estado_id', 'solicitud_ibfk_2')->references('id')->on('estado')->onDelete('CASCADE');
            $table->foreign('user_id', 'solicitud_ibfk_3')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropForeign('solicitud_ibfk_1');
            $table->dropForeign('solicitud_ibfk_2');
            $table->dropForeign('solicitud_ibfk_3');
        });
    }
}
