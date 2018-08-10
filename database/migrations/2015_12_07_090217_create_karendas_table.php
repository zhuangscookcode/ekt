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
			$table->tinyInteger('TEKIYOUKBN');//�K�p�敪
			$table->string('NENDO',4);//�N�x
			$table->string('NENGETU',6);//�N��
			$table->string('NENGAPI',10); //�N����
			$table->tinyInteger('YOUBI');//�j��
			$table->tinyInteger('KYUUJITUFLG');//�x���t���O
			$table->string('BKU',60);//���l
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
