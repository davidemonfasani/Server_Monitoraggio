<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('Users', function (Blueprint $table) {
            $table->id("id_user");
            $table->string('nome', 35);
            $table->string('cognome', 35);
            $table->string('email', 55)->unique();
            $table->string('password', 300);
            $table->string('foto',255)->nullable();
            $table->timestamps();
        });
        Schema::dropIfExists('personal_access_tokens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Users');
    }
};
