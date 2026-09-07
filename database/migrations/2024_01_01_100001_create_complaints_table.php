<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number', 30)->unique()->comment('Nomor unik pengaduan, cth: SPR-2024-0001');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('title');
            $table->string('location');
            $table->text('description');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->enum('status', [
                'menunggu',
                'ditinjau',
                'diproses',
                'dalam_perbaikan',
                'selesai',
                'ditolak',
            ])->default('menunggu');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
