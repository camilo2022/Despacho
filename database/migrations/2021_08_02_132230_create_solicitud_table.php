<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tiposolicitud_id')->index('tiposolicitud_id');
            $table->integer('estado_id')->index('estado_id');
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->text('observacion')->nullable();
            $table->integer('cantidadtotal');
            $table->timestamp('delete_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitud');
    }
}
