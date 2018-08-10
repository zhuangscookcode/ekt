<?php
/**
 * Created by PhpStorm.
 * User: symmetrix
 * Date: 2015/12/03
 * Time: 18:09
 */

use Illuminate\Database\Seeder;
use App\Kmg;

class KmgTableSeeder extends Seeder {

    public function run()
    {

      DB::table('kmgs')->delete();
        for ($i=0;$i<10;$i++) {

$date = date('Y-m-0'.$i);
$datetime = new DateTime($date);
$w = (int)$datetime->format('w');

//$week = array("“ú", "ŒŽ", "‰Î", "…", "–Ø", "‹à", "“y");

            Km::create(
                [
                    'SHAINCD' => 1,
                    'NENGAPI' => $date,
                    'NENGETU' => $date,
                    'NENDO' => $date,
                    'YOUBI' => $w,
                    'KINMUKBN'=> '01',
                    'STIME' => '9:0'.$i,
                    'ETIME' => '19:0'.$i,
                    'KINMUTIME' =>9,
                    'ZANGYOUTIMEF'=>1,
                    'ZANGYOUTIMES'=>0,
                    'ZANGYOUTIMEK'=>0,
                    'DAIKYUUDATE'=>0,
                    'SHORTAGETIME'=>0,
                    'BKU'=>'test',
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]
            );

        }

        for ($i=10;$i<25;$i++) {

            $date = date('Y-m-'.$i);
            $datetime = new DateTime($date);
            $w = (int)$datetime->format('w');

            Km::create(
            [
                'SHAINCD' => 1,
                'NENGAPI' => $date,
                'NENGETU' => $date,
                'NENDO' => $date,
                'YOUBI' => $w,
                'KINMUKBN'=> '02',
                'STIME' => '9:'.$i,
                'ETIME' => '19:'.$i,
                'KINMUTIME' =>9,
                'ZANGYOUTIMEF'=>1,
                'ZANGYOUTIMES'=>0,
                'ZANGYOUTIMEK'=>0,
                'DAIKYUUDATE'=>0,
                'BKU'=>'test',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ]
            );

        }

    }
}