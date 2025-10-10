<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('pengeluarans', function ($table) {
        $table->bigInteger('harga')->nullable()->after('keterangan');
    });
}

public function down()
{
    Schema::table('pengeluarans', function ($table) {
        $table->dropColumn('harga');
    });
}

};
