<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->guid('id')->primary('PK__selfy_no__3213E83F83EA938B');
			$table->string('type');
			$table->integer('notifiable_id');
			$table->string('notifiable_type');
			$table->string('data');
			$table->dateTime('read_at')->nullable();
			$table->timestamps();
			$table->index(['notifiable_id','notifiable_type'], 'notifications_notifiable_id_notifiable_type_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_notifications');
	}

}
