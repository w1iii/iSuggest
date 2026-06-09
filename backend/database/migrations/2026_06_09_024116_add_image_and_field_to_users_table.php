<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('email');
            $table->string('field')->nullable()->after('Title');
            $table->renameColumn('Title', 'title');
            $table->dropColumn('PhoneNumber');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('title', 'Title');
            $table->string('PhoneNumber')->nullable();
            $table->dropColumn(['image', 'field']);
        });
    }
};
