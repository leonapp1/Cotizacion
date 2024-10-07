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
        Schema::create('observacionescotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacionesid')->constrained('cotizaciones')->onUpdate("cascade")->onDelete('restrict');
            $table->foreignId('observacionesid')->constrained('observaciones')->onUpdate("cascade")->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observacionescotizaciones');
    }
};
