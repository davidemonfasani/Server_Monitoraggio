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
            $table->id('id_Sensor');
            $table->unsignedBigInteger('id_cellar');
            $table->float('TemperaturaMax');
            $table->float('UmiditàMax');
            $table->float('TemperaturaMin');
            $table->float('UmiditàMin');
            $table->float('TemperaturaNow');
            $table->float('UmiditàNow');
            $table->float('PesoNow');
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
