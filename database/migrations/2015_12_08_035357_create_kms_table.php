<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKmsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
//		Schema::create('pages', function(Blueprint $table)
//		{
//			$table->increments('id');
//			$table->timestamps();
//		});
		Schema::create('kms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('SHAINCD');  //ŽÐˆõƒR[ƒh
			$table->string('NENGAPI'); //”NŒŽ“ú
			$table->string('NENGETU');//”NŒŽ
			$table->string('NENDO');//”N“x
			$table->tinyInteger('YOUBI');//—j“ú
			$table->tinyInteger('KYUUJITUFLG');//‹x“úƒtƒ‰ƒO
			$table->tinyInteger('KINMUKBN');//‹Î–±‹æ•ª  // 01 —L‹x 02 ‘ã‹x 03 Œß‘O”¼‹x 04 ŒßŒã”¼‹x  05 Œ‡‹Î 06 ‚»‚Ì‘¼
			$table->string('STIME');//oŽÐŽžŠÔ
			$table->string('ETIME');//‘ÞŽÐŽžŠÔ
			$table->float('KINMUTIME');//‹Î–±ŽžŠÔ
			$table->float('ZANGYOUTIMEF');//•’ÊŽc‹Æ
			$table->float('ZANGYOUTIMES');//[–éŽc‹Æ
			$table->float('ZANGYOUTIMEK');//‹x“úŽc‹Æ
			$table->float('TIKOKUTIME');//’xŽžŠÔ
			$table->float('SHORTAGETIME');//•s‘«ŽžŠÔ
			$table->float('SOUTAITIME');//‘‘ÞŽžŠÔ
			$table->string('DAIKYUUDATE');//‘ã‹x“ú
			$table->string('BKU');//”õl
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
		Schema::drop('kms');
	}

}
