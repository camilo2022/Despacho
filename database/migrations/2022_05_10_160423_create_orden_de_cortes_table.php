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
        Schema::create('orden_de_cortes', function (Blueprint $table) {
            $table->id();
            $table->string('coleccion',50);
            $table->string('ncorte',50);
            $table->string('referencia',50);
            $table->string('letra',50);
            $table->string('diseñador',50)->nullable();
            $table->string('cortador1',50)->nullable();
            $table->string('cortador2',50)->nullable();
            $table->date('fecha',50);
            $table->string('nombre',50)->nullable();
            $table->string('porc',50)->nullable();
            $table->integer('estado')->unsigned();
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
        Schema::dropIfExists('ordendecorte');
    }
};
