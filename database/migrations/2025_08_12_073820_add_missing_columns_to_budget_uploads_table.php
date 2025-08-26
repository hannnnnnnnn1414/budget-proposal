<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToBudgetUploadsTable extends Migration
{
    public function up()
    {
        Schema::table('budget_uploads', function (Blueprint $table) {
            $table->enum('type', ['last_year', 'outlook', 'proposal'])->after('year');
            $table->string('file_path')->after('type');
            $table->foreignId('uploaded_by')->constrained('users')->after('file_path');
            $table->json('data')->after('uploaded_by');
        });
    }

    public function down()
    {
        Schema::table('budget_uploads', function (Blueprint $table) {
            $table->dropColumn(['type', 'file_path', 'uploaded_by', 'data']);
        });
    }
}
