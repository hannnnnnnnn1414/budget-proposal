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
        Schema::connection('mysql')->create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('id_user');
            $table->string('otp');
            $table->dateTime('expiry_date');
            $table->string('send');
            $table->dateTime('send_date')->nullable();
            $table->string('use');
            $table->dateTime('use_date')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
