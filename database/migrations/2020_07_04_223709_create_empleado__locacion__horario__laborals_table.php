<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadoLocacionHorarioLaboralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado_locacion_horario_laboral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_locacion_id')->constrained('empleado_locacion');
            $table->foreignId('horario_laboral_id')->constrained('horario_laboral');
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
        Schema::dropIfExists('empleado_locacion_horario_laboral');
    }
}
