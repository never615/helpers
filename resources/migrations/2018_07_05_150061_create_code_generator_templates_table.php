<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;


class CreateCodeGeneratorTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("code_generator_templates", function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');

            $table->string("name");
            $table->text("model")->nullable();
            $table->text("controller")->nullable();
            $table->text("migration")->nullable();
            $table->text("router")->nullable();
            $table->text("menu")->nullable();
            $table->text("permission")->nullable();

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
        Schema::dropIfExists('code_generator_templates');
    }
}
