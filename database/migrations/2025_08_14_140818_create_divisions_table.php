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
        // Membuat tabel divisions
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('division_name')->unique();
            $table->timestamps();
        });

        // Menambahkan kolom division_id ke tabel departments
        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('dpt_id');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus foreign key dan kolom division_id dari tabel departments
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });

        // Menghapus tabel divisions
        Schema::dropIfExists('divisions');
    }
};