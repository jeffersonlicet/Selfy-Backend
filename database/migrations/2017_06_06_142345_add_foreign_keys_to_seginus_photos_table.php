<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusPhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_photos', function(Blueprint $table)
		{
			$table->foreign('user_id', 'FK_seginus_photos_To_seginus_users')->references('user_id')->on('seginus_users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('place_id', 'FK_seginus_photos_To_seginus_places')->references('place_id')->on('seginus_places')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('photo_group', 'FK_seginus_photos_To_seginus_photo_group')->references('photo_group_id')->on('seginus_photo_groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_photos', function(Blueprint $table)
		{
			$table->dropForeign('FK_seginus_photos_To_seginus_users');
			$table->dropForeign('FK_seginus_photos_To_seginus_places');
			$table->dropForeign('FK_seginus_photos_To_seginus_photo_group');
		});
	}

}
