<?php namespace App\Http\Controllers\S;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Zbg;
use App\Zb;
use DB;
use Illuminate\Support\Facades\Mail;
use Redirect, Input, Auth;
use App\Func;
use App\FuncMail;

class SzController extends Controller {

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
		$zb_list=DB::table('zbgs')
			->join('userinfo', 'zbgs.shaincd', '=', 'userinfo.id')
			->Orderby('zbgs.SHINSEIDATE')
			//->where('zbgs.shaincd', '=', $usercd)
			->Where('zbgs.SHINSEIM','=',$nengetu)
			->get();

		$arrzblist=array();

		if(count($zb_list)>0){
			for($i=0;$i<count($zb_list);$i++){
				$arrzblist[$i]=get_object_vars($zb_list[$i]);
				//承認ステータスを文字に変換
				$arrzblist[$i]["SHOUNINSTATUS"]=Func::houkokuhenkan($arrzblist[$i]["SHOUNINSTATUS"]);
			}
			//view画面に遷移
			return view('s.sz.show',compact('nendo','kotoshi','getudo','arrzblist'));
		}else{
			//view画面に遷移
			return view('s.sz.show',compact('nendo','kotoshi','getudo','arrzblist'));
		}
	}


    public function store(Request $request)
    {
		//指定した月を取得
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		//session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		//$usercd=$_SESSION["username"];

		$niki=Input::get('niki');

		$yesbutton = Input::get('yes-button');

		$confirm1 = Input::get('confirm1');
		$idC= Input::get('idC');

		$usercd = Input::get('userid');

		//usermail取得
		$usermail = DB::table('userinfo')->where('id', $usercd)->pluck('ADDRESS');

		//承認者id
		session_start();
		//$userid = 2;
		$userid=$_SESSION["username"];
		//承認者名前取得
		$name = DB::table('userinfo')->where('id', $userid)->pluck('name');
		//承認者所属取得
		$division = DB::table('userinfo')->where('id', $userid)->pluck('DIVISION');

		if($yesbutton == ""){
			$jyotai = "kk";

			// send 却下 mail
			// mail 内容
			$view = 'emails.sz';
			// send to
			$user = $usermail;
			// title
			$subject = '週報却下';
			// 却下理由
			$data = array('division'=>$division, 'name'=>$name, 'confirm1'=>$confirm1);
			// mail functionを取り出す
			FuncMail::sendTo( $user, $subject, $view, $data );

		}else{
			$jyotai = "sn";
		}


		$zbgslist = DB::table('zbgs')->Orderby('SHINSEIDATE')->where('shaincd', '=', $usercd)->Where('SHINSEIDATE', '=', $niki)->get();



		$arr_zbgslist = get_object_vars($zbgslist[0]);
		$wlist = Zbg::find($arr_zbgslist["id"]);
		//承認者コードを登録
		$wlist->SHOUNINSHACD = $userid;//承認者コード
		//承認者名を登録
		$wlist->SHOUNINSHANM = $name;//承認者名
		//確認コメットを登録
		$wlist->CONFIRM = $confirm1;//承認者名

		if ($jyotai == "sn") {
			$wlist->SHOUNINSTATUS = "3";//承認状態 3:承認
		} else {
			$wlist->SHOUNINSTATUS = "4";//承認状態 4:却下
		}
		$wlist->SHOUNINDATE = date('Ymd');//承認日
		$wlist->save();

		//確認コメットを申請者に登録

		//内容はない場合は保存しない
		if(null != $confirm1) {
			if (null != $idC) {
				//一行のデータを取り出す
				$clist = Zb::find($idC);
				//項目設定
				//$wlist=new hiyou();
				$clist->SINSEIDATE = $niki;
				$clist->CONFIRM = $confirm1;
				$clist->RPTFLG = 2;
				$clist->SHAINCD = $usercd;
				$clist->updated_at = date('Y-m-d');
				//データをデータベースに更新
				$clist->save();
			} elseif (null == $idC) {
				//新規登録
				$clist = new Zb();
				$clist->SINSEIDATE = $niki;
				$clist->CONFIRM = $confirm1;
				$clist->RPTFLG = 2;
				$clist->SHAINCD = $usercd;
				$clist->updated_at = date('Y-m-d');
				//データをデータベースに登録
				$clist->save();
			}
		}

		return Redirect::to('s/sz');
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (strlen($id) > 10) {

			//承認却下判断
			$jyotai = substr($id, 0, 2);
			$nengetu = substr($id, 0, 6);
			$username = substr($id, 7,1);


		}else{

			$kotoshi=date('Y');
			//$usercd=1;
			session_start();
			$usercd=$_SESSION["username"];


			$nengetu = $id;
			$nendo = substr($nengetu,0,4);
			$getudo = substr($nengetu,4,2);


			$zb_list=DB::table('zbgs')
				->join('userinfo', 'zbgs.shaincd', '=', 'userinfo.id')
				//->join('orders', 'users.id', '=', 'orders.user_id')
				->Orderby('zbgs.SHINSEIDATE')
				//->where('zbgs.shaincd', '=', $usercd)
				->Where('zbgs.SHINSEIM','=',$nengetu)
				->get();

			$arrzblist=array();

			if(count($zb_list)>0){
				for($i=0;$i<count($zb_list);$i++){
					$arrzblist[$i]=get_object_vars($zb_list[$i]);
					$arrzblist[$i]["SHOUNINSTATUS"]=Func::houkokuhenkan($arrzblist[$i]["SHOUNINSTATUS"]);
				}
                //勤務承認画面へ遷移
                return view('s.sz.show',compact('nendo','kotoshi','getudo','arrzblist'));
			}else{
                $arrzblist = null;
                return view('s.sz.show',compact('nendo','kotoshi','getudo','arrzblist'));
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


		$Ymd = date('Y-m-d', strtotime($time));
		$Ymd_Y = date('Y', strtotime($time));
		$Ymd_M = date('m', strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_niki = date('Ymd', strtotime($time));

		//計算当前周
		$first = 1;
		$w = date('w', strtotime($Ymd));
		//一周日期
		$date1 = date('Y-m-d', strtotime("$Ymd -" . ($w ? $w - $first : 6) . ' days'));//本周始まり日期
		$date2 = date('Y-m-d', strtotime("$date1 +1 days"));
		$date3 = date('Y-m-d', strtotime("$date1 +2 days"));
		$date4 = date('Y-m-d', strtotime("$date1 +3 days"));
		$date5 = date('Y-m-d', strtotime("$date1 +4 days"));
		$date6 = date('Y-m-d', strtotime("$date1 +5 days"));
		$date7 = date('Y-m-d', strtotime("$date1 +6 days"));  //本周終わり日期

		// for判断
		$date_start = date('Ymd', strtotime("$Ymd -" . ($w ? $w - $first : 6) . ' days'));
		$date_end = date('Ymd', strtotime("$date_start +6 days"));
		$date_M = date('Ym', strtotime("$date_start +6 days"));

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
		$confirm1  = null;
		$supervisor1 = null;
		$assistsupervisor1 = null;
		//$SHAINCD = null;

		//データベースからデータを取り出す
		$data1 = Func::getZbbyWeek1($Ymd, $userid, $date1);
		$data2 = Func::getZbbyWeek2($Ymd, $userid, $date2);
		$data3 = Func::getZbbyWeek3($Ymd, $userid, $date3);
		$data4 = Func::getZbbyWeek4($Ymd, $userid, $date4);
		$data5 = Func::getZbbyWeek5($Ymd, $userid, $date5);
		$data6 = Func::getZbbyWeek6($Ymd, $userid, $date6);
		$data7 = Func::getZbbyWeek7($Ymd, $userid, $date7);

		$dataR = Func::getZbreport($Ymd, $userid, $date_start, $date_end);
		$dataC = Func::getZbconfirm($Ymd, $userid, $date_start, $date_end);


		foreach ($data1 as $key => $value) {
			$naiyo1 = $value->NAIYO;
			$id1 = $value->id;
			$supervisor1 = $value->SUPERVISOR;
			$assistsupervisor1 = $value->ASSISTSUPERVISOR;
			//$SHAINCD = $value->SHAINCD;
		}

		foreach ($data2 as $key => $value) {
			$id2 = $value->id;
			$naiyo2 = $value->NAIYO;
		}

		foreach ($data3 as $key => $value) {
			$id3 = $value->id;
			$naiyo3 = $value->NAIYO;
		}

		foreach ($data4 as $key => $value) {
			$id4 = $value->id;
			$naiyo4 = $value->NAIYO;
		}

		foreach ($data5 as $key => $value) {
			$id5 = $value->id;
			$naiyo5 = $value->NAIYO;
		}

		foreach ($data6 as $key => $value) {
			$id6 = $value->id;
			$naiyo6 = $value->NAIYO;
		}

		foreach ($data7 as $key => $value) {
			$id7 = $value->id;
			$naiyo7 = $value->NAIYO;
		}

		foreach ($dataR as $key => $value) {
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
		$arrsupervisor = Func::getSupervisorZb($userid);
		if (count($arrsupervisor) > 0) {
			$supervisor = $arrsupervisor[0]->SUPERVISOR;
			$assistsupervisor = $arrsupervisor[0]->ASSISTSUPERVISOR;
		}

		$arr_supervisor = array($supervisor,$assistsupervisor);



		return view('s.sz.edit', compact('Ymd_Y', 'Ymd_M','Ymd_d', 'date1', 'date2', 'date3', 'date4', 'date5', 'date6', 'date7'
			, 'naiyo1', 'naiyo2', 'naiyo3', 'naiyo4', 'naiyo5', 'naiyo6', 'naiyo7', 'report1','confirm1', 'id1', 'id2', 'id3', 'id4'
			, 'id5', 'id6', 'id7', 'idR','idC', 'Ymd_niki','arr_supervisor','supervisor1','assistsupervisor1','userid'));
	}

}

