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
        Schema::create('Monitoraggios', function (Blueprint $table) {
            $table->id("id_monitoraggio");
            $table->unsignedBigInteger('id_Sensor');
            $table->integer('Temperatura');
            $table->integer('Umidita');
            $table->integer('Peso');
            $table->timestamps();
            $table->foreign('id_Sensor')->references('id_Sensor')->on('Sensors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Monitoraggios');
    }
};
