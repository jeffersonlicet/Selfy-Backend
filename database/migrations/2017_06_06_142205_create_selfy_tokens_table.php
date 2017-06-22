<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tokens', function(Blueprint $table)
		{
			$table->integer('token_id', true);
			$table->integer('user_id');
			$table->text('public_key', -1);
			$table->string('device_id')->nullable();
			$table->string('device_os')->nullable();
			$table->text('private_key', -1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_tokens');
	}

}
