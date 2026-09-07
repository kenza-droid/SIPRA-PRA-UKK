<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->string('image_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('Bytes');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_images');
    }
};
