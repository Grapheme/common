<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEveFacesTables extends Migration {

    private $prefix = 'eve_';

	public function up(){

        $this->table = $this->prefix . "faces";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('status')->unsigned()->default(0)->index();
                $table->string('city')->default('')->index();
                $table->longText('data')->nullable();
                $table->string('image')->default('');
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }
    }


	public function down(){

        Schema::dropIfExists($this->prefix . "faces");
        echo(' - ' . $this->prefix . "faces" . PHP_EOL);
	}

}

