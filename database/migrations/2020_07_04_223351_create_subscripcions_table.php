<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscripcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacion_id')->constrained('locacion');
            $table->foreignId('servicio_id')->constrained('servicio');
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
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
        Schema::dropIfExists('subscripcion');
    }
}
