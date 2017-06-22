<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyFacesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('faces', function(Blueprint $table)
		{
			$table->foreign('user_id', 'FK_selfy_faces_To_selfy_users')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('faces', function(Blueprint $table)
		{
			$table->dropForeign('FK_selfy_faces_To_selfy_users');
		});
	}

}
