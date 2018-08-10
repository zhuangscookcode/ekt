<?php namespace App\Http\Controllers\H;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\hiyou;
use App\hiyout;
use DB;
use App\Func;

use Redirect, Input, Auth;

class HkController extends Controller {


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response

	 */
	public function index(){

		//別途で社員コードを取得する
		//$usercd="1";
		$usercd = Auth::user()->email;
		session_start();
		$_SESSION["username"]=$usercd;
		$usercd=$_SESSION["username"];

		//当年月取得
		$sinseidate=date('Ymd');
		//当年月取得
		$nengetu=date('Ym');
		//当日日付取得
		$page_day=date('Y-m-d');
		//勤務管理表月表当月データあるチェック
		//$wlist= DB::table('hiyous')->where('shaincd', '=', $usercd)->Where('SINSEIDATE','=',$nengetu)->get();
		$hiyoustlist= DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('SHINSEIM','=',$nengetu)->Where('SINSEICD','=',1)->get();


		//勤務管理表日表と月表両方ともデータある場合
		if(count($hiyoustlist)>0){

			$ar=get_object_vars($hiyoustlist["0"]);
			//今月分の申請フラグをチェック
			$month_check=$ar["SHOUNINSTATUS"];
			var_dump($month_check);
			//却下の場合
			if($month_check==4){
				//エディタ画面に遷移する
				return Redirect::to('h/hk/'.$page_day.'/edit');
				//申請と承認の場合
			}elseif($month_check==2 || $month_check==3){
				//show画面に遷移する
				return Redirect::to('h/hk/'.$page_day);
			}
			//ほかの場合
		}else{
			return Redirect::to('h/hk/'.$page_day.'/edit');
		}

	}



	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//指定した月を取得
		$page_day=date('Y-m-d');
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		$usercd=$_SESSION["username"];

		//定期券データを貰う
		for ($i = 0; $i < count(Input::get('sinseidateK')); $i++) {
	    	$rootK = Input::get('rootK.' . $i);
			$sinseidateK = Input::get('sinseidateK.' . $i);
	    	$ekifromK = Input::get('ekifromK.' . $i);
		    $ekitoK = Input::get('ekitoK.' . $i);
		    $koutuuhiK = Input::get('koutuuhiK.' . $i);
		    $SINSEICDK = 1;
		    $bikouK = Input::get('bikouK.' . $i);
		    $hklist_idK = Input::get('idK.' . $i);
		    $TEKIFLG = 1;

		    //定期券データをデータベースに保存
		    //日付はない場合は保存しない
		    if(null != $sinseidateK){
		     	//return Redirect::back()->withInput()->withErrors('保存失败！');
			    if(null != $hklist_idK ) {
			    	//一行のデータを取り出す
			    	$wlistK=hiyou::find($hklist_idK);
				    //項目設定
				    //$wlist=new hiyou();
				    $wlistK->ROOT = $rootK;
				    $wlistK->SINSEIDATE = $sinseidateK;
				    $wlistK->EKIFROM = $ekifromK;
				    $wlistK->EKITO = $ekitoK;
				    $wlistK->KOUTUUHI = $koutuuhiK;
				    $wlistK->SINSEICD = $SINSEICDK;
				    $wlistK->SHAINCD = $usercd;
				    $wlistK->TEKIFLG = $TEKIFLG;
				    $wlistK->BIKOU = $bikouK;
				    $wlistK->updated_at = date('Y-m-d');
				    //データをデータベースに更新
				    $wlistK->save();
			    }elseif(null == $hklist_idK){
				    //新規登録
				    $wlistK=new hiyou();
				    $wlistK->ROOT = $rootK;
				    $wlistK->SINSEIDATE = $sinseidateK;
				    $wlistK->EKIFROM = $ekifromK;
				    $wlistK->EKITO = $ekitoK;
				    $wlistK->KOUTUUHI = $koutuuhiK;
				    $wlistK->SINSEICD = $SINSEICDK;
				    $wlistK->SHAINCD = $usercd;
				    $wlistK->TEKIFLG = $TEKIFLG;
				    $wlistK->BIKOU = $bikouK;
				    $wlistK->updated_at = date('Y-m-d');
				    //データをデータベースに登録
				    $wlistK->save();
			    }
		    }else{
				return Redirect::back()->withInput()->withErrors('日付を選んでください！');
			}

		}


		//交通費データテーブル配列
		for ($i = 0; $i < count(Input::get('sinseidate')); $i++) {
			//画面から情報を取得
			$root = Input::get('root.' . $i);
			$sinseidate = Input::get('sinseidate.' . $i);
			$ekifrom = Input::get('ekifrom.' . $i);
			$ekito = Input::get('ekito.' . $i);
			$koutuuhi = Input::get('koutuuhi.' . $i);
			$SINSEICD = 1;

			//画面から備考情報を取得
			$bikou = Input::get('bikou.' . $i);

			//テーブルのidを取得
			$hklist_id = Input::get('id.'.$i);

			//日付はない場合は保存しない
			if(null != $sinseidate){
				//return Redirect::back()->withInput()->withErrors('保存失败！');
				if(null != $hklist_id ) {
					//一行のデータを取り出す
					$wlist=hiyou::find($hklist_id);
					//項目設定
					//$wlist=new hiyou();
					$wlist->ROOT = $root;
					$wlist->SINSEIDATE = $sinseidate;
					$wlist->EKIFROM = $ekifrom;
					$wlist->EKITO = $ekito;
					$wlist->KOUTUUHI = $koutuuhi;
					$wlist->SINSEICD = $SINSEICD;
					$wlist->SHAINCD = $usercd;
					$wlist->BIKOU = $bikou;
					$wlist->updated_at = date('Y-m-d');
					//データをデータベースに更新
					$wlist->save();
				}elseif(null == $hklist_id){
					//新規登録
					$wlist=new hiyou();
					$wlist->ROOT = $root;
					$wlist->SINSEIDATE = $sinseidate;
					$wlist->EKIFROM = $ekifrom;
					$wlist->EKITO = $ekito;
					$wlist->KOUTUUHI = $koutuuhi;
					$wlist->SINSEICD = $SINSEICD;
					$wlist->SHAINCD = $usercd;
					$wlist->BIKOU = $bikou;
					$wlist->updated_at = date('Y-m-d');
					//データをデータベースに登録
					$wlist->save();
				}
			}else{
				return Redirect::back()->withInput()->withErrors('日付を選んでください！');
			}
		}

		//データ更新
		for ($j = 0; $j < count(Input::get('idD')); $j++) {
			//テーブルのidを取得
			$delList_id = Input::get('idD.'.$j);

			if(null != $delList_id ) {
				//一行のデータを取り出す
				$dlist = hiyou::find($delList_id);
				//データをデータベースに削除
				if(!is_null($dlist)) {
					$dlist->delete();
				}
			}
		}

		//データ更新
		for ($k = 0; $k < count(Input::get('idD1')); $k++) {
			//テーブルのidを取得
			$delListK_id = Input::get('idD1.'.$k);

			if(null != $delListK_id ) {
				//一行のデータを取り出す
				$dlistK = hiyou::find($delListK_id);
				//データをデータベースに削除
				if(!is_null($dlistK)) {
					$dlistK->delete();
				}
			}
		}

		//edit関数に遷移
		return Redirect::to('h/hk/'.$page_day.'/edit');

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $time
	 * @return Response
	 */
	public function show($time)
	{
		session_start();
		$niki=$time;
		//年度取得
		$nengetu=date('Ym',strtotime($niki));
		//ユーザーコード取得
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		$usercd=$_SESSION["username"];
		//
		//$usercd = "1";
		$Ymd = date('Ymd',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_m = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_sinseibi = "申請日：".$Ymd_Y.""-".$Ymd_m."-".$Ymd_d.";
		$Ymd_niki = $Ymd_Y."-".$Ymd_m."-".$Ymd_d."";
		$Ymd_sinseigetsu = "申請：".$Ymd_Y."-".$Ymd_m;

		//計算用
		$sum_koutuuhi = 0;//交通費

		//日別表からデータを取り出す
		$day_list = Func::getHkbyMonth($Ymd, $usercd);
		$Teki_list = Func::getHkTbyMonth($Ymd, $usercd);
		//データなし場合
		if (count($day_list) == 0 && count($Teki_list) == 0) {
			//登録後のデータを取得
			$day_list = Func::getHkbyMonth($Ymd, $usercd);
			$Teki_list = Func::getHkTbyMonth($Ymd, $usercd);
			//edit画面に遷移する
			return view('h.hk.edit', compact('day_list',"Teki_list",'Ymd_sinseibi','sum_koutuuhi','Ymd_niki'));
		} else {

			//合計定期+交通
			foreach($day_list as $key => $value){
				$sum_koutuuhi += $value->KOUTUUHI;
			}
			foreach($Teki_list as $key => $value){
				$sum_koutuuhi += $value->KOUTUUHI;
			}
		}
		//費用類別=1
		$SINSEICD = 1;

		//dbから月別表の当年月のデータを取り出す
		$hiyoustlist= DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('shinseim','=',$nengetu)->Where('SINSEICD','=',1)->get();

		if(count($hiyoustlist)<=0) {
			//なし場合、新規クラス
			$wlist = new hiyout();
		}else{
			$hiyoustlist_0=$hiyoustlist[0];
			$shounincheck=$hiyoustlist_0->SHOUNINSTATUS;

			//ある場合、修正
			$arr_hiyoustlist=get_object_vars($hiyoustlist[0]);
			$wlist=hiyout::find($arr_hiyoustlist["id"]);
		}
		$wlist->SHAINCD = $usercd;
		$wlist->SHINSEIM=$nengetu;
		$wlist->SINSEIY = date('Y',strtotime($niki));
		$wlist->HIYOUST = $sum_koutuuhi;//有給回数
		$wlist->SHINSEIDATE=date('Ymd'); //申請日
		$wlist->SINSEICD = $SINSEICD;//費用類別
		$wlist->SHOUNINSTATUS='2';//承認状態 2:申請　3:承認　4:却下
		$wlist->SHOUNINSHACD="";//承認者コード
		$wlist->SHOUNINSHANM="";//承認者名
		$wlist->SHOUNINDATE="";//承認日
		$wlist->BKU="";//備考
		$wlist->save();


		$sum_koutuuhi = "¥".number_format($sum_koutuuhi, 0);


		//show画面に遷移する
		return view("h.hk.show", compact("day_list","Teki_list","Ymd_sinseigetsu","sum_koutuuhi","Ymd_niki"));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $time
	 * @return Response
	 */
	public function edit($time)
	{
		//
		session_start();
		//$usercd = "1";
		$usercd=$_SESSION["username"];

		$Ymd = date("Ymd",strtotime($time));
		$Ymd_Y = date("Y",strtotime($time));
		$Ymd_m = date("m",strtotime($time));
		$Ymd_d = date("d",strtotime($time));
		$Ymd_sinseibi = "申請日：".$Ymd_Y."-".$Ymd_m."-".$Ymd_d."";
		$Ymd_niki = $Ymd_Y."-".$Ymd_m."-".$Ymd_d."";
		$Ymd_sinseigetsu = "申請：".$Ymd_Y."-".$Ymd_m;

		//計算用
		$sum_koutuuhi = 0;//交通費

		//日別表からデータを取り出す
		$day_list = Func::getHkbyMonth($Ymd, $usercd);
		$Teki_list = Func::getHkTbyMonth($Ymd, $usercd);

		//データなし場合
		if (count($day_list) == 0 && count($Teki_list) == 0) {
			//登録後のデータを取得
			$day_list = Func::getHkbyMonth($Ymd, $usercd);
			$Teki_list = Func::getHkTbyMonth($Ymd, $usercd);
			//edit画面に遷移する
			return view("h.hk.edit", compact("day_list","Teki_list","Ymd_sinseibi","sum_koutuuhi","Ymd_niki"));
		} else {

			//合計
			foreach($day_list as $key => $value){
				$sum_koutuuhi += $value->KOUTUUHI;
			}
		}


		$sum_koutuuhi = "¥".number_format($sum_koutuuhi, 0);


		//月別表当月データあるチェック
		$wlist = DB::table("hiyoust")->where("shaincd", "=", $usercd)->Where("SHINSEIM", "=", $Ymd_Y . $Ymd_m)->Where("SINSEICD","=",1)->get();
		if (count($wlist) > 0) {
			$ar = get_object_vars($wlist["0"]);
			//今月分の申請フラグをチェック
			$month_check = $ar["SHOUNINSTATUS"];
			//申請と承認の場合
			if ($month_check == 2 || $month_check == 3) {
				return view("h.hk.show", compact("day_list","Teki_list","Ymd_sinseigetsu","sum_koutuuhi","Ymd_niki"));
			} else {
				//ほかの場合
				return view("h.hk.edit", compact("day_list","Teki_list","Ymd_sinseibi","sum_koutuuhi","Ymd_niki"));
			}
		}else{
			//月別表当月データなし場合
			return view("h.hk.edit", compact("day_list","Teki_list","Ymd_sinseibi","sum_koutuuhi","Ymd_niki"));
		}
	}








}
