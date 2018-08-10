<?php namespace App\Http\Controllers\H;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\hiyou;
use App\hiyout;
use DB;
use App\Func;

use Redirect, Input, Auth;

class HlController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
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
		$hiyoustlist= DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('SHINSEIM','=',$nengetu)->Where('SINSEICD','=',3)->get();


		//勤務管理表日表と月表両方ともデータある場合
		if(count($hiyoustlist)>0){

			$ar=get_object_vars($hiyoustlist["0"]);
			//今月分の申請フラグをチェック
			$month_check=$ar["SHOUNINSTATUS"];
			//却下の場合
			if($month_check==4){
				//エディタ画面に遷移する
				return Redirect::to('h/hl/'.$page_day.'/edit');
				//申請と承認の場合
			}elseif($month_check==2 || $month_check==3){
				//show画面に遷移する
				return Redirect::to('h/hl/'.$page_day);
			}
			//ほかの場合
		}else{
			return Redirect::to('h/hl/'.$page_day.'/edit');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
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

		//テーブル配列
		for ($i = 0; $i < count(Input::get('sinseidate')); $i++) {
			//画面から情報を取得
			$sinseidate = Input::get('sinseidate.' . $i);
			$utiwake = Input::get('utiwake.' . $i);
			$ikisaki = Input::get('ikisaki.' . $i);
			$sonotahi = Input::get('sonotahi.' . $i);
			$koutuuhi = Input::get('koutuuhi.' . $i);
			$shukuhakuhi = Input::get('shukuhakuhi.' . $i);
			$SINSEICD = 3;

			//画面から備考情報を取得
			$bikou = Input::get('bikou.' . $i);

			//テーブルのidを取得
			$hllist_id = Input::get('id.'.$i);

			//日付はない場合は保存しない
			if(null != $sinseidate){
				//return Redirect::back()->withInput()->withErrors('保存失败！');
				if(null != $hllist_id ) {
					//一行のデータを取り出す
					$wlist=hiyou::find($hllist_id);
					//項目設定
					//$wlist=new hiyou();
					$wlist->SINSEIDATE = $sinseidate;
					$wlist->UTIWAKE = $utiwake;
					$wlist->IKISAKI = $ikisaki;
					$wlist->KOUTUUHI = $koutuuhi;
					$wlist->SHUKUHAKUHI = $shukuhakuhi;
					$wlist->SONOTAHI = $sonotahi;
					$wlist->SINSEICD = $SINSEICD;
					$wlist->SHAINCD = $usercd;
					$wlist->BIKOU = $bikou;
					$wlist->updated_at = date('Y-m-d');
					//データをデータベースに更新
					$wlist->save();
				}elseif(null == $hllist_id){
					//新規登録
					$wlist=new hiyou();
					$wlist->SINSEIDATE = $sinseidate;
					$wlist->UTIWAKE = $utiwake;
					$wlist->IKISAKI = $ikisaki;
					$wlist->KOUTUUHI = $koutuuhi;
					$wlist->SHUKUHAKUHI = $shukuhakuhi;
					$wlist->SONOTAHI = $sonotahi;
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

		//edit関数に遷移
		return Redirect::to('h/hl/'.$page_day.'/edit');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($time)
	{
		session_start();
		$niki=$time;
		//年度取得
		$nengetu=date('Ym',strtotime($niki));
		//ユーザーコード取得
		//$username=$_SESSION["username"];
		//
		//$usercd = "1";
		$usercd=$_SESSION["username"];

		$Ymd = date('Ymd',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_m = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_sinseibi = "申請日：".$Ymd_Y.""-".$Ymd_m."-".$Ymd_d.";
		$Ymd_niki = $Ymd_Y."-".$Ymd_m."-".$Ymd_d."";
		$Ymd_sinseigetsu = "申請：".$Ymd_Y."-".$Ymd_m;

		//計算用
		$sum_sonotahi = 0;//申請金額
		$sum_koutuuhi = 0;
		$sum_shukuhakuhi = 0;
		//日別表からデータを取り出す
		$day_list = Func::getHlbyMonth($Ymd, $usercd);
		//データなし場合
		if (count($day_list) == 0) {
			//登録後のデータを取得
			$day_list = Func::getHlbyMonth($Ymd, $usercd);
			//edit画面に遷移する
			return view('h.hl.edit', compact('day_list','Ymd_sinseibi','sum_sonotahi','sum_koutuuhi','sum_shukuhakuhi','Ymd_niki'));
		} else {

			//合計
			foreach($day_list as $key => $value){
				$sum_sonotahi += $value->SONOTAHI;
				$sum_koutuuhi += $value->KOUTUUHI;
				$sum_shukuhakuhi +=$value->SHUKUHAKUHI;
			}
		}
		//費用類別=3
		$SINSEICD = 3;
		$sum_totalhiyous = $sum_sonotahi + $sum_koutuuhi + $sum_shukuhakuhi;
		//dbから月別表の当年月のデータを取り出す
		$hiyoustlist= DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('shinseim','=',$nengetu)->Where('SINSEICD','=',3)->get();

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
		$wlist->HIYOUST = $sum_totalhiyous;//有給回数
		$wlist->SHINSEIDATE=date('Ymd'); //申請日
		$wlist->SINSEICD = $SINSEICD;//費用類別
		$wlist->SHOUNINSTATUS='2';//承認状態 2:申請　3:承認　4:却下
		$wlist->SHOUNINSHACD="";//承認者コード
		$wlist->SHOUNINSHANM="";//承認者名
		$wlist->SHOUNINDATE="";//承認日
		$wlist->BKU="";//備考
		$wlist->save();


		$sum_sonotahi = "¥".number_format($sum_sonotahi, 0);
		$sum_koutuuhi = "¥".number_format($sum_koutuuhi, 0);
		$sum_shukuhakuhi = "¥".number_format($sum_shukuhakuhi, 0);


		//show画面に遷移する
		return view("h.hl.show", compact("day_list","Ymd_sinseigetsu","sum_sonotahi","sum_koutuuhi","sum_shukuhakuhi","Ymd_niki"));

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
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
		$sum_sonotahi = 0;//申請金額
		$sum_koutuuhi = 0;
		$sum_shukuhakuhi = 0;

		//日別表からデータを取り出す
		$day_list = Func::getHlbyMonth($Ymd, $usercd);
		//データなし場合
		if (count($day_list) == 0) {
			//登録後のデータを取得
			$day_list = Func::getHlbyMonth($Ymd, $usercd);
			//edit画面に遷移する
			return view("h.hl.edit", compact("day_list","Ymd_sinseibi","sum_sonotahi","sum_koutuuhi","sum_shukuhakuhi","Ymd_niki"));
		} else {

			//合計
			foreach($day_list as $key => $value){
				$sum_sonotahi += $value->SONOTAHI;
				$sum_koutuuhi += $value->KOUTUUHI;
				$sum_shukuhakuhi += $value->SHUKUHAKUHI;
			}
		}


		$sum_sonotahi = "¥".number_format($sum_sonotahi, 0);
		$sum_koutuuhi = "¥".number_format($sum_koutuuhi, 0);
		$sum_shukuhakuhi = "¥".number_format($sum_shukuhakuhi, 0);


		//月別表当月データあるチェック
		$wlist = DB::table("hiyoust")->where("shaincd", "=", $usercd)->Where("SHINSEIM", "=", $Ymd_Y . $Ymd_m)->Where("SINSEICD","=",3)->get();
		if (count($wlist) > 0) {
			$ar = get_object_vars($wlist["0"]);
			//今月分の申請フラグをチェック
			$month_check = $ar["SHOUNINSTATUS"];
			//申請と承認の場合
			if ($month_check == 2 || $month_check == 3) {
				return view("h.hl.show", compact("day_list","Ymd_sinseigetsu","sum_sonotahi","sum_koutuuhi","sum_shukuhakuhi","Ymd_niki"));
			} else {
				//ほかの場合
				return view("h.hl.edit", compact("day_list","Ymd_sinseibi","sum_sonotahi","sum_koutuuhi","sum_shukuhakuhi","Ymd_niki"));
			}
		}else{
			//月別表当月データなし場合
			return view("h.hl.edit", compact("day_list","Ymd_sinseibi","sum_sonotahi","sum_koutuuhi","sum_shukuhakuhi","Ymd_niki"));
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
