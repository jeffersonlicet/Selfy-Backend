<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyChallengesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('challenges', function(Blueprint $table)
		{
			$table->integer('challenge_id', true);
			$table->string('object_type', 100);
			$table->timestamps();
			$table->bigInteger('completed_count')->nullable()->default(0);
			$table->integer('object_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_challenges');
	}

}
