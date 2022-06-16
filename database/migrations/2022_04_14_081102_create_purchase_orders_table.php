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
		Schema::dropIfExists('purchase_orders');
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
			$table->string("po_number");
			$table->integer("id_pt");
			$table->integer("id_project");
			$table->integer("id_vendor");
			$table->bigInteger("value");
			$table->bigInteger("vat")->default(0);
			$table->string("top")->nullable();
            $table->date('tod')->nullable();
            $table->longText("description")->nullable();
            //$table->json("payment")->default("[]");
            $table->tinyInteger('status');
            $table->integer("created_by");
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
        Schema::dropIfExists('purchase_orders');
    }
};
