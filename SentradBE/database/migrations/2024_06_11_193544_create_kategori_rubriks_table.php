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
        Schema::create('kategori_rubriks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rubrik_id')->references('id')->on('rubriks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('kategori_id')->references('id')->on('kategori_senis')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('kategori_rubriks');
    }
};
