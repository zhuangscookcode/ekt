<?php namespace App\Http\Controllers\S;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Kmg;
use DB;
use App\Func;
use App\FuncMail;

use Redirect, Input, Auth;

class SkController extends Controller {

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
		$km_list=DB::table('kmgs')
			->join('userinfo', 'kmgs.shaincd', '=', 'userinfo.id')
			//->where('kmgs.shaincd', '=', $usercd)
			->Where('kmgs.nengetu','=',$nengetu)
			->get();

		$arrkmlist=array();

		if(count($km_list)>0){
			for($i=0;$i<count($km_list);$i++){
				$arrkmlist[$i]=get_object_vars($km_list[$i]);
				//承認ステータスを文字に変換
				$arrkmlist[$i]["SHOUNINSTATUS"]=Func::jyotaihenkan($arrkmlist[$i]["SHOUNINSTATUS"]);
			}
			//view画面に遷移
			return view('s.sk.show',compact('nendo','kotoshi','getudo','arrkmlist'));
		}else{
			//view画面に遷移
			return view('s.sk.show',compact('nendo','kotoshi','getudo','arrkmlist'));
		}
	}


    public function store(Request $request)
    {

		//指定した月を取得
		//$username = 1;//session から取得予定　修正予定
		//既存情報かとうかにチェック
		session_start();
		//$_SESSION["niki"]=$page_day;
		//$_SESSION["username"]=$username;
		$usercd=$_SESSION["username"];

		//$nengetu = date('Ym');

		$yesbutton = Input::get('yes-button');

		$usercd = Input::get('userid');

		//usermail取得
		$usermail = DB::table('userinfo')->where('id', $usercd)->pluck('ADDRESS');

		//承認者id
		//$userid = 2;
		$userid = $_SESSION["username"];
		//承認者名前取得
		$name = DB::table('userinfo')->where('id', $userid)->pluck('name');
		//承認者所属取得
		$division = DB::table('userinfo')->where('id', $userid)->pluck('DIVISION');

		if($yesbutton == ""){
			$jyotai = "kk";

			// send 却下 mail
			// mail 内容
			$view = 'emails.sk';
			// send to
			$user = $usermail;
			// title
			$subject = '勤務却下';
			// 却下理由
			$data = array('division'=>$division, 'name'=>$name);
			// mail functionを取り出す
			FuncMail::sendTo( $user, $subject, $view, $data);


		}else{
			$jyotai = "sn";
		}


		//$usercd = Input::get('userid');
		$nengetu = Input::get('niki');
		


		$kmgslist = DB::table('kmgs')->where('shaincd', '=', $usercd)->Where('nengetu', '=', $nengetu)->get();

		$arr_kmgslist = get_object_vars($kmgslist[0]);
		$wlist = Kmg::find($arr_kmgslist["id"]);
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

		return Redirect::to('s/sk');

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
			$nengetu = substr($id, 0, 6);
			$username = substr($id, 8);


		}else{

			$kotoshi=date('Y');
			//$usercd=1;
			session_start();
			$usercd=$_SESSION["username"];

			$nengetu = $id;
			$nendo = substr($nengetu,0,4);
			$getudo = substr($nengetu,4,2);

			$km_list=DB::table('kmgs')
				->join('userinfo', 'kmgs.shaincd', '=', 'userinfo.id')
				//->join('orders', 'users.id', '=', 'orders.user_id')
				//->where('kmgs.shaincd', '=', $usercd)
				->Where('kmgs.nengetu','=',$nengetu)
				->get();

			$arrkmlist=array();

			if(count($km_list)>0){
				for($i=0;$i<count($km_list);$i++){
					$arrkmlist[$i]=get_object_vars($km_list[$i]);
					$arrkmlist[$i]["SHOUNINSTATUS"]=Func::jyotaihenkan($arrkmlist[$i]["SHOUNINSTATUS"]);
				}
                //勤務承認画面へ遷移
                return view('s.sk.show',compact('nendo','kotoshi','getudo','arrkmlist'));
			}else{
                $arrkmlist = null;
                return view('s.sk.show',compact('nendo','kotoshi','getudo','arrkmlist'));
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
		$nengetu = date("Ym",strtotime($time));
		$Ymd_niki = $Ymd_Y.'-'.$Ymd_M.'';

		//計算用
		$sum_kinmutime = '';//勤務時間
		$sum_zangyoutimef = 0;//普通残業
		$sum_zangyoutimek = 0;//休日残業
		$sum_shortagetime = 0;//不足時間
		$sum_daikyuukanobi=0;//代休可日数

		$day_list = Func::getKmbyMonth($Ymd, $userid);

		foreach ($day_list as $key => $value) {
			//勤務時間の加算
			$sum_kinmutime += $value->KINMUTIME;
			//残業時間の加算
			$sum_zangyoutimef += $value->ZANGYOUTIMEF;
			//休日出勤時間加算
			$sum_zangyoutimek += $value->ZANGYOUTIMEK;
			//不足時間加算
			$sum_shortagetime += $value->SHORTAGETIME;
			//代休可日数計算
			if ($value->ZANGYOUTIMEK >= 6) {
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
		//edit画面へ遷移
		return view('s.sk.edit', compact('Ymd_Y', 'Ymd_M', 'Ymd','Ymd_niki', 'day_list', 'sum_kinmutime', 'sum_zangyoutimef', 'sum_zangyoutimes', 'sum_zangyoutimek', 'sum_shortagetime', 'sum_daikyuukanobi','userid','nengetu'));
	}


}
