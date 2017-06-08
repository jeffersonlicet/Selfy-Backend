<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyUserChallengesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_challenges', function(Blueprint $table)
		{
			$table->integer('user_challenge_id', true);
			$table->integer('challenge_id');
			$table->integer('user_id');
			$table->integer('challenge_status')->default(0);
			$table->integer('photo_id')->nullable();
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
		Schema::drop('selfy_user_challenges');
	}

}
