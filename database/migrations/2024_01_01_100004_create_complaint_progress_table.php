<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->integer('progress_percentage')->default(0)->comment('0-100');
            $table->string('image_path')->nullable()->comment('Foto dokumentasi progres');
            $table->date('work_date')->nullable()->comment('Tanggal pengerjaan');
            $table->timestamps();

            $table->index(['complaint_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_progress');
    }
};
