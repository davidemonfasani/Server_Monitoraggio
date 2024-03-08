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
        Schema::create('Cellars', function (Blueprint $table) {
            $table->id('id_cellar');
            $table->string('nome', 35)->unique();
            $table->string('città', 35);
            $table->string('provincia', 35);
            $table->string('via', 35);
            $table->integer('n°_civico');
            $table->integer('dimensioneMq');
            $table->integer('numero_sensori');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Cellars');
    }
};
