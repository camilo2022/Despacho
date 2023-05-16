<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNotificacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notificacion', function (Blueprint $table) {
            $table->foreign('rol_id', 'notificacion_ibfk_1')->references('id')->on('rol')->onDelete('CASCADE');
            $table->foreign('user_id', 'notificacion_ibfk_2')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notificacion', function (Blueprint $table) {
            $table->dropForeign('notificacion_ibfk_1');
            $table->dropForeign('notificacion_ibfk_2');
        });
    }
}
