<?php
/**
 * Created by PhpStorm.
 * User: symmetrix
 * Date: 2015/12/03
 * Time: 18:09
 */

use Illuminate\Database\Seeder;
use App\Karenda;

class KarendaTableSeeder extends Seeder {

    public function run()
    {
        DB::table('karendas')->delete();
        for ($i=1;$i<10;$i++) {
            Karenda::create([
                'NENGAPI' => date('Y-m-0' . $i),
                'TEKIYOUKBN' => $i,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ]);
        }
        for ($i=10;$i<=31;$i++) {
            Karenda::create([
             'NENGAPI' => date('Y-m-'.$i),
             'TEKIYOUKBN' => $i,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d'),
            ]);

        }

        for ($i=1;$i<10;$i++) {
            Karenda::create([
                'NENGAPI' => date('Y-m-d',strtotime(date('Y-m-0' . $i).'next month')),
                'TEKIYOUKBN' => $i,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ]);
        }
        for ($i=10;$i<=31;$i++) {
            Karenda::create([
                'NENGAPI' => date('Y-m-d',strtotime(date('Y-m-' . $i).'next month')),
                'TEKIYOUKBN' => $i,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ]);
        }
    }
}