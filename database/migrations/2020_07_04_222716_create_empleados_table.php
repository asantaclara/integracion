<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('legajo', 255);
            $table->enum('tipo_documento', [
                'CUIT',
                'CDI',
                'LE',
                'LC',
                'CI Extranjera',
                'Dni',
                'Pasaporte',
                'CI Policia Federal',
                'Certificado de Migracion'
            ]);
            $table->integer('documento');
            $table->string('direccion', 255);
            $table->string('telefono', 255);
            $table->enum('nacionalidad', [
                'Argentina',
                'Bolivia',
                'Brasil',
                'Chile',
                'Paraguay',
                'Uruguay',
                'Otra'
            ]);
            $table->enum('genero', [
                'Hombre',
                'Mujer',
                'Otro'
            ]);
            $table->foreignId('cliente_id')->constrained('cliente');
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
        Schema::dropIfExists('empleado');
    }
}
