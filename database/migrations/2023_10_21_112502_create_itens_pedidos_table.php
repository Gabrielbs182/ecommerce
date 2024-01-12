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
        Schema::create('itens_pedidos', function (Blueprint $table) {
            $table->id();
            $table->integer("quantidade");
            $table->decimal("valor");
            $table->string("numero");
            $table->bigInteger("produto_id")->unsigned();
            $table->bigInteger("pedido_id")->unsigned();
            $table->timestamps();

            $table->foreign("produto_id")
                ->references("id")
                ->on("produtos");

            $table->foreign("pedido_id")
                ->references("id")
                ->on("pedidos")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itens_pedidos');
    }
};
