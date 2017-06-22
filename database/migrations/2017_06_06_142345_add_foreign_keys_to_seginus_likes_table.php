<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusLikesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_likes', function(Blueprint $table)
		{
			$table->foreign('user_id', 'seginus_likes_seginus_users_user_id_fk')->references('user_id')->on('seginus_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('photo_id', 'seginus_likes_seginus_photos_photo_id_fk')->references('photo_id')->on('seginus_photos')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_likes', function(Blueprint $table)
		{
			$table->dropForeign('seginus_likes_seginus_users_user_id_fk');
			$table->dropForeign('seginus_likes_seginus_photos_photo_id_fk');
		});
	}

}
