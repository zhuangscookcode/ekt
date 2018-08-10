<?php namespace App\Http\Controllers\K;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Km;
use App\Kmg;
use DB;
use App\Func;

use Redirect, Input, Auth;

class KmController extends Controller {
	/**
	 * Show the form for creating a new resource.
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
		$nengetu=date('Ym');
		//当日日付取得
		$page_day=date('Y-m-d');
		//勤務管理表月表当月データあるチェック
		$wlist= DB::table('kmgs')->where('shaincd', '=', $usercd)->Where('nengetu','=',$nengetu)->get();
		//勤務管理表日表当月データあるチェック
		$wgetsulist= DB::table('kms')->where('shaincd', '=', $usercd)->Where('nengetu','=',$nengetu)->get();

		//勤務管理表日表と月表両方ともデータある場合
		if(count($wlist)>0 && count($wgetsulist)>0)
		{

			$ar=get_object_vars($wlist["0"]);
			//今月分の申請フラグをチェック
			$month_check=$ar["SHOUNINSTATUS"];
			//却下の場合
			if($month_check==4)
			{
				//エディタ画面に遷移する
				return Redirect::to('k/km/'.$page_day.'/edit');
				//申請と承認の場合
			}
			elseif($month_check==2 || $month_check==3)
			{
				//show画面に遷移する
				return Redirect::to('k/km/'.$page_day);
			}
			//ほかの場合
		}else{
			return Redirect::to('k/km/'.$page_day.'/edit');
		}

	}



	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		//指定した月を取得
		$page_day =date('Y-m-d',strtotime(Input::get('nengapi.0')));
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		$usercd=$_SESSION["username"];

		//テーブル配列

		$daikyuhandan=0;
		$everyday=1;
		for ($i = 0; $i < count(Input::get('nengapi')); $i++) {

			$this->validate($request, [
				'stime.' . $i => 'sometimes|date_format:H:i',
				'etime.' . $i => 'sometimes|date_format:H:i',
			]);

			//画面から情報を取得
			$kinmukbn = Input::get('kinmukbn.' . $i);
			$stime = Input::get('stime.' . $i);
			$etime = Input::get('etime.' . $i);
			//出勤時間と退勤時間一つだけあるの場合
			if(($stime!="" && $etime=="") ||(($stime=="" && $etime!="") )){
				return Redirect::back()->withInput()->withErrors('開始時間か終了時間入力してください！');
			}
			//有休、代休、欠勤の場合も勤務時間を入力しました
			if(($kinmukbn!="" && $kinmukbn!="4") && $stime!=""){
				return Redirect::back()->withInput()->withErrors('有休、代休、欠勤の場合、勤務時間入力しないでください！');
			}

            //開始時間>終了時間のエラーメッセージ
            if(($stime <> 0 & $etime <>0) & $stime >= $etime){
                $nengapi_error = Input::get('nengapi.' . $i);
                $error_message = $nengapi_error.'の開始時間が終了時間より大きくなりました！';
                return Redirect::back()->withInput()->withErrors($error_message);
            }

			//画面から備考情報を取得
			$bku = Input::get('bku.' . $i);
			//画面から休日フラグ情報を取得
			$kyujitsuflg=Input::get('kyujitsu.'. $i);

			//共通関数を呼び出して、勤務時間、残業時間、不足時間を算出する
			$sumtime_array = Func::getSumTime($stime,$etime,$kyujitsuflg);
			$kinmutime = $sumtime_array[0];
			$zangyoutimef = $sumtime_array[1];
			$shortagetime = $sumtime_array[2];

			//画面から日付情報を取得
			$nengapi = Input::get('nengapi.' . $i);
			//テーブルのidを取得
			$kmlist_id = Input::get('id.'.$i);


			if(null != $kmlist_id) {
				//一行のデータを取り出す
				$wlist=Km::find($kmlist_id);
				//項目設定
				$wlist->KINMUKBN = $kinmukbn;
				$wlist->STIME = $stime;
				$wlist->ETIME = $etime;
				$wlist->KINMUTIME = $kinmutime;
				//平日の場合
				if($kyujitsuflg==0){
					$wlist->ZANGYOUTIMEF = $zangyoutimef;
					$wlist->ZANGYOUTIMEK=0;
				}else{
					//休日の場合
					$wlist->ZANGYOUTIMEF =0 ;
					$wlist->ZANGYOUTIMEK=$zangyoutimef;
					if($wlist->ZANGYOUTIMEK>=6){
						$daikyuhandan++;
					}
				}
				//qirui.sun 20160815
				//有休、代休、欠勤の場合
				//if($kinmukbn==1 || $kinmukbn==3) {
					//$wlist->SHORTAGETIME = 8;
				//}else if($kinmukbn==2){
					//$wlist->KINMUTIME = 8;
				if($kinmukbn==1 || $kinmukbn==2) {
					$wlist->SHORTAGETIME = 0;
					$wlist->KINMUTIME = 0;
					if($kinmukbn==2){
						$daikyuhandan--;
					}
				}else if($kinmukbn==3){
					$wlist->SHORTAGETIME = 8;
					$wlist->KINMUTIME = 0;
					//qirui.sun 20160815
				}else{
					//ほかの場合
					$wlist->SHORTAGETIME = $shortagetime;
				}
				$wlist->BKU = $bku;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				//$wlist->save();
				$mlist[$everyday]=$wlist;
				$everyday++;
			}else{
				if(null!=$nengapi){
					return Redirect::back()->withInput()->withErrors('保存失败！');
				}
			}
		}

		if($daikyuhandan<0){
			return Redirect::back()->withInput()->withErrors('今月の代休可の日が足りないです！');
		}

    for($i = 1; $i <= count(Input::get('nengapi')); $i++){
		$mlist[$i]->save();
	}
		//edit関数に遷移
		return Redirect::to('k/km/'.$page_day.'/edit');
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
		//$username=$_SESSION["username"];
		$usercd=$_SESSION["username"];

		//年を取得
		$Ymd_Y=date('Y',strtotime($niki));
		//月を取得
		$Ymd_M=date('m',strtotime($niki));
		//当年月のデータをdbから取り出す
		$day_list =Func::getKmbyMonth($niki,$usercd);


		//勤務時間
		$sum_kinmutime=0;
		//残業時間
		$sum_zangyoutimef=0;
		//$sum_zangyoutimes='';
		//休日残業時間
		$sum_zangyoutimek=0;
		//代休可日数
		$sum_daikyuukanobi=0;
		//欠勤時間
		$sum_shortagetime=0;
		//標準日数
		$sum_standaryday=0;
		//勤務日数
		$sum_kimuhikisuu=0;
		//有休日数
		$sum_yuukyuucnt=0;
		//代休日数
		$sum_daikyuucnt=0;
		//欠勤日数
		$sum_kekkincnt=0;
		//その他日数
		$sum_sonotacnt=0;


		//合計
		foreach($day_list as $key => $value){

			//チェック
			if($value->KYUUJITUFLG==0 && $value->KINMUTIME==0 && $value->KINMUKBN==0){
				return Redirect::back()->withInput()->withErrors('平日休みの場合、勤務区分を選んでください！');

			}
			//稼働時間の加算
			$sum_kinmutime += $value->KINMUTIME;
			//普通残業時間の加算
			$sum_zangyoutimef += $value->ZANGYOUTIMEF;
			//休日残業時間の加算
			if($value->KYUUJITUFLG!=0 and $value->KINMUTIME>0) {
				$sum_zangyoutimek += $value->ZANGYOUTIMEK;
			}
			//不足時間の加算
			$sum_shortagetime += $value->SHORTAGETIME;
			//標準時間の計算
			if($value->KYUUJITUFLG==0){
				$sum_standaryday++;
			}
			//勤務日数の計算
			if($value->KINMUTIME>0){
				$sum_kimuhikisuu++;
			}

			if($value->KINMUKBN==1){
				//有給日数加算
				$sum_yuukyuucnt++;
			}elseif($value->KINMUKBN==2){
				//代休日数加算
				$sum_daikyuucnt++;
			}elseif($value->KINMUKBN==3){
				//欠勤日数加算
				$sum_kekkincnt++;
			}elseif($value->KINMUKBN==4){
				//その他日数加算
				$sum_sonotacnt++;
			}
			if($value->ZANGYOUTIMEK>=6){
				//休日残業時間６時間以上の場合、代休可能
				$sum_daikyuukanobi++;
			}
			//勤務区分から漢字に変換する
			if($value->KINMUKBN==0){
				$value->KINMUKBN="";
			}elseif($value->KINMUKBN==1){
				$value->KINMUKBN="有休";
			}elseif($value->KINMUKBN==2){
				$value->KINMUKBN="代休";
			}elseif($value->KINMUKBN==3){
				$value->KINMUKBN="欠勤";
			}elseif($value->KINMUKBN==4){
				$value->KINMUKBN="その他";
			}
			//時間がゼロの場合、spaceに入れ替える
			if($value->KINMUTIME==0){
				$value->KINMUTIME="";
			}
			if($value->ZANGYOUTIMEF==0){
				$value->ZANGYOUTIMEF="";
			}
			if($value->ZANGYOUTIMEK==0){
				$value->ZANGYOUTIMEK="";
			}
			if($value->SHORTAGETIME==0){
				$value->SHORTAGETIME="";
			}
		}
		//dbから月別表の当年月のデータを取り出す
		$kmgslist= DB::table('kmgs')->where('shaincd', '=', $usercd)->Where('nengetu','=',$nengetu)->get();

		if(count($kmgslist)<=0) {
			//なし場合、新規クラス
			$wlist = new Kmg;
			$wlist->UPDATEDATE=date('Ymd');//更新日
		}else{
            $kmgslist_0=$kmgslist[0];
            $shounincheck=$kmgslist_0->SHOUNINSTATUS;

			//ある場合、修正
			$arr_kmgslist=get_object_vars($kmgslist[0]);
			$wlist=Kmg::find($arr_kmgslist["id"]);
			$wlist->UPDATEDATE=date('Ymd');//更新日
		}
			$wlist->SHAINCD = $usercd;
		   // $wlist->NENGAPI = date('Y/m/d',strtotime($value->NENGAPI));
			$wlist->NENGETU=$nengetu;
			$wlist->NENDO = date('Y',strtotime($niki));
			$wlist->STANDARDDAY=$sum_standaryday;
			$wlist->STANDARDTIME=$sum_standaryday*8;//月間標準勤務時間
			$wlist->KINMUDAY=$sum_kimuhikisuu;//勤務日数
			$wlist->KINMUTIME=$sum_kinmutime;//勤務時間
			$wlist->ZANGYOUTIMEF=$sum_zangyoutimef;//普通残業
			//$wlist->ZANGYOUTIMES="";//深夜残業
			$wlist->ZANGYOUTIMEK=$sum_zangyoutimek;//休日残業
			$wlist->SHORTAGETIME=$sum_shortagetime;//不足時間
			//$wlist->TIKOKUTIME="";//遅刻時間
			//$wlist->SOUTAITIME="";//早退時間
			$wlist->YUUKYUUCNT=$sum_yuukyuucnt;//有給回数

			//$wlist->HANKYUUCNT="";//半休回数
			$wlist->YUUKYUUZAN="";//有給残数  			保留
			$wlist->DAIKYUUCNT=$sum_daikyuucnt;//代休回数
			$wlist->KEKKINCNT=$sum_kekkincnt;//欠勤回数
			$wlist->SONOTACNT=$sum_sonotacnt;//その他回数
			$wlist->UPDATEDATE=date('Ymd');//更新日
			$wlist->SINSEIDATE=date('Ymd'); //申請日
			$wlist->SHOUNINSTATUS='2';//承認状態 2:申請　3:承認　4:却下
			$wlist->SHOUNINSHACD="";//承認者コード
			$wlist->SHOUNINSHANM="";//承認者名
			$wlist->SHOUNINDATE="";//承認日
			$wlist->BKU="";//備考
			$wlist->save();

		//show画面に遷移する
		return view('k.km.show', compact('Ymd_Y','Ymd_M','day_list', 'sum_kinmutime', 'sum_zangyoutimef', 'sum_zangyoutimek', 'sum_shortagetime','sum_daikyuukanobi'));

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $time
	 * @return Response
	 */
	public function edit($time)
	{

		//$usercd = "1";
		session_start();
		//ユーザーコード取得
		//$username=$_SESSION["username"];
		$usercd=$_SESSION["username"];

		//年月取得
		$Ymd = date('Y-m-d', strtotime($time));
		$Ymd_Y = date('Y', strtotime($time));
		$Ymd_M = date('m', strtotime($time));

		//計算用
		$sum_kinmutime = '';//勤務時間
		$sum_zangyoutimef = 0;//普通残業
		$sum_zangyoutimek = 0;//休日残業
		$sum_shortagetime = 0;//不足時間
		$sum_daikyuukanobi=0;//代休可日数
		//日別表からデータを取り出す
		$day_list = Func::getKmbyMonth($Ymd, $usercd);


		//データなし場合
		if (count($day_list) == 0)
		{
			//当月の曜日と休日フラグを取得
			$getarr=Func::getYobiToKyuujitsuflg($Ymd_Y.$Ymd_M);

			for( $i=0;$i<count($getarr[0]);$i++)
			{
				//日別表にデータ登録
				$wlist=new Km;
				$wlist->SHAINCD = $usercd;
				$wlist->NENGAPI =$getarr[0][$i];
				$wlist->NENGETU=$Ymd_Y.$Ymd_M;
				$wlist->NENDO =$Ymd_Y;
				$wlist->YOUBI =$getarr[1][$i];
				$wlist->KYUUJITUFLG =$getarr[2][$i];
				$wlist->KINMUKBN = "";
				$wlist->STIME = "";
				$wlist->ETIME = "";
				$wlist->KINMUTIME = "";
				$wlist->ZANGYOUTIMEF = "";
				$wlist->SHORTAGETIME = "";
				$wlist->BKU = "";
				$wlist->updated_at = "";
				$wlist->save();
			}
			//登録後のデータを取得
			$day_list = Func::getKmbyMonth($Ymd, $usercd);
			//edit画面に遷移する
			return view('k.km.edit', compact('Ymd_Y','Ymd_M','Ymd', 'day_list', 'sum_kinmutime', 'sum_zangyoutimef',  'sum_zangyoutimek', 'sum_shortagetime','sum_daikyuukanobi'));
		}
		else
		{
			//合計
			foreach ($day_list as $key => $value)
			{
				//勤務時間の加算
				$sum_kinmutime += $value->KINMUTIME;
				//残業時間の加算
				$sum_zangyoutimef += $value->ZANGYOUTIMEF;
				//休日出勤時間加算
				$sum_zangyoutimek += $value->ZANGYOUTIMEK;
				//不足時間加算
				$sum_shortagetime += $value->SHORTAGETIME;
				//代休可日数計算
				if($value->ZANGYOUTIMEK>=6)
				{
					$sum_daikyuukanobi++;
				}
				//bug No:1004
				if($value->KINMUKBN==2)
				{
					$sum_daikyuukanobi--;
				}
			    //時間がゼロの場合、spaceに入れ替える
				if($value->KINMUTIME==0){
					$value->KINMUTIME="";
				}
				if($value->ZANGYOUTIMEF==0){
					$value->ZANGYOUTIMEF="";
				}
				if($value->ZANGYOUTIMEK==0){
					$value->ZANGYOUTIMEK="";
				}
				if($value->SHORTAGETIME==0){
					$value->SHORTAGETIME="";
				}
			}
			//月別表当月データあるチェック
			$wlist = DB::table('kmgs')->where('shaincd', '=', $usercd)->Where('nengetu', '=', $Ymd_Y . $Ymd_M)->get();
			if (count($wlist) > 0)
			{
				$ar = get_object_vars($wlist["0"]);
				//今月分の申請フラグをチェック
				$month_check = $ar["SHOUNINSTATUS"];
				//申請と承認の場合
				if ($month_check == 2 || $month_check == 3) {
					return view('k.km.show', compact('Ymd_Y', 'Ymd_M', 'day_list', 'sum_kinmutime', 'sum_zangyoutimef', 'sum_zangyoutimes', 'sum_zangyoutimek', 'sum_shortagetime','sum_daikyuukanobi'));
				}
				else
				{
					//ほかの場合
					return view('k.km.edit', compact('Ymd_Y', 'Ymd_M', 'Ymd', 'day_list', 'sum_kinmutime', 'sum_zangyoutimef', 'sum_zangyoutimes', 'sum_zangyoutimek', 'sum_shortagetime', 'sum_daikyuukanobi'));
				}
			}
			else
			{
				//月別表当月データなし場合
				return view('k.km.edit', compact('Ymd_Y', 'Ymd_M', 'Ymd', 'day_list', 'sum_kinmutime', 'sum_zangyoutimef', 'sum_zangyoutimes', 'sum_zangyoutimek', 'sum_shortagetime', 'sum_daikyuukanobi'));
			}
		}
	}
}
