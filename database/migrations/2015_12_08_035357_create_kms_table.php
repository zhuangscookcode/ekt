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
			$table->string('SHAINCD');  //�Ј��R�[�h
			$table->string('NENGAPI'); //�N����
			$table->string('NENGETU');//�N��
			$table->string('NENDO');//�N�x
			$table->tinyInteger('YOUBI');//�j��
			$table->tinyInteger('KYUUJITUFLG');//�x���t���O
			$table->tinyInteger('KINMUKBN');//�Ζ��敪  // 01 �L�x 02 ��x 03 �ߑO���x 04 �ߌ㔼�x  05 ���� 06 ���̑�
			$table->string('STIME');//�o�Ў���
			$table->string('ETIME');//�ގЎ���
			$table->float('KINMUTIME');//�Ζ�����
			$table->float('ZANGYOUTIMEF');//���ʎc��
			$table->float('ZANGYOUTIMES');//�[��c��
			$table->float('ZANGYOUTIMEK');//�x���c��
			$table->float('TIKOKUTIME');//�x������
			$table->float('SHORTAGETIME');//�s������
			$table->float('SOUTAITIME');//���ގ���
			$table->string('DAIKYUUDATE');//��x��
			$table->string('BKU');//���l
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
