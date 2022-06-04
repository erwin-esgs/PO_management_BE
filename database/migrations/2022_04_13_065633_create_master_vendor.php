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
		Schema::dropIfExists('master_vendor');
        Schema::create('master_vendor', function (Blueprint $table) {
            $table->id();
            $table->string("vendor_name");
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("contact")->nullable();
            $table->string("manager")->nullable();
            $table->string("bank_acc")->nullable();
            $table->longText("description")->nullable();
            $table->integer("created_by");
            $table->timestamp("created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_vendor');
    }
};
