<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDetallesolicitudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detallesolicitud', function (Blueprint $table) {
            $table->foreign('solicitud_id', 'detallesolicitud_ibfk_1')->references('id')->on('solicitud')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detallesolicitud', function (Blueprint $table) {
            $table->dropForeign('detallesolicitud_ibfk_1');
        });
    }
}
