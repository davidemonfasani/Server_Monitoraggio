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
        Schema::create('Keep_alives', function (Blueprint $table) {
            $table->id("id_keep_alive");
            $table->unsignedBigInteger('id_sensore');
            $table->timestamps();
            $table->foreign('id_sensore')->references('id_sensore')->on('sensori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Keep_alives');
    }
};
