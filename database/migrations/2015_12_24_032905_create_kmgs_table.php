<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKmgsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kmgs', function(Blueprint $table)
		{
			$table->increments('id');
				$table->string('SHAINCD',40);  //�Ј��R�[�h

				$table->string('NENGETU',6);//�N��
				$table->string('NENDO',4);//�N�x

				$table->string('BUMONCD',5);//����R�[�h

				$table->float('STANDARDDAY');//���ԕW���Ζ�����
				$table->float('STANDARDTIME');//���ԕW���Ζ�����

				$table->float('KINMUDAY');//�Ζ�����
				$table->float('KINMUTIME');//�Ζ�����

				$table->float('ZANGYOUTIMEF');//���ʎc��
				$table->float('ZANGYOUTIMES');//�[��c��
				$table->float('ZANGYOUTIMEK');//�x���c��
				$table->float('SHORTAGETIME');//�s������

				$table->float('TIKOKUTIME');//�x������
				$table->float('SOUTAITIME');//���ގ���

				$table->float('YUUKYUUCNT');//�L����
				$table->float('HANKYUUCNT');//���x��

				$table->float('YUUKYUUZAN');//�L���c��
				$table->float('DAIKYUUCNT');//��x��
				$table->float('KEKKINCNT');//���Ή�
				$table->float('SONOTACNT');//���̑���

				$table->string('UPDATEDATE',8); //�X�V��

				$table->string('SINSEIDATE',8); //�\����
				$table->tinyInteger('SHOUNINSTATUS');//���F��� 2:�\���@3:���F�@4:�p��

				$table->string('SHOUNINSHACD',7);//���F�҃R�[�h
				$table->string('SHOUNINSHANM',20);//���F�Җ�
				$table->string('SHOUNINDATE',20);//���F��
				$table->string('BKU',60);//���l
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
		Schema::drop('kmgs');
	}

}
