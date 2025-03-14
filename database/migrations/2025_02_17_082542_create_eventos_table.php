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
        Schema::create('eventos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamps();
            $table->string('nombre');
            $table->integer('stock');
            $table->date('fecha');
            $table->time('hora');
            $table->string('direccion');
            $table->string('ciudad');
            $table->decimal('precio', 8, 2);
            $table->string('foto')->nullable()->default('https://granadateguia.com/wp-content/uploads/2023/02/baile-flamenco-870x480.jpg');
            $table->text('descripcion')->nullable();
            $table->string('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
