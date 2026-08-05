<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_types', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('public_name')->nullable();
            $table->unsignedBigInteger('funding_category_id')->nullable();
            $table->integer('edonation_id')->nullable();
            $table->boolean('print_bsz')->default(false);
            $table->boolean('is_terikat')->default(false);
            $table->boolean('is_dskl')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_types');
    }
};
