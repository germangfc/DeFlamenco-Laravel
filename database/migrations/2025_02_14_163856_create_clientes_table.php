<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('dni')->unique();
            $table->string('foto_dni')->nullable();
            $table->string('avatar')->nullable();
            $table->json('lista_entradas')->nullable();
            $table->timestamps();
            $table->boolean('is_deleted')->default(false);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
