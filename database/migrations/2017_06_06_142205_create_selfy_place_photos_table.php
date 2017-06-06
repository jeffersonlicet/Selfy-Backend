<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyPlacePhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('place_photos', function(Blueprint $table)
		{
			$table->integer('photo_id', true);
			$table->integer('place_id');
			$table->string('photo_prefix')->nullable();
			$table->string('photo_suffix')->nullable();
			$table->string('photo_width')->nullable();
			$table->string('photo_height')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_place_photos');
	}

}
