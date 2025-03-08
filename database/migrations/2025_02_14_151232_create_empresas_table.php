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
        Schema::create('empresas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('cif');
            $table->string('name');
            $table->string('direccion');
            $table->string('imagen')->default('storage/empresa1.jpg');
            $table->string('telefono');
            $table->string('email');
            $table->string('cuentaBancaria');
            $table->unsignedInteger('usuario_id')->unique();
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
