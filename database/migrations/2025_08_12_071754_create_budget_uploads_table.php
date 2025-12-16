<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_uploads', function (Blueprint $table) {
            $table->id();

            $table->year('year');
            $table->enum('type', ['last_year', 'outlook', 'proposal', 'final']);
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->json('data');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_uploads');
    }
};
