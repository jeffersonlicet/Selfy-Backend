<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyPhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('photos', function(Blueprint $table)
		{
			$table->foreign('user_id', 'FK_selfy_photos_To_selfy_users')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('place_id', 'FK_selfy_photos_To_selfy_places')->references('place_id')->on('selfy_places')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('photo_group', 'FK_selfy_photos_To_selfy_photo_group')->references('photo_group_id')->on('selfy_photo_groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('photos', function(Blueprint $table)
		{
			$table->dropForeign('FK_selfy_photos_To_selfy_users');
			$table->dropForeign('FK_selfy_photos_To_selfy_places');
			$table->dropForeign('FK_selfy_photos_To_selfy_photo_group');
		});
	}

}
