<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleado');
            $table->foreignId('locacion_id')->constrained('locacion');
            $table->enum('genero', [
                'Entrada',
                'Salida'
            ]);
            $table->dateTime('fecha_hora', 0);
            $table->string('justificacion', 255)->nullable();
            $table->boolean('activa');
            $table->foreignId('fichada_original_id')->nullable()->constrained('fichada');
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
        Schema::dropIfExists('fichada');
    }
}
