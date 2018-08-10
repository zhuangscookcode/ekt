<?php namespace App\Http\Controllers\Z;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Zb;
use App\Zbg;
use DB;
use App\Func;
use App\FuncMail;

use Redirect, Input, Auth;
class ZbController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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

		//計算当前周
		$first=1;
		$w=date('w',strtotime('Ymd'));

		// for判断
		$date_start =date('Ymd',strtotime("'Ymd' -".($w ? $w - $first : 6).' days'));
		$date_end = date('Ymd',strtotime("$date_start +6 days"));

		//勤務管理表月表当月データあるチェック
		//$wlist= DB::table('hiyous')->where('shaincd', '=', $usercd)->Where('SINSEIDATE','=',$nengetu)->get();
		$zbgslist= DB::table('zbgs')->where('shaincd', '=', $usercd)->whereBetween('SHINSEIDATE',array($date_start, $date_end))->get();


		//勤務管理表日表と月表両方ともデータある場合
		if(count($zbgslist)>0){

			$ar=get_object_vars($zbgslist["0"]);
			//今月分の申請フラグをチェック
			$month_check=$ar["SHOUNINSTATUS"];
			//却下の場合
			if($month_check==4){
				//エディタ画面に遷移する
				return Redirect::to('z/zb/'.$page_day.'/edit');
				//申請と承認の場合
			}elseif($month_check==2 || $month_check==3){
				//show画面に遷移する
				return Redirect::to('z/zb/'.$page_day.'+'.+0);
			}
			//ほかの場合
		}else{
			return Redirect::to('z/zb/'.$page_day.'/edit');
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
	public function store(Request $request)
	{
		//指定した月を取得
		$page_day =date('Y-m-d');
		$sinseidate = date('Ymd');
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		$usercd=$_SESSION["username"];

		//当前日付
		$niki = Input::get('niki');

		$Ymd = date('Ymd',strtotime($niki));

		//計算当前周
		$first=1;
		$w=date('w',strtotime($Ymd));

		// for判断
		$date_start =date('Ymd',strtotime("$Ymd -".($w ? $w - $first : 6).' days'));
		$date_end = date('Ymd',strtotime("$date_start +6 days"));

		//画面から月曜日情報を取得
		$date1 = Input::get('date1');
		$naiyo1 = Input::get('naiyo1');
		//テーブルのidを取得
		$id1 = Input::get('id1');
		//承認者を取得
		$supervisor = Input::get('supervisor');
		$assistsupervisor = Input::get('assistsupervisor');

		//内容はない場合は保存しない
		if(null != $naiyo1) {
			if (null != $id1) {
				//一行のデータを取り出す
				$wlist = Zb::find($id1);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date1;
				$wlist->NAIYO = $naiyo1;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id1) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date1;
				$wlist->NAIYO = $naiyo1;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から火曜日情報を取得
		$date2 = Input::get('date2');
		$naiyo2 = Input::get('naiyo2');
		//テーブルのidを取得
		$id2 = Input::get('id2');

		//内容はない場合は保存しない
		if(null != $naiyo2) {
			if (null != $id2) {
				//一行のデータを取り出す
				$wlist = Zb::find($id2);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date2;
				$wlist->NAIYO = $naiyo2;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id2) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date2;
				$wlist->NAIYO = $naiyo2;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から水曜日情報を取得
		$date3 = Input::get('date3');
		$naiyo3 = Input::get('naiyo3');
		//テーブルのidを取得
		$id3 = Input::get('id3');

		//内容はない場合は保存しない
		if(null != $naiyo3) {
			if (null != $id3) {
				//一行のデータを取り出す
				$wlist = Zb::find($id3);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date3;
				$wlist->NAIYO = $naiyo3;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id3) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date3;
				$wlist->NAIYO = $naiyo3;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から木曜日情報を取得
		$date4 = Input::get('date4');
		$naiyo4 = Input::get('naiyo4');
		//テーブルのidを取得
		$id4 = Input::get('id4');

		//内容はない場合は保存しない
		if(null != $naiyo4) {
			if (null != $id4) {
				//一行のデータを取り出す
				$wlist = Zb::find($id4);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date4;
				$wlist->NAIYO = $naiyo4;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id4) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date4;
				$wlist->NAIYO = $naiyo4;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から金曜日情報を取得
		$date5 = Input::get('date5');
		$naiyo5 = Input::get('naiyo5');
		//テーブルのidを取得
		$id5 = Input::get('id5');

		//内容はない場合は保存しない
		if(null != $naiyo5) {
			if (null != $id5) {
				//一行のデータを取り出す
				$wlist = Zb::find($id5);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date5;
				$wlist->NAIYO = $naiyo5;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id5) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date5;
				$wlist->NAIYO = $naiyo5;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から土曜日情報を取得
		$date6 = Input::get('date6');
		$naiyo6 = Input::get('naiyo6');
		//テーブルのidを取得
		$id6 = Input::get('id6');

		//内容はない場合は保存しない
		if(null != $naiyo6) {
			if (null != $id6) {
				//一行のデータを取り出す
				$wlist = Zb::find($id6);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date6;
				$wlist->NAIYO = $naiyo6;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id6) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date6;
				$wlist->NAIYO = $naiyo6;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から日曜日情報を取得
		$date7 = Input::get('date7');
		$naiyo7 = Input::get('naiyo7');
		//テーブルのidを取得
		$id7 = Input::get('id7');

		//内容はない場合は保存しない
		if(null != $naiyo7) {
			if (null != $id7) {
				//一行のデータを取り出す
				$wlist = Zb::find($id7);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date7;
				$wlist->NAIYO = $naiyo7;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $id7) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->NENGAPI = $date7;
				$wlist->NAIYO = $naiyo7;
				$wlist->SHAINCD = $usercd;
				$wlist->SUPERVISOR = $supervisor;
				$wlist->ASSISTSUPERVISOR = $assistsupervisor;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から週間総括情報を取得
		$report = Input::get('report');
		//テーブルのidを取得
		$idR= Input::get('idR');

		//内容はない場合は保存しない
		if(null != $report) {
			if (null != $idR) {
				//一行のデータを取り出す
				$wlist = Zb::find($idR);
				//項目設定
				//$wlist=new hiyou();
				$wlist->SINSEIDATE = $date_end;
				$wlist->REPORT = $report;
				$wlist->RPTFLG = 1;
				$wlist->SHAINCD = $usercd;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$wlist->save();
			} elseif (null == $idR) {
				//新規登録
				$wlist = new Zb();
				$wlist->SINSEIDATE = $date_end;
				$wlist->REPORT = $report;
				$wlist->RPTFLG = 1;
				$wlist->SHAINCD = $usercd;
				$wlist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$wlist->save();
			}
		}

		//画面から週間総括情報を取得
		$confirm1 = Input::get('confirm1');
		//テーブルのidを取得
		$idC = Input::get('idC');

		//内容はない場合は保存しない
		if(null != $confirm1) {
			if (null != $idC) {
				//一行のデータを取り出す
				$clist = Zb::find($idC);
				//項目設定
				//$wlist=new hiyou();
				$clist->SINSEIDATE = $date_end;
				$clist->CONFIRM = $confirm1;
				$clist->RPTFLG = 2;
				$clist->SHAINCD = $usercd;
				$clist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$clist->save();
			} elseif (null == $idC) {
				//新規登録
				$clist = new Zb();
				$clist->SINSEIDATE = $date_end;
				$clist->CONFIRM = $confirm1;
				$clist->RPTFLG = 2;
				$clist->SHAINCD = $usercd;
				$clist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$clist->save();
			}
		}


		//edit関数に遷移
		return Redirect::to('z/zb/'.$page_day.'/edit');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function show($info)
	{
		$arr_info = explode("+", $info);

		$time = $arr_info[0];
		$report_flag = $arr_info[1];

		session_start();
		$niki=$time;
		//年度取得
		$nengetu=date('Ym',strtotime($niki));
		//ユーザーコード取得
		//$username=$_SESSION["username"];
		$usercd=$_SESSION["username"];
		//
		//$usercd = "1";
		$Ymd = date('Ymd',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_M = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_niki = $Ymd_Y.'-'.$Ymd_M.'-'.$Ymd_d.'';

		//計算当前周
		$first=1;
		$w=date('w',strtotime($Ymd));
		//一周日期
		$date1=date('Y-m-d',strtotime("$Ymd -".($w ? $w - $first : 6).' days'));//本周始まり日期
		$date2=date('Y-m-d',strtotime("$date1 +1 days"));
		$date3=date('Y-m-d',strtotime("$date1 +2 days"));
		$date4=date('Y-m-d',strtotime("$date1 +3 days"));
		$date5=date('Y-m-d',strtotime("$date1 +4 days"));
		$date6=date('Y-m-d',strtotime("$date1 +5 days"));
		$date7=date('Y-m-d',strtotime("$date1 +6 days"));  //本周終わり日期

		// for判断
		$date_start =date('Ymd',strtotime("$Ymd -".($w ? $w - $first : 6).' days'));
		$date_end = date('Ymd',strtotime("$date_start +6 days"));
		$date_M = date('Ym',strtotime("$date_start +6 days"));

		// 関数を用意
		$id1 = null;
		$id2 = null;
		$id3 = null;
		$id4 = null;
		$id5 = null;
		$id6 = null;
		$id7 = null;
		$idR = null;
		$idC = null;
		$naiyo1 = null;
		$naiyo2 = null;
		$naiyo3 = null;
		$naiyo4 = null;
		$naiyo5 = null;
		$naiyo6 = null;
		$naiyo7 = null;
		$report1 = null;
		$confirm1 = null;
		$supervisor1 = null;
		$assistsupervisor1 = null;

		//データベースからデータを取り出す
		$data1 = Func::getZbbyWeek1($Ymd, $usercd, $date1);
		$data2 = Func::getZbbyWeek2($Ymd, $usercd, $date2);
		$data3 = Func::getZbbyWeek3($Ymd, $usercd, $date3);
		$data4 = Func::getZbbyWeek4($Ymd, $usercd, $date4);
		$data5 = Func::getZbbyWeek5($Ymd, $usercd, $date5);
		$data6 = Func::getZbbyWeek6($Ymd, $usercd, $date6);
		$data7 = Func::getZbbyWeek7($Ymd, $usercd, $date7);

		$dataR = Func::getZbreport($Ymd, $usercd,$date_start,$date_end);
		$dataC = Func::getZbconfirm($Ymd, $usercd, $date_start, $date_end);


		foreach($data1 as $key => $value){
			$naiyo1 = $value->NAIYO;
			$id1 = $value->id;
			$supervisor1 = $value->SUPERVISOR;
			$assistsupervisor1 = $value->ASSISTSUPERVISOR;
		}

		foreach($data2 as $key => $value){
			$id2 = $value->id;
			$naiyo2 = $value->NAIYO;
		}

		foreach($data3 as $key => $value){
			$id3 = $value->id;
			$naiyo3 = $value->NAIYO;
		}

		foreach($data4 as $key => $value){
			$id4 = $value->id;
			$naiyo4 = $value->NAIYO;
		}

		foreach($data5 as $key => $value){
			$id5 = $value->id;
			$naiyo5 = $value->NAIYO;
		}

		foreach($data6 as $key => $value){
			$id6 = $value->id;
			$naiyo6 = $value->NAIYO;
		}

		foreach($data7 as $key => $value){
			$id7 = $value->id;
			$naiyo7 = $value->NAIYO;
		}

		foreach($dataR as $key => $value){
			$idR = $value->id;
			$report1 = $value->REPORT;
		}

		foreach ($dataC as $key => $value) {
			$idC = $value->id;
			$confirm1 = $value->CONFIRM;
		}

		//get 承認者と代理承認者
		$supervisor = null;
		$assistsupervisor = null;
		$arrsupervisor = Func::getSupervisorZb($usercd);
		if (count($arrsupervisor) > 0) {
			$supervisor = $arrsupervisor[0]->SUPERVISOR;
			$assistsupervisor = $arrsupervisor[0]->ASSISTSUPERVISOR;
		}

		$arr_supervisor = array($supervisor,$assistsupervisor);

		//dbからmailaddressゲート
		//$userlist=DB::table('users')->where('id', '=', 2)->get();
		//$address=get_object_vars($userlist[2]);

		//dbから月別表の当年月のデータを取り出す
		$zbgslist= DB::table('zbgs')->where('shaincd', '=', $usercd)->whereBetween('SHINSEIDATE',array($date_start, $date_end))->get();

		if(count($zbgslist)<=0) {
			//なし場合、新規クラス
			$wlist = new Zbg();
			$wlist->SHAINCD = $usercd;
			//$wlist->ADDRESS = $address;
			$wlist->SHINSEIM=$date_M;
			$wlist->ZBDATE =$date1."～".$date7;//一周
			$wlist->ADDRESS ="";
			$wlist->SHINSEIDATE=$date_end; //申請日
			$wlist->SHOUNINSTATUS='2';//承認状態 2:申請　3:承認　4:却下
			$wlist->SHOUNINSHACD="";//承認者コード
			$wlist->SHOUNINSHANM="";//承認者名
			$wlist->SHOUNINDATE="";//承認日
			$wlist->save();
		}else{
			$zbgslist_0=$zbgslist[0];
			$shounincheck=$zbgslist_0->SHOUNINSTATUS;

			//ある場合、修正
			$arr_zbgslist=get_object_vars($zbgslist[0]);
			$wlist=Zbg::find($arr_zbgslist["id"]);

			if($shounincheck != 3 && $shounincheck != 4){
				$wlist->SHAINCD = $usercd;
				//$wlist->ADDRESS = $address;
				$wlist->SHINSEIM=$date_M;
				$wlist->ZBDATE =$date1."～".$date7;//一周
				$wlist->ADDRESS ="";
				$wlist->SHINSEIDATE=$date_end; //申請日
				$wlist->save();
			}else{
				$wlist->SHAINCD = $usercd;
				//$wlist->ADDRESS = $address;
				$wlist->SHINSEIM=$date_M;
				$wlist->ZBDATE =$date1."～".$date7;//一周
				$wlist->ADDRESS ="";
				$wlist->SHINSEIDATE=$date_end; //申請日
				$wlist->SHOUNINSTATUS='2';//承認状態 2:申請　3:承認　4:却下
				$wlist->SHOUNINSHACD="";//承認者コード
				$wlist->SHOUNINSHANM="";//承認者名
				$wlist->SHOUNINDATE="";//承認日
				$wlist->save();
			}
		}

		//for mail send
		if ($report_flag == 1){

			$name = DB::table('userinfo')->where('id', $usercd)->pluck('name');
			//get 承認者id
			$supervisorid = DB::table('userinfo')->where('id', $usercd)->pluck('SUPERVISORID');
			$assistsupervisorid = DB::table('userinfo')->where('id', $usercd)->pluck('ASSISTSUPERVISORID');
			//get 承認者mail address
			$supervisormail = DB::table('userinfo')->where('id', $supervisorid)->pluck('ADDRESS');
			$assistsupervisormail = DB::table('userinfo')->where('id', $assistsupervisorid)->pluck('ADDRESS');
			// mail 内容
			$view = 'emails.zb';
			// 送信先を決まる
			if ($supervisor1 == 01 && $assistsupervisor1 == 01){
				$user = $supervisormail;
				$CCuser = $supervisormail;
			}else if ($supervisor1 == 01 && $assistsupervisor1 == 02){
				$user = $supervisormail;
				$CCuser = $assistsupervisormail;
			}else if ($supervisor1 == 02 && $assistsupervisor1 == 01){
				$user = $assistsupervisormail;
				$CCuser = $supervisormail;
			}else{
				$user = $assistsupervisormail;
				$CCuser = $assistsupervisormail;
			}
			// title
			$subject = '週報申請テストメール';
			// 却下理由
			$data = array('name'=>$name);
			// mail functionを取り出す
			FuncMail::sendToWithCC( $user, $CCuser, $subject, $view, $data );
		}

		return view('z.zb.show',compact('Ymd_Y','Ymd_M','date1','date2','date3','date4','date5','date6','date7'
			,'naiyo1','naiyo2','naiyo3','naiyo4','naiyo5','naiyo6','naiyo7','report1','confirm1','id1','id2','id3','id4'
			,'id5','id6','id7','idR','idC','Ymd_niki','arr_supervisor','supervisor1','assistsupervisor1'));

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
		//ユーザーコード取得
		//$username=$_SESSION["username"];
		$usercd=$_SESSION["username"];
		//
		//$username= "1";
		$Ymd = date('Y-m-d',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_M = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_niki = $Ymd_Y.'-'.$Ymd_M.'-'.$Ymd_d.'';

		//計算当前周
		$first=1;
		$w=date('w',strtotime($Ymd));
		//一周日期
		$date1=date('Y-m-d',strtotime("$Ymd -".($w ? $w - $first : 6).' days'));//本周始まり日期
		$date2=date('Y-m-d',strtotime("$date1 +1 days"));
		$date3=date('Y-m-d',strtotime("$date1 +2 days"));
		$date4=date('Y-m-d',strtotime("$date1 +3 days"));
		$date5=date('Y-m-d',strtotime("$date1 +4 days"));
		$date6=date('Y-m-d',strtotime("$date1 +5 days"));
		$date7=date('Y-m-d',strtotime("$date1 +6 days"));  //本周終わり日期

		// for判断
		$date_start =date('Ymd',strtotime("$Ymd -".($w ? $w - $first : 6).' days'));
		$date_end = date('Ymd',strtotime("$date_start +6 days"));


		// 関数を用意
		$id1 = null;
		$id2 = null;
		$id3 = null;
		$id4 = null;
		$id5 = null;
		$id6 = null;
		$id7 = null;
		$idR = null;
		$idC = null;
		$naiyo1 = null;
		$naiyo2 = null;
		$naiyo3 = null;
		$naiyo4 = null;
		$naiyo5 = null;
		$naiyo6 = null;
		$naiyo7 = null;
		$report1 = null;
		$confirm1 = null;
		$supervisor1 = null;
		$assistsupervisor1 = null;

		//データベースからデータを取り出す
		$data1 = Func::getZbbyWeek1($Ymd, $usercd, $date1);
		$data2 = Func::getZbbyWeek2($Ymd, $usercd, $date2);
		$data3 = Func::getZbbyWeek3($Ymd, $usercd, $date3);
		$data4 = Func::getZbbyWeek4($Ymd, $usercd, $date4);
		$data5 = Func::getZbbyWeek5($Ymd, $usercd, $date5);
		$data6 = Func::getZbbyWeek6($Ymd, $usercd, $date6);
		$data7 = Func::getZbbyWeek7($Ymd, $usercd, $date7);

		$dataR = Func::getZbreport($Ymd, $usercd, $date_start, $date_end);
		$dataC = Func::getZbconfirm($Ymd, $usercd, $date_start, $date_end);


		foreach($data1 as $key => $value){
			$naiyo1 = $value->NAIYO;
			$id1 = $value->id;
			$supervisor1 = $value->SUPERVISOR;
			$assistsupervisor1 = $value->ASSISTSUPERVISOR;
		}

		foreach($data2 as $key => $value){
			$id2 = $value->id;
			$naiyo2 = $value->NAIYO;
		}

		foreach($data3 as $key => $value){
			$id3 = $value->id;
			$naiyo3 = $value->NAIYO;
		}

		foreach($data4 as $key => $value){
			$id4 = $value->id;
			$naiyo4 = $value->NAIYO;
		}

		foreach($data5 as $key => $value){
			$id5 = $value->id;
			$naiyo5 = $value->NAIYO;
		}

		foreach($data6 as $key => $value){
			$id6 = $value->id;
			$naiyo6 = $value->NAIYO;
		}

		foreach($data7 as $key => $value){
			$id7 = $value->id;
			$naiyo7 = $value->NAIYO;
		}

		foreach($dataR as $key => $value){
			$idR = $value->id;
			$report1 = $value->REPORT;
		}

		foreach ($dataC as $key => $value) {
			$idC = $value->id;
			$confirm1 = $value->CONFIRM;
		}

		//get 承認者と代理承認者
		$supervisor = null;
		$assistsupervisor = null;
		$arrsupervisor = Func::getSupervisorZb($usercd);
		if (count($arrsupervisor) > 0) {
			$supervisor = $arrsupervisor[0]->SUPERVISOR;
			$assistsupervisor = $arrsupervisor[0]->ASSISTSUPERVISOR;
		}

		$arr_supervisor = array($supervisor,$assistsupervisor);


		//月別表当月データあるチェック
		$wlist = DB::table('zbgs')->where('shaincd', '=', $usercd)->whereBetween('SHINSEIDATE',array($date_start, $date_end))->get();
		if (count($wlist) > 0) {
			$arr_zbgslist = get_object_vars($wlist["0"]);
			//今月分の申請フラグをチェック
			$month_check = $arr_zbgslist["SHOUNINSTATUS"];
			//申請と承認の場合
			if ($month_check == 2 || $month_check == 3) {
				return view('z.zb.show',compact('Ymd_Y','Ymd_M','date1','date2','date3','date4','date5','date6','date7'
					,'naiyo1','naiyo2','naiyo3','naiyo4','naiyo5','naiyo6','naiyo7','report1','confirm1','id1','id2','id3','id4'
					,'id5','id6','id7','idR','idC','Ymd_niki','arr_supervisor','supervisor1','assistsupervisor1'));
			} else {
				//ほかの場合
				return view('z.zb.edit',compact('Ymd_Y','Ymd_M','date1','date2','date3','date4','date5','date6','date7'
					,'naiyo1','naiyo2','naiyo3','naiyo4','naiyo5','naiyo6','naiyo7','report1','confirm1','id1','id2','id3','id4'
					,'id5','id6','id7','idR','idC','Ymd_niki','arr_supervisor','supervisor1','assistsupervisor1'));
			}
		}else{
			//月別表当月データなし場合
			return view('z.zb.edit',compact('Ymd_Y','Ymd_M','date1','date2','date3','date4','date5','date6','date7'
				,'naiyo1','naiyo2','naiyo3','naiyo4','naiyo5','naiyo6','naiyo7','report1','confirm1','id1','id2','id3','id4'
				,'id5','id6','id7','idR','idC','Ymd_niki','arr_supervisor','supervisor1','assistsupervisor1'));
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
