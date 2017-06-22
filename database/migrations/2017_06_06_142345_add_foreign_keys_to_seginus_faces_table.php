<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusFacesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_faces', function(Blueprint $table)
		{
			$table->foreign('user_id', 'FK_seginus_faces_To_seginus_users')->references('user_id')->on('seginus_users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_faces', function(Blueprint $table)
		{
			$table->dropForeign('FK_seginus_faces_To_seginus_users');
		});
	}

}
