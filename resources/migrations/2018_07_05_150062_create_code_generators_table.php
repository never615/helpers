<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;


class CreateCodeGeneratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("code_generators", function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');


            $table->unsignedInteger("config_id");
            $table->json("configs");
            $table->json("fields");


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
        Schema::dropIfExists('code_generators');
    }
}
