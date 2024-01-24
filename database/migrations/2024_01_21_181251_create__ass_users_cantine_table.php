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
        Schema::create('Ass_cellars', function (Blueprint $table) {

                $table->id("id_Ass_cellar");
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_cellar');
                $table->timestamps();
                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
                $table->foreign('id_cellar')->references('id_cellar')->on('cellars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Ass_cellars');
    }
};
