<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyPhotoReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('photo_reports', function(Blueprint $table)
		{
			$table->foreign('photo_id', 'selfy_photo_reports_selfy_photos_photo_id_fk')->references('photo_id')->on('selfy_photos')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id', 'selfy_photo_reports_selfy_users_user_id_fk')->references('user_id')->on('selfy_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('photo_reports', function(Blueprint $table)
		{
			$table->dropForeign('selfy_photo_reports_selfy_photos_photo_id_fk');
			$table->dropForeign('selfy_photo_reports_selfy_users_user_id_fk');
		});
	}

}
