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
        Schema::create('cantine', function (Blueprint $table) {
            $table->id('id_cantina');
            $table->string('nome', 35);
            $table->string('città', 35);
            $table->string('provincia', 35);
            $table->string('via', 35);
            $table->integer('n°_civico', 15);
            $table->integer('dimensione Mq', 35);
            $table->integer('numero_sensori', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantine');
    }
};
