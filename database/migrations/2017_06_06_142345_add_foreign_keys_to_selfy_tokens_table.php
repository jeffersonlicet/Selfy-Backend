<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tokens', function(Blueprint $table)
		{
			$table->foreign('user_id', 'selfy_tokens_selfy_users_user_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tokens', function(Blueprint $table)
		{
			$table->dropForeign('selfy_tokens_selfy_users_user_id_fk');
		});
	}

}
