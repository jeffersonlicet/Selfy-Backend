<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusUserChallengesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_user_challenges', function(Blueprint $table)
		{
			$table->foreign('challenge_id', 'seginus_user_challenges_seginus_challenges_challenge_id_fk')->references('challenge_id')->on('seginus_challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id', 'seginus_user_challenges_seginus_users_user_id_fk')->references('user_id')->on('seginus_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_user_challenges', function(Blueprint $table)
		{
			$table->dropForeign('seginus_user_challenges_seginus_challenges_challenge_id_fk');
			$table->dropForeign('seginus_user_challenges_seginus_users_user_id_fk');
		});
	}

}
