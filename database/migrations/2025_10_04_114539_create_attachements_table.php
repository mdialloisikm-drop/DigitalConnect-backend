<?php

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
        Schema::create('attachements', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('file_type', ['attachment', 'deliverable']);
            $table->string('file_name');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->enum('format', ['file', 'link'])->default('file');
            $table->timestamps();
            $table->index(['attachable_type', 'attachable_id', 'file_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachements');
    }
};
