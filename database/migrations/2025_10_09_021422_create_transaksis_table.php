<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->string('keterangan');
            $table->decimal('jumlah', 15, 2);
             $table->foreignId('user_id')->constrained()->onDelete('cascade'); // relasi user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
