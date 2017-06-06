<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSelfyJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jobs', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('queue');
			$table->string('payload');
			$table->smallInteger('attempts');
			$table->integer('reserved_at')->nullable();
			$table->integer('available_at');
			$table->integer('created_at');
			$table->index(['queue','reserved_at'], 'jobs_queue_reserved_at_index');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('selfy_jobs');
	}

}
