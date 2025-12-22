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
        Schema::create('budget_plans', function (Blueprint $table) {
            $table->id();

            $table->string('sub_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('purpose', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('acc_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('asset_class', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('itm_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('ins_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('description', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('prioritas', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('alasan', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('keterangan', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('customer', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('position', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->string('beneficiary', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('trip_propose', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->string('destination', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->string('days', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('kwh', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('participant', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('jenis_training', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('unit', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->integer('quantity')->nullable();

            $table->float('price')->nullable();

            $table->float('amount')->nullable();

            $table->string('wct_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('dpt_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('bdc_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('lob_id', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('month', 255)->nullable()->collation('utf8mb4_unicode_ci');

            $table->string('month_value', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->integer('status')->nullable();

            $table->string('version', 255)->nullable()->default('initial')->collation('utf8mb4_general_ci');

            $table->string('revision_id', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->unsignedBigInteger('based_on_final_id')->nullable();

            $table->boolean('is_final_approved')->nullable()->default(false);

            $table->text('validation_errors')->nullable()->collation('utf8mb4_general_ci');

            $table->longText('pdf_attachment')->nullable()->collation('utf8mb4_bin');

            $table->string('business_partner', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->string('ledger_account', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->string('ledger_account_description', 255)->nullable()->collation('utf8mb4_general_ci');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_plans');
    }
};
