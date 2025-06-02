<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('inmueble_id')->constrained('inmueble')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'inmueble_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favoritos');
    }
};