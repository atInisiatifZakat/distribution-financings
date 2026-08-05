<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->nullable();
            $table->unsignedBigInteger('program_category_id')->nullable();
            $table->unsignedBigInteger('sub_program_category_id')->nullable();
            $table->unsignedInteger('funding_type_id')->nullable();
            $table->string('name');
            $table->integer('edonation_id')->nullable();
            $table->boolean('is_regular')->default(false);
            $table->boolean('is_ramadhan')->default(false);
            $table->date('end_date')->nullable();
            $table->decimal('target', 18, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
