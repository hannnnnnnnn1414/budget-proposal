<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql')->create('budget_fy_los', function (Blueprint $table) {
            $table->id();
            $table->string('tipe')->nullable();
            $table->string('periode')->nullable();
            $table->string('r_nr')->nullable();
            $table->string('account')->nullable();
            $table->string('budget_code')->nullable();
            $table->string('line_of_business')->nullable();
            $table->string('wc')->nullable();
            $table->string('dept')->nullable();
            $table->string('dept_code')->nullable();
            $table->string('criteria_to_master')->nullable();
            $table->decimal('jan', 15, 2)->nullable();
            $table->decimal('feb', 15, 2)->nullable();
            $table->decimal('mar', 15, 2)->nullable();
            $table->decimal('apr', 15, 2)->nullable();
            $table->decimal('may', 15, 2)->nullable();
            $table->decimal('jun', 15, 2)->nullable();
            $table->decimal('jul', 15, 2)->nullable();
            $table->decimal('aug', 15, 2)->nullable();
            $table->decimal('sep', 15, 2)->nullable();
            $table->decimal('oct', 15, 2)->nullable();
            $table->decimal('nov', 15, 2)->nullable();
            $table->decimal('dec', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_fy_los');
    }
};