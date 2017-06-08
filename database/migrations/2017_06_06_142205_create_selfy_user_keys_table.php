<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyUserKeysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_keys', function(Blueprint $table)
		{
			$table->integer('key_id', true);
			$table->string('key_value')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('key_status')->nullable();
			$table->timestamps();
			$table->integer('key_type')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_user_keys');
	}

}
