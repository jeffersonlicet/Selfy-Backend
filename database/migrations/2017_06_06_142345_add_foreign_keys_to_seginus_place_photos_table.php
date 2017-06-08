<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusPlacePhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_place_photos', function(Blueprint $table)
		{
			$table->foreign('place_id', 'FK_seginus_place_photos_TO_places')->references('place_id')->on('seginus_places')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_place_photos', function(Blueprint $table)
		{
			$table->dropForeign('FK_seginus_place_photos_TO_places');
		});
	}

}
