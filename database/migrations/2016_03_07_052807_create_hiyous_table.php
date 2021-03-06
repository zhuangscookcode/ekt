<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHiyousTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hiyous', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('SHAINCD',40);  //ÐõR[h
			$table->string('SINSEIDATE',8); //\¿ú
			$table->tinyInteger('SINSEICD');//\¿íÊ@1FèúEðÊï@2FïpžZ@3:Œ¥
			$table->bigInteger('SEQ');//s
			$table->string('NENGETU',6);//N
			$table->tinyInteger('ROOT');//Hü@1:JR@2:nºS@3:S@4:^NV[@5:oX@6:»ÌŒ
			$table->string('EKIFROM',20); //o­w
			$table->string('EKITO',20);//w
			$table->string('HASSEIDATE',8);//ïp­¶ú
			$table->float('KOUTUUHI');//ðÊïi~j
			$table->float('SHUKUHAKUHI');//hïi~j
			$table->float('KOUSAIHI');//ðÛïi~j
			$table->float('SONOTAHI');//»ÌŒÌïpi~j
			$table->string('IKISAKI',60);//sæEvWFNgŒ
			$table->string('UTIWAKE',60);//gpÚIEàóER
			$table->string('BIKOU',60);//õl
			$table->tinyInteger('SHOUNINSTATUS');//³FóÔ 2:\¿@3:³F@4:pº
			$table->string('SHOUNINSHACD',7);//³FÒR[h
			$table->string('SHOUNINSHANM',20);//³FÒŒ
			$table->string('SHOUNINDATE',20);//³Fú
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
		Schema::drop('hiyous');
	}

}
