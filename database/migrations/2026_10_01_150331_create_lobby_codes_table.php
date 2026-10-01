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
        Schema::create('lobby_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('reward');
            $table->string('requirement')->nullable();
            $table->boolean('is_expired')->default(false);
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lobby_codes');
    }
};
