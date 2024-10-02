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
        Schema::create('detalles_contratos', function (Blueprint $table) {
            $table->id();
            $table->decimal("cantidad",10,2);
            $table->decimal("precio_unitario",10,2);
            $table->decimal("subtotal",10,2);
            $table->foreignId('contratoid')->constrained('contratos')->onUpdate("cascade")->onDelete('restrict');
            $table->foreignId('productoid')->constrained('productos')->onUpdate("cascade")->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_contratos');
    }
};
