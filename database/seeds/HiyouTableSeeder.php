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
        $table->string('SHAINCD',40);  //社員コード
        $table->string('SINSEIDATE',8); //申請日
        $table->tinyInteger('SINSEICD');//申請種別　1：定期券・交通費　2：費用精算　3:仮払い
        $table->bigInteger('SEQ');//行
        $table->string('NENGETU',6);//年月
        $table->tinyInteger('ROOT');//路線　1:JR　2:地下鉄　3:私鉄　4:タクシー　5:バス　6:その他
        $table->string('EKIFROM',20); //出発駅
        $table->string('EKITO',20);//到着駅
        $table->string('HASSEIDATE',8);//費用発生日
        $table->float('KOUTUUHI');//交通費（円）
        $table->float('SHUKUHAKUHI');//宿泊費（円）
        $table->float('KOUSAIHI');//交際費（円）
        $table->float('SONOTAHI');//その他の費用（円）
        $table->string('IKISAKI',60);//行先・プロジェクト名
        $table->string('UTIWAKE',60);//使用目的・内訳・事由
        $table->string('BIKOU',60);//備考
        $table->tinyInteger('SHOUNINSTATUS');//承認状態 2:申請　3:承認　4:却下
        $table->string('SHOUNINSHACD',7);//承認者コード
        $table->string('SHOUNINSHANM',20);//承認者氏名
        $table->string('SHOUNINDATE',20);//承認日
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