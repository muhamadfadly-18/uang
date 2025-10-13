<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained('targets')->onDelete('cascade');
            $table->decimal('nilai_tercapai', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_histories');
    }
};
