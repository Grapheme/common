<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMarlboroYaDiskTables extends Migration {

    private $prefix = 'marlboro_';

	public function up(){

        $this->table = $this->prefix . "yadisk";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');

                $table->string('user_id')->default('')->index();
                $table->string('firstname')->default('')->index();
                $table->string('lastname')->default('')->index();
                $table->string('patronymic')->default('')->index();

                $table->string('city')->default('')->index();
                $table->string('yad_name')->default('')->index();
                $table->string('yad_link', 2048)->default('')->index();
                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }
    }


	public function down(){

        Schema::dropIfExists($this->prefix . "yadisk");
        echo(' - ' . $this->prefix . "yadisk" . PHP_EOL);
	}

}

