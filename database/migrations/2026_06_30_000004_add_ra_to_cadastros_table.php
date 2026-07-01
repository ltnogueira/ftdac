<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastros', function (Blueprint $table) {
            $table->string('ra')->nullable()->after('apelido');
            $table->index('ra');
        });
    }

    public function down(): void
    {
        Schema::table('cadastros', function (Blueprint $table) {
            $table->dropIndex(['ra']);
            $table->dropColumn('ra');
        });
    }
};
