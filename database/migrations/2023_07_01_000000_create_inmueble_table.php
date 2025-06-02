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
        Schema::create('inmueble', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo');
            $table->string('operacion')->default('venta'); // venta o alquiler
            $table->string('titulo');
            $table->string('foto')->nullable();
            $table->string('direccion');
            $table->decimal('precio', 10, 2);
            $table->integer('habitacion');
            $table->decimal('metro', 8, 2);
            $table->text('descripcion');
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmueble');
    }
};
