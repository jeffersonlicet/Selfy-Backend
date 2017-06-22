<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyUserInformationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_information', function(Blueprint $table)
		{
			$table->foreign('user_id', 'selfy_user_information_selfy_users_user_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_information', function(Blueprint $table)
		{
			$table->dropForeign('selfy_user_information_selfy_users_user_id_fk');
		});
	}

}
