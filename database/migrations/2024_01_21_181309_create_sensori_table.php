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
        Schema::create('Sensores', function (Blueprint $table) {
            $table->id('id_sensore');
            $table->unsignedBigInteger('id_cantina');
            $table->timestamps();
            $table->foreign('id_cantina')->references('id_cantina')->on('cantine')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Sensores');
    }
};
