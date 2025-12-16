<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budget_finals', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 4);
            $table->string('tipe');
            $table->string('r_nr');
            $table->string('account');
            $table->string('budget_code');
            $table->string('line_of_business');
            $table->string('wc')->nullable();
            $table->string('dept');
            $table->string('dept_code');
            $table->string('criteria_to_master');
            $table->decimal('jan', 20, 2)->default(0);
            $table->decimal('feb', 20, 2)->default(0);
            $table->decimal('mar', 20, 2)->default(0);
            $table->decimal('apr', 20, 2)->default(0);
            $table->decimal('may', 20, 2)->default(0);
            $table->decimal('jun', 20, 2)->default(0);
            $table->decimal('jul', 20, 2)->default(0);
            $table->decimal('aug', 20, 2)->default(0);
            $table->decimal('sep', 20, 2)->default(0);
            $table->decimal('oct', 20, 2)->default(0);
            $table->decimal('nov', 20, 2)->default(0);
            $table->decimal('dec', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->index(['periode', 'tipe', 'dept_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_finals');
    }
};
