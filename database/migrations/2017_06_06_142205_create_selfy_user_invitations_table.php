<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyUserInvitationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_invitations', function(Blueprint $table)
		{
			$table->integer('invitation_id', true);
			$table->integer('user_id')->nullable();
			$table->integer('profile_id')->nullable();
			$table->integer('invitation_status')->nullable()->default(0);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_user_invitations');
	}

}
