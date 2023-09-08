<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSchemaVersionToPlace extends Migration
{
    public function up(): void
    {
        Schema::table('place', static function (Blueprint $table): void {
            $table->unsignedInteger('schema_version')->nullable(true);
        });

        DB::table('place')->whereNull('schema_version')->update(['schema_version' => 1]);

        Schema::table('place', static function (Blueprint $table): void {
            $table->unsignedInteger('schema_version')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('place', static function (Blueprint $table): void {
            $table->dropColumn('schema_version');
        });
    }
}
