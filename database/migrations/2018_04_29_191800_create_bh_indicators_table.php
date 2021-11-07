<?php
/**
 * Created by PhpStorm.
 * User: deakzsolt
 * Date: 2018. 04. 29.
 * Time: 19:19
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBhIndicatorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bh_indicators', function(Blueprint $table) {
			$table->increments('id');
			$table->string('pair', 255)->nullable()->default(0);
			$table->string('signal', 255)->nullable()->default(0);
			$table->dateTime('inserted');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bh_indicators');
	}

}