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
        Schema::create('master_pt', function (Blueprint $table) {
            $table->id();
			$table->string('pt_name');
			$table->string('full_name');
			$table->string('npwp')->nullable();
			$table->string('siup')->nullable();
			$table->string('address')->nullable();
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
        Schema::dropIfExists('master_pt');
    }
};
