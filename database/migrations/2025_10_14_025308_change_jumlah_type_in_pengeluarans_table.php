<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            // ubah tipe kolom jumlah jadi integer
            $table->integer('jumlah')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            // kalau di-rollback, balikin ke decimal (misal sebelumnya decimal(10,2))
            $table->decimal('jumlah', 10, 2)->change();
        });
    }
};

