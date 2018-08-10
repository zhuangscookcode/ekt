<?php namespace App\Http\Controllers\K;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Func;
use Redirect, Input, Auth;
use Excel;

class KrController extends Controller
{
	/**
	 * Display the specified resource.
	 *
	 * @param  int $time
	 * @return Response
	 */
	public function show($para)
	{

		if (substr($para, 0, 4) == "show") {
			//引数から年度取得
			$nendo = date('Y');
		}
		else {
			$nendo = $para;
		}
		//今年年月日取得
		$kotoshi = date('Y');
		//$usercd = 1;
		$usercd = Auth::user()->email;
		session_start();
		$_SESSION["username"]=$usercd;
		$usercd=$_SESSION["username"];


		//月別表から当年のデータを取り出す
		$km_list = DB::table('kmgs')
			->join('userinfo', 'kmgs.shaincd', '=', 'userinfo.id')
			//->join('orders', 'users.id', '=', 'orders.user_id')
			->where('kmgs.shaincd', '=', $usercd)
			->Where('kmgs.nendo', '=', $nendo)
			->get();

		$user_list = DB::table('userinfo')->where('id', '=', $usercd)->get();

		$arruserlist[0] = get_object_vars($user_list[0]);

		$arrkmlist = array();

		if (count($km_list) > 0) {
			for ($i = 0; $i < count($km_list); $i++) {
				$arrkmlist[$i] = get_object_vars($km_list[$i]);
				//ステータスコードからステータス漢字に変更
				$arrkmlist[$i]["SHOUNINSTATUS"] = Func::jyotaihenkan($arrkmlist[$i]["SHOUNINSTATUS"]);
			}
			//view画面に遷移
			return view('k.kr.list', compact('nendo', 'kotoshi', 'arrkmlist','arruserlist'));
		} else {
			$arrkmlist = null;
			return view('k.kr.list', compact('nendo', 'kotoshi', 'arrkmlist','arruserlist'));
		}
	}

	public function store(Excel $excel)
	{
		//指定した月を取得
		$nengetu = date('Ym', strtotime(Input::get('nengapi.0')));
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"] = $username;
		$usercd=$_SESSION["username"];

		$arrkmlist = array();

		$kmslist = Func::getKmforexcel($nengetu,$usercd);


		for ($i = 0; $i < count($kmslist); $i++) {
			$arrkmlist[$i] = get_object_vars($kmslist[$i]);
		}


		set_include_path(implode(PATH_SEPARATOR, [
			realpath(__DIR__ . '/Classes'), // assuming Classes is in the same directory as this script
			get_include_path()
		]));

		require_once 'PHPExcel.php';


		//$xl = new PHPExcel();

		Excel::create('当月勤務表', function($excel) use($arrkmlist) {

			$excel->sheet('Excel sheet', function($sheet) use($arrkmlist){

				$sheet->fromArray($arrkmlist);

			});

		})->export('csv');

	}
}


