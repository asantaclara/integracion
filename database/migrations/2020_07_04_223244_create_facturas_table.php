<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('cliente');
            $table->date('fecha');
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->decimal('monto', 8, 2);
            $table->enum('forma_pago', [
                'Contado',
                'TC',
                'TD',
                'Cuentacorriente'
            ]);
            $table->boolean('pagada');
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
        Schema::dropIfExists('factura');
    }
}
