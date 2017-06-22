<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyPlacesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('places', function(Blueprint $table)
		{
			$table->integer('place_id', true);
			$table->string('place_external_id')->nullable();
			$table->text('name', -1)->nullable()->default('');
			$table->string('category')->nullable()->default('');
			$table->string('category_icon')->nullable()->default('');
			$table->string('category_icon_suffix', 100)->nullable()->default('');
			$table->string('country')->nullable()->default('');
			$table->string('state')->nullable()->default('');
			$table->string('city')->nullable()->default('');
			$table->string('latitude', 100)->nullable();
			$table->string('longitude', 100)->nullable();
			$table->timestamps();
			$table->string('address')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_places');
	}

}
