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
        Schema::create('phpmyadmin_sessions', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\User::class);
            $table->string('token')->unique();
            $table->string('username');
            $table->string('password');
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phpmyadmin_sessions');
    }
};
