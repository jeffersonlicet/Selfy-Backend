<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyUserInformationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_information', function(Blueprint $table)
		{
			$table->integer('information_id', true);
			$table->integer('user_id')->nullable();
			$table->decimal('facebook_id', 20, 0)->nullable();
			$table->decimal('twitter_id', 20, 0)->nullable();
			$table->timestamps();
			$table->string('facebook_email')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_user_information');
	}

}
