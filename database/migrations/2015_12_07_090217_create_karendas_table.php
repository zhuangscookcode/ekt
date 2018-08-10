<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKarendasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('karendas', function(Blueprint $table)
		{
			$table->tinyInteger('TEKIYOUKBN');//適用区分
			$table->string('NENDO',4);//年度
			$table->string('NENGETU',6);//年月
			$table->string('NENGAPI',10); //年月日
			$table->tinyInteger('YOUBI');//曜日
			$table->tinyInteger('KYUUJITUFLG');//休日フラグ
			$table->string('BKU',60);//備考
			$table->primary(array('TEKIYOUKBN', 'NENGAPI'));//
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
		Schema::drop('karendas');
	}

}
