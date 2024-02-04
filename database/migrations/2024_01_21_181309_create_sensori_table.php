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
            $table->float('Temperatura-Max');
            $table->float('Umidità-Max');
            $table->float('Temperatura-Min');
            $table->float('Umidità-Min');
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
