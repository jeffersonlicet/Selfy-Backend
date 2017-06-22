<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusFollowersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_followers', function(Blueprint $table)
		{
			$table->foreign('follower_id', 'seginus_followers_seginus_users_user_id_fk')->references('user_id')->on('seginus_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_followers', function(Blueprint $table)
		{
			$table->dropForeign('seginus_followers_seginus_users_user_id_fk');
		});
	}

}
