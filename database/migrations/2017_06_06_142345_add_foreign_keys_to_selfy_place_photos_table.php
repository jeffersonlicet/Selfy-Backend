<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyPlacePhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('place_photos', function(Blueprint $table)
		{
			$table->foreign('place_id', 'FK_selfy_place_photos_TO_places')->references('place_id')->on('selfy_places')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('place_photos', function(Blueprint $table)
		{
			$table->dropForeign('FK_selfy_place_photos_TO_places');
		});
	}

}
