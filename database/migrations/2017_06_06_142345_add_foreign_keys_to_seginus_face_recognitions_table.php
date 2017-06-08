<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSeginusFaceRecognitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('s_face_recognitions', function(Blueprint $table)
		{
			$table->foreign('face_id', 'seginus_face_recognitions_seginus_faces_face_id_fk')->references('face_id')->on('seginus_faces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('s_face_recognitions', function(Blueprint $table)
		{
			$table->dropForeign('seginus_face_recognitions_seginus_faces_face_id_fk');
		});
	}

}
