<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;


class CreateCodeGeneratorConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("code_generator_configs", function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');

            $table->string("name");
            $table->string("model_namespace")->nullable();
            $table->string("controller_namespace")->nullable();
            $table->string("model_path")->nullable();
            $table->string("controller_path")->nullable();
            $table->string("migration_path")->nullable();
            $table->string("route_path")->nullable();
            $table->string("permission_seeder_path")->nullable();
            $table->string("menu_seeder_path")->nullable();
            $table->string("run_seeder_command")->nullable()->comment("执行seeder的命令");

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
        Schema::dropIfExists('code_generator_configs');
    }
}
