<?php

use App\Enums\FileUploadStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('path');
            $table->string('extension', 10);
            $table->unsignedMediumInteger('size');
            $table->enum('status', array_column(FileUploadStatus::cases(), 'value'))->default(FileUploadStatus::Pending);
            $table->unsignedMediumInteger('records')->default(0);
            $table->decimal('progress', 5, 2)->unsigned();
            $table->unsignedMediumInteger('downloads')->default(0);

            $table->timestamps();

            $table->index('name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
