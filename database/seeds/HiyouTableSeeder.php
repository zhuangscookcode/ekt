<?php
/**
 * Created by PhpStorm.
 * User: symmetrix
 * Date: 2015/12/03
 * Time: 18:09
 */

use Illuminate\Database\Seeder;
use App\hiyou;

class HiyouTableSeeder extends Seeder {

    public function run()
    {
        DB::table('hiyous')->delete();
/*
        $table->increments('id');
        $table->string('SHAINCD',40);  //�Ј��R�[�h
        $table->string('SINSEIDATE',8); //�\����
        $table->tinyInteger('SINSEICD');//�\����ʁ@1�F������E��ʔ�@2�F��p���Z�@3:������
        $table->bigInteger('SEQ');//�s
        $table->string('NENGETU',6);//�N��
        $table->tinyInteger('ROOT');//�H���@1:JR�@2:�n���S�@3:���S�@4:�^�N�V�[�@5:�o�X�@6:���̑�
        $table->string('EKIFROM',20); //�o���w
        $table->string('EKITO',20);//�����w
        $table->string('HASSEIDATE',8);//��p������
        $table->float('KOUTUUHI');//��ʔ�i�~�j
        $table->float('SHUKUHAKUHI');//�h����i�~�j
        $table->float('KOUSAIHI');//���۔�i�~�j
        $table->float('SONOTAHI');//���̑��̔�p�i�~�j
        $table->string('IKISAKI',60);//�s��E�v���W�F�N�g��
        $table->string('UTIWAKE',60);//�g�p�ړI�E����E���R
        $table->string('BIKOU',60);//���l
        $table->tinyInteger('SHOUNINSTATUS');//���F��� 2:�\���@3:���F�@4:�p��
        $table->string('SHOUNINSHACD',7);//���F�҃R�[�h
        $table->string('SHOUNINSHANM',20);//���F�Ҏ���
        $table->string('SHOUNINDATE',20);//���F��
        $table->timestamps();
*/
            hiyou::create(
                [
                    'SHAINCD' => 1,
                    'SINSEIDATE' => date('Y-m-d'),
                    'SINSEICD' => 3,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]
            );

    }
}