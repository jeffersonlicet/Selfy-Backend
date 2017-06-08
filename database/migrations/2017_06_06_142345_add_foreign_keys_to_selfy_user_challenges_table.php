<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyUserChallengesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_challenges', function(Blueprint $table)
		{
			$table->foreign('challenge_id', 'selfy_user_challenges_selfy_challenges_challenge_id_fk')->references('challenge_id')->on('selfy_challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id', 'selfy_user_challenges_selfy_users_user_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('photo_id', 'selfy_user_challenges_selfy_photos_photo_id_fk')->references('photo_id')->on('selfy_photos')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_challenges', function(Blueprint $table)
		{
			$table->dropForeign('selfy_user_challenges_selfy_challenges_challenge_id_fk');
			$table->dropForeign('selfy_user_challenges_selfy_users_user_id_fk');
			$table->dropForeign('selfy_user_challenges_selfy_photos_photo_id_fk');
		});
	}

}
