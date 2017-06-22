<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyPhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('photos', function(Blueprint $table)
		{
			$table->integer('photo_id', true);
			$table->integer('user_id');
			$table->integer('place_id')->nullable();
			$table->integer('photo_group')->nullable()->default(1);
			$table->string('caption')->nullable()->default('');
			$table->string('url')->nullable();
			$table->bigInteger('likes_count')->default(0);
			$table->bigInteger('comments_count')->default(0);
			$table->bigInteger('views_count')->default(0);
			$table->timestamps();
			$table->integer('reports_count')->nullable()->default(0);
			$table->integer('adult_content')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_photos');
	}

}
