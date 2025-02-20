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
            $table->increments('id');
            $table->string('cif');
            $table->string('nombre');
            $table->string('direccion');
            $table->string('imagen')->default('storage/empresa1.jpg');
            $table->string('telefono');
            $table->string('email');
            $table->string('cuentaBancaria');
            $table->unsignedInteger('usuario_id')->unique(); // Asegura que un usuario solo tenga una empresa
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->json('lista_eventos')->default(json_encode([]));
            // $table->foreign('evento_id')->references('id')->on('eventos');
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
            $table->foreign('usuario_id')
                ->references('id')
                ->on('users');
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
