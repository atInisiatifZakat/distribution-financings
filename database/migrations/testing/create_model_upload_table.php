<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_upload_files', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignId('user_id');
            $table->string('model_type');
            $table->string('file_name');
            $table->string('storage_disk');
            $table->string('file_path');
            $table->longText('error_message')->nullable();
            $table->string('state', 50);
            $table->timestamps();
        });

        Schema::create('model_upload_records', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('model_upload_file_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->json('payload')->nullable();
            $table->json('meta')->nullable();
            $table->longText('error_message')->nullable();
            $table->longText('exception')->nullable();
            $table->string('model_id', 36)->nullable();
            $table->string('model_type')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_upload_records');
        Schema::dropIfExists('model_upload_files');
    }
};
