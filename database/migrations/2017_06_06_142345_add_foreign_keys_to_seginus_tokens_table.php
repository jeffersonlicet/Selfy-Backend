<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_tokens', function(Blueprint $table)
		{
			$table->foreign('user_id', 'seginus_tokens_seginus_users_user_id_fk')->references('user_id')->on('seginus_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_tokens', function(Blueprint $table)
		{
			$table->dropForeign('seginus_tokens_seginus_users_user_id_fk');
		});
	}

}
