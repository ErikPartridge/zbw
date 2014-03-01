<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePokerCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('PokerCards', function(Blueprint $table) {
			$table->increments('id');
			$table->date('time_dealt');
			$table->string('card', 3);
			$table->integer('pid', 10);
			$table->datetime('discarded');
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
		Schema::drop('PokerCards');
	}

}
