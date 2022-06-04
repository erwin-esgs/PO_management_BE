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
		Schema::dropIfExists('master_project');
        Schema::create('master_project', function (Blueprint $table) {
            $table->id();
			$table->string("client_name")->nullable();
			$table->string("project_code");
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string("contract_value")->nullable();
            $table->string("finance")->nullable();
            $table->string("project_team")->nullable();
            $table->longText("list_item")->default("[]");
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
        Schema::dropIfExists('master_project');
    }
};
