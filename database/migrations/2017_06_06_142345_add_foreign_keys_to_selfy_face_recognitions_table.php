<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSelfyFaceRecognitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('face_recognitions', function(Blueprint $table)
		{
			$table->foreign('face_id', 'selfy_face_recognitions_selfy_faces_face_id_fk')->references('face_id')->on('selfy_faces')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('face_recognitions', function(Blueprint $table)
		{
			$table->dropForeign('selfy_face_recognitions_selfy_faces_face_id_fk');
		});
	}

}
