<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->string('cuit_cuil',11);
            $table->enum('tipo_categoria', [
                'IVA Responsable Inscripto',
                'IVA Sujeto Excento',
                'Consumidor Final',
                'Responsable Monotributo'
            ]);
            $table->enum('tipo_cliente', [
                'P. Fisica',
                'P. Juridica'
            ]);
            $table->enum('forma_pago_habitual', [
                'Contado',
                'TC',
                'TD',
                'Cuentacorriente'
            ]);
            $table->string('direccion', 255);
            $table->string('nombre_razon_social', 255);
            $table->string('email')->unique();
            $table->string('telefono');
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
        Schema::dropIfExists('cliente');
    }
}
