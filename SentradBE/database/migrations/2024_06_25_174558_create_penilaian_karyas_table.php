<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_karyas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penilai_id')->references('id')->on('penilais')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('regisIndividu_id')->nullable()->references('id')->on('registrasi_individus')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('regisKelompok_id')->nullable()->references('id')->on('registrasi_kelompoks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->datetime('tgl_penilaian');
            $table->integer('total_nilai');
            $table->string('komentar');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penilaian_karyas');
    }
};
