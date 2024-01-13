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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string("nome");
            $table->string("descricao")->nullable();
            $table->decimal("valor",10,2);
            $table->integer("peso");
            $table->integer("largura");
            $table->integer("altura");
            $table->integer("comprimento");
            $table->integer("ativo");
            $table->bigInteger("categoria_id")->unsigned();

            $table->timestamps();

            $table->foreign("categoria_id")
            ->references("id")->on("categorias");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
};
