<?php

use App\Models\DatabaseInfo;
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
        Schema::create('login_nonces', function (Blueprint $table) {
            $table->foreignIdFor(DatabaseInfo::class);
            $table->string('nonce')->unique();
            $table->timestamps();
            $table->primary(['database_info_id', 'nonce']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_nonces');
    }
};
