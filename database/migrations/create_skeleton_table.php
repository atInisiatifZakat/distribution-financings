<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_table_name_table', static function (Blueprint $table): void {
            $table->id();

            // add fields

            $table->timestamps();
        });
    }
};
