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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmueble_id')->constrained('inmueble')->onDelete('cascade');
            $table->foreignId('propietario_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
