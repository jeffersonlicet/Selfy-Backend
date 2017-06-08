<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->integer('user_id', true);
			$table->string('username')->nullable()->unique('unique_username');
			$table->string('email')->nullable();
			$table->string('password')->nullable();
			$table->string('firstname')->nullable()->default('');
			$table->string('lastname')->nullable()->default('');
			$table->string('bio')->nullable()->default('');
			$table->text('firebase_token', -1)->nullable();
			$table->timestamps();
			$table->integer('user_group')->nullable()->default(1);
			$table->string('user_locale', 50)->nullable();
			$table->bigInteger('spot_completed')->nullable()->default(0);
			$table->bigInteger('duo_completed')->default(0);
			$table->bigInteger('play_completed')->default(0);
			$table->bigInteger('spot_todo')->nullable()->default(0);
			$table->bigInteger('duo_todo')->nullable()->default(0);
			$table->bigInteger('play_todo')->nullable()->default(0);
			$table->string('reset_password_token')->nullable();
			$table->string('reset_password_sent_at')->nullable();
			$table->softDeletes();
			$table->integer('followers_count')->nullable()->default(0);
			$table->integer('following_count')->nullable()->default(0);
			$table->string('avatar')->nullable()->default('http://i.imgur.com/4sfeHin.jpg');
			$table->integer('photos_count')->nullable()->default(0);
			$table->integer('duo_enabled')->nullable()->default(1);
			$table->integer('spot_enabled')->nullable()->default(1);
			$table->integer('account_private')->nullable()->default(0);
			$table->integer('account_verified')->nullable()->default(0);
			$table->integer('save_photos')->nullable()->default(1);
			$table->integer('gender')->nullable()->default(0);
			$table->dateTime('birthday')->nullable();
			$table->integer('location_id')->nullable();
			$table->integer('facebook')->nullable()->default(0);
			$table->integer('twitter')->nullable()->default(0);
			$table->string('facebook_token')->nullable();
			$table->integer('play_enabled')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_users');
	}

}
