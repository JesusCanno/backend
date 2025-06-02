<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_negocio', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('business_type')->nullable();
            $table->string('employees')->nullable();
            $table->text('description')->nullable();
            $table->text('services')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitudes_negocio');
    }
};
