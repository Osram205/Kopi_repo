<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('usuarios', 'matricula')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('matricula', 20)->nullable()->unique()->after('nombre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'matricula')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('matricula');
            });
        }
    }
};
