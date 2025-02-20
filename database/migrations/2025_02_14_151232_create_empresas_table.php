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
            $table->string('name');
            $table->string('direccion');
            $table->string('imagen');
            $table->string('telefono');
            $table->string('email');
            $table->string('cuentaBancaria');
            $table->unsignedInteger('usuario_id');
           // $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->json('lista_eventos')->nullable();
           // $table->foreign('evento_id')->references('id')->on('eventos');
            $table->boolean('isDeleted');
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
