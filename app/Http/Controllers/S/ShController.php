<?php namespace App\Http\Controllers\S;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\hiyout;
use DB;
use Redirect, Input, Auth;
use App\Func;

class ShController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$kotoshi=date('Y');
		//$usercd=1;
		$usercd = Auth::user()->email;
		session_start();
		$_SESSION["username"]=$usercd;
		$usercd=$_SESSION["username"];

		//年月
		$nengetu = date("Ym");
		//年度
		$nendo = substr($nengetu,0,4);
		//月度
		$getudo = substr($nengetu,4,2);
		//当年月のデータを取得
		/*
		$km_list=DB::table('kmgs')
			->join('users', 'kmgs.shaincd', '=', 'users.id')
			//->join('orders', 'users.id', '=', 'orders.user_id')
			->where('kmgs.shaincd', '=', $usercd)
			->Where('kmgs.nengetu','=',$nengetu)
			->get();
		*/
		$h_list=DB::table('hiyoust')->join('userinfo', 'hiyoust.shaincd', '=', 'userinfo.id')->Where('SHINSEIM','=',$nengetu)->get();

		$arrhlist=array();

		if(count($h_list)>0){
			for($i=0;$i<count($h_list);$i++){
				$arrhlist[$i]=get_object_vars($h_list[$i]);
				//承認ステータスを文字に変換
				$arrhlist[$i]["SHOUNINSTATUS"]=Func::jyotaihenkan($arrhlist[$i]["SHOUNINSTATUS"]);
			}
			//view画面に遷移
			return view('s.sh.show',compact('nendo','kotoshi','getudo','arrhlist'));
		}else{
			//view画面に遷移
			return view('s.sh.show',compact('nendo','kotoshi','getudo','arrhlist'));
		}
	}

	public function store()
	{

		//指定した月を取得
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		$usercd=$_SESSION["username"];

		$nengetu = date('Ym');

		$yesbutton = Input::get('yes-button');

		if($yesbutton == ""){
			$jyotai = "kk";
		}else{
			$jyotai = "sn";
		}



		//承認者id
		session_start();
		//$userid = 2;
		$userid=$_SESSION["username"];
		//承認者名前取得
		$name = DB::table('userinfo')->where('id', $userid)->pluck('name');

		$hiyoustlist = DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('SHINSEIM', '=', $nengetu)->get();


		$arr_hiyoustlist = get_object_vars($hiyoustlist[0]);
		$wlist = hiyout::find($arr_hiyoustlist["id"]);
		//承認者コードを登録
		$wlist->SHOUNINSHACD = $userid;//承認者コード
		//承認者名を登録
		$wlist->SHOUNINSHANM = $name;//承認者名

		if ($jyotai == "sn") {
			$wlist->SHOUNINSTATUS = "3";//承認状態 3:承認
		} else {
			$wlist->SHOUNINSTATUS = "4";//承認状態 4:却下
		}
		$wlist->SHOUNINDATE = date('Ymd');//承認日
		$wlist->save();
		return Redirect::to('s/sh');

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (strlen($id) > 6) {

			//承認却下判断
			$jyotai = substr($id, 0, 2);
			$nengetu = substr($id, 2, 6);
			$username = substr($id, 8);


		}else{

			$kotoshi=date('Y');
			//$usercd=1;
			session_start();
			$usercd=$_SESSION["username"];


			$nengetu = $id;
			$nendo = substr($nengetu,0,4);
			$getudo = substr($nengetu,4,2);

			$h_list=DB::table('hiyoust')
				->join('userinfo', 'hiyoust.shaincd', '=', 'userinfo.id')
				//->join('orders', 'users.id', '=', 'orders.user_id')
				//->where('hiyoust.shaincd', '=', $usercd)
				->Where('hiyoust.SHINSEIM','=',$nengetu)
				->get();


			$arrhlist=array();



			if(count($h_list)>0){
				for($i=0;$i<count($h_list);$i++){
					$arrhlist[$i]=get_object_vars($h_list[$i]);
					$arrhlist[$i]["SHOUNINSTATUS"]=Func::jyotaihenkan($arrhlist[$i]["SHOUNINSTATUS"]);
				}
                //勤務承認画面へ遷移
                return view('s.sh.show',compact('nendo','kotoshi','getudo','arrhlist'));
			}else{
                $arrhlist = null;
                return view('s.sh.show',compact('nendo','kotoshi','getudo','arrhlist'));
            }
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($info)
	{
		//$usercd = "1";
		session_start();
		$usercd=$_SESSION["username"];

		$arr_info = explode("+", $info);

		$time = $arr_info[0];
		$userid = $arr_info[1];

		$Ymd = date('Ymd',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_M = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_sinseibi = '申請日：'.$Ymd_Y.'-'.$Ymd_M.'-'.$Ymd_d.'';
		$Ymd_niki = $Ymd_Y.'-'.$Ymd_M.'-'.$Ymd_d.'';
		$nengetu = date("Ym",strtotime($time));
		$nendo = substr($nengetu,0,4);
		$getudo = substr($nengetu,4,2);




		$h_list=DB::table('hiyoust')
			->join('userinfo', 'hiyoust.shaincd', '=', 'userinfo.id')
			//->join('orders', 'users.id', '=', 'orders.user_id')
			//->where('hiyoust.shaincd', '=', $usercd)
			->Where('hiyoust.SHINSEIM','=',$nengetu)
			->get();

		$arrhlist=array();

		if(count($h_list)>0){
			for($i=0;$i<count($h_list);$i++){
				$arrhlist[$i]=get_object_vars($h_list[$i]);
				$arrhlist[$i]["SHOUNINSTATUS"]=Func::jyotaihenkan($arrhlist[$i]["SHOUNINSTATUS"]);
				//費用類別判断
				if($arrhlist[$i]["SINSEICD"] == 2){

					//日別表からデータを取り出す
					$day_list = Func::getHgbyMonth($Ymd, $userid);

					//計算用
					$sum_sonotahi = 0;//申請金額
					$shinseicd = 0;

					//合計
					foreach($day_list as $key => $value){

						$sum_sonotahi += $value->SONOTAHI;

					}

					$sum_sonotahi = '¥'.number_format($sum_sonotahi, 0);

					//edit画面へ遷移
					return view('s.hg.edit', compact('day_list','Ymd_Y', 'Ymd_M','sum_sonotahi','Ymd_niki'));
				}
				if($arrhlist[$i]["SINSEICD"] == 1){
					//計算用
					$sum_koutuuhi = 0;//交通費
					$shinseicd = 0;

					//日別表からデータを取り出す
					$day_list = Func::getHkbyMonth($Ymd, $userid);
					$Teki_list = Func::getHkTbyMonth($Ymd, $userid);


					//合計
					foreach($day_list as $key => $value) {
						$sum_koutuuhi += $value->KOUTUUHI;
					}

					$sum_koutuuhi = "¥".number_format($sum_koutuuhi, 0);

					return view("s.hk.edit", compact("day_list","Teki_list",'Ymd_Y', 'Ymd_M',"sum_koutuuhi","Ymd_niki"));
				}
				else{
					//計算用
					$sum_sonotahi = 0;//申請金額
					$sum_koutuuhi = 0;
					$sum_shukuhakuhi = 0;
					$shinseicd = 0;

					//日別表からデータを取り出す
					$day_list = Func::getHlbyMonth($Ymd, $userid);

					//合計
					foreach($day_list as $key => $value){
						$sum_sonotahi += $value->SONOTAHI;
						$sum_koutuuhi += $value->KOUTUUHI;
						$sum_shukuhakuhi += $value->SHUKUHAKUHI;
					}

					$sum_sonotahi = "¥".number_format($sum_sonotahi, 0);
					$sum_koutuuhi = "¥".number_format($sum_koutuuhi, 0);
					$sum_shukuhakuhi = "¥".number_format($sum_shukuhakuhi, 0);

					//edit画面に遷移する
					return view("s.hl.edit", compact("day_list",'Ymd_Y', 'Ymd_M',"sum_sonotahi","sum_koutuuhi","sum_shukuhakuhi","Ymd_niki"));
				}
			}
		}else{
			$arrhlist = null;
			return view('s.sh.show',compact('nendo','kotoshi','getudo','arrhlist'));
		}


	}

}
