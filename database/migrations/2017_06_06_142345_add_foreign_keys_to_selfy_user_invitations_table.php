<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyUserInvitationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_invitations', function(Blueprint $table)
		{
			$table->foreign('profile_id', 'selfy_user_invitations_selfy_users_user_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('profile_id', 'selfy_user_invitations_selfy_users_profile_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_invitations', function(Blueprint $table)
		{
			$table->dropForeign('selfy_user_invitations_selfy_users_user_id_fk');
			$table->dropForeign('selfy_user_invitations_selfy_users_profile_id_fk');
		});
	}

}
