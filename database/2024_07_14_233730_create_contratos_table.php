<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 10, 2);
            $table->string('requerimiento', 200);
            $table->string('ubicacion', 200);
            $table->string('departamento', 200);
            $table->string('provincia', 200);
            $table->string('distrito', 200);
            $table->string('descuento', 200);
            $table->foreignId('clienteid')->constrained('clientes')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
