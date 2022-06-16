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
		Schema::dropIfExists('invoice');
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
			$table->string("inv_number");
			$table->integer("id_po");
			$table->integer("id_project");
			$table->date('due_date');
			$table->bigInteger("value");
			$table->bigInteger("vat")->default(0);
			$table->longText("description")->nullable();
			$table->json("payment")->default("[]");
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
        Schema::dropIfExists('invoice');
    }
};
