<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_users', function(Blueprint $table)
		{
			$table->foreign('user_group', 'FK_seginus_users_TO_user_groups')->references('user_group_id')->on('seginus_user_groups')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_users', function(Blueprint $table)
		{
			$table->dropForeign('FK_seginus_users_TO_user_groups');
		});
	}

}
