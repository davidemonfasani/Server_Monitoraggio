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
        Schema::create('Ass_users_cantine', function (Blueprint $table) {

                $table->id("id_Ass_users_cantine");
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_cantina');
                $table->timestamps();
                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
                $table->foreign('id_cantina')->references('id_cantina')->on('cantine')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Ass_users_cantine');
    }
};
