<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_de_corte_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('id_corte')->unsigned();
            $table->string('tela',50);
            $table->float('ancho')->nullable();
            $table->string('tela_bolsillo',50)->nullable();
            $table->string('largot2_tela_bolsillo',50)->nullable();
            $table->string('tendida2_tela_bolsillo',50)->nullable();
            $table->string('tela_dos',50)->nullable();
            $table->string('largot2_tela_dos',50)->nullable();
            $table->string('tendida2_tela_dos',50)->nullable();
            $table->float('ribete');
            $table->float('trazo_pasadores');
            $table->float('trazo_aletillones');
            $table->float('tendidos_1')->nullable();
            $table->float('tendidos_2')->nullable();
            $table->float('tendidos_3')->nullable();
            $table->string('foto_D');
            $table->string('foto_T');
            $table->string('marca',50);
            $table->string('especificacion1',50)->nullable();
            $table->string('especificacion2',50)->nullable();
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
        Schema::dropIfExists('orden_de_corte_detalles');
    }
};
