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
        Schema::create('insurance_prems', function (Blueprint $table) {
            $table->id();
            $table->string('sub_id');
            $table->string('purpose');
            $table->string('acc_id');
            $table->string('description')->nullable();
            $table->string('ins_id');
            $table->string('quantity');
            $table->string('price');
            $table->string('amount');
            $table->string('wct_id')->nullable();;
            $table->string('dpt_id');
            $table->string('bdc_id');
            $table->string('month');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_prems');
    }
};
