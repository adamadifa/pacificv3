<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuranglebihsetorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keuangan_kuranglebihsetor', function (Blueprint $table) {
            $table->char('kode_kl', 9)->primary();
            $table->date('tanggal');
            $table->char('kode_salesman', 7);
            $table->integer('uang_kertas');
            $table->integer('uang_logam');
            $table->char('jenis_bayar', 1);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keuangan_kuranglebihsetor');
    }
}
