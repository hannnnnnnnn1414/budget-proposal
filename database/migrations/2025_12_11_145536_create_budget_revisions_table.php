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
        Schema::create('budget_revisions', function (Blueprint $table) {
            $table->id();
            $table->string('sub_id')->nullable();
            $table->string('purpose')->nullable();
            $table->string('acc_id')->nullable();
            $table->string('itm_id')->nullable();
            $table->string('ins_id')->nullable();
            $table->string('description')->nullable();
            $table->string('beneficiary')->nullable();
            $table->string('days')->nullable();
            $table->string('kwh')->nullable();
            $table->string('participant')->nullable();
            $table->string('jenis_training')->nullable();
            $table->string('unit')->nullable();
            $table->string('quantity')->nullable();
            $table->string('price')->nullable();
            $table->string('amount')->nullable();
            $table->string('wct_id')->nullable();
            $table->string('dpt_id')->nullable();
            $table->string('bdc_id')->nullable();
            $table->string('lob_id')->nullable();
            $table->string('month')->nullable();
            $table->string('month_value')->nullable();
            $table->integer('status')->nullable();
            $table->json('pdf_attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_revisions');
    }
};
