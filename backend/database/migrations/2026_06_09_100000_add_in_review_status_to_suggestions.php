<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            // Modify enum to include 'In Review'
            $table->enum('status', ['Pending', 'In Review', 'Approved', 'Rejected', 'Implemented'])->default('Pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Implemented'])->default('Pending')->change();
        });
    }
};
