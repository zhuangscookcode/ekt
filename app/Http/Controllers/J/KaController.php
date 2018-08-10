<?php namespace App\Http\Controllers\J;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Karenda;
use DB;
use App\Func;
use Illuminate\Http\Request;
use Redirect, Input, Auth;

class KaController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$kotoshi=date('Y');
		$nendo=date('Y');
		return view('j.ka.show', compact('kotoshi','nendo'));
	}
	public function edit($time)
	{
		if (substr($time, 0, 2) != "tj")
		{
            $Now_time = $time;
			$Ymd_Y = date('Y', strtotime($time));
			$Ymd_m = date('m', strtotime($time));
			$Ymd_ym = date('Ym', strtotime($time));
			$day_list = DB::table('karendas')->where('nengetu', '=', $Ymd_ym)->get();
			if (count($day_list) > 0)
			{
				foreach ($day_list as $ss)
				{
					$ss->YOUBI = Func::weekformart($ss->YOUBI);
				}

				return view('j.ka.edit', compact('day_list', 'Ymd_Y', 'Ymd_m','Now_time'));
			}
		}else{
			var_dump("sdjfskdf");

		}


	}



public function store(Request $request){

	$kubun=Input::get("kubun");
 //   var_dump(count(Input::get('bku')));
	if($kubun=="show")
	{
		$kinmukbn = Input::get('nendo');
		$nendo = $kinmukbn;
		//年度チェック
		$karendasCheck = "SELECT nengapi "
			. "FROM karendas "
			. "WHERE karendas.nendo = '" . $nendo . "'";
		$karendasExist = DB::select($karendasCheck);

		if ($karendasExist == null) {//カレンダーがない場合
			$year = $nendo;
			$karendasFirstday = array();
			for ($i = 1; $i <= 12; $i++) {//毎月の1日を作成する
				$firstDay = $year . "-" . $i . "-01";
				$firstStandard = date("Y-m-d", strtotime($firstDay));
				$karendasFirstday[] = $firstStandard;
			}
			$allDay = array();
			foreach ($karendasFirstday as $key => $value) {
				$valueComputer = strtotime($value);
				$karendasLastday = date("Y-m-t", $valueComputer);
				$firstDay = substr($value, 8, 2);
				$lastDay = substr($karendasLastday, 8, 2);
				for ($i = $firstDay; $i <= $lastDay; $i++) {//毎日を作成する
					$thisMonthday = substr($value, 0, 8) . $i;
					$standardMonthday = strtotime($thisMonthday);
					$allDay[] = date("Y-m-d", $standardMonthday);
				}
			}
			foreach ($allDay as $key => $value) {//データベースに入れる


				$kyujitsuflg = 0;
				//土曜日と日曜日判断
				if (date('w', strtotime($value)) == 0 || date('w', strtotime($value)) == 6) {
					$kyujitsuflg = 1;
				}
				//休日判断
				$holiday = Func::ktHolidayName($value);
				if ($holiday != "") {
					$kyujitsuflg = 1;
				}
				//登録sql文を作成
				$karendasInsert = "INSERT INTO"
					. " karendas (nendo,nengetu,nengapi,youbi,kyuujituflg,bku,created_at,updated_at)"
					. " VALUES"
					. " ('" . date('Y', strtotime($value))
					. "', '" . date('Ym', strtotime($value))
					. "', '" . date('Y-m-d', strtotime($value))
					. "', " . date('w', strtotime($value))
					. ", " . $kyujitsuflg
					. ", '" . $holiday
					. "', '" . date('Y-m-d')
					. "', '" . date('Y-m-d') . "')";
				DB::insert($karendasInsert);


			}
		}
		$kotoshi = date('Y');

		return view('j.ka.show', compact('kotoshi', 'nendo'));
	}
	else
	{//一年間のカレンダーを人工的に変更する（休日、平日、備考）
        $Now_time = Input::get('Now_time');
		//テーブル配列
		for ($i = 0; $i < count(Input::get('nengapi')); $i++) {
            //画面から情報を取得
            $nengapi = Input::get('nengapi.'.$i);
            $kyuujituflg = Input::get('kyuujituflg'.$nengapi);
            $bku = Input::get('bku.'.$i);
            //更新sql文を作成
            $karendasUpdate = "UPDATE"
                . " karendas SET kyuujituflg = '"
                . $kyuujituflg.
            "', bku = '"
                .$bku.
            "', updated_at = '"
                .date('Y-m-d') .
            "' WHERE nengapi = '"
            .$nengapi."'";
            DB::update($karendasUpdate);
		}
        return Redirect::to('j/ka/'.$Now_time.'/edit');
	}
}

}
