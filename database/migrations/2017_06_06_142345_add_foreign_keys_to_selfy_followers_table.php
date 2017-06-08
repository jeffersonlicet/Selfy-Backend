<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyFollowersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('followers', function(Blueprint $table)
		{
			$table->foreign('follower_id', 'selfy_followers_selfy_users_user_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('followers', function(Blueprint $table)
		{
			$table->dropForeign('selfy_followers_selfy_users_user_id_fk');
		});
	}

}
