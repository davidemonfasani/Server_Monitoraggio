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
        Schema::create('Sensors', function (Blueprint $table) {
            $table->id('id_sensor');
            $table->unsignedBigInteger('id_cellar');
            $table->float('TemperaturaMax');
            $table->float('UmiditaMax');
            $table->float('TemperaturaMin');
            $table->float('UmiditaMin');
            $table->float('TemperaturaNow')->nullable(true);
            $table->float('UmiditaNow')->nullable(true);
            $table->float('PesoNow')->nullable(true);
            $table->integer('Timer');
            $table->timestamps();
            $table->foreign('id_cellar')->references('id_cellar')->on('cellars')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Sensors');
    }
};
