<?php

declare(strict_types=1);

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
        // Create the new column
        Schema::table('covidcase', static function (Blueprint $table): void {
            $table->tinyInteger('age')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the previous column
        Schema::table('covidcase', static function (Blueprint $table): void {
            $table->dropColumn('age');
        });
    }
};
