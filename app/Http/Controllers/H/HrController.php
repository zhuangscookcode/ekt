<?php namespace App\Http\Controllers\H;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Func;
use Redirect, Input, Auth;

class HrController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function show($para)
	{
		if(substr($para,0,4)=="show") {
			//引数から年度取得
			$nengetu = date('Ym');
		}
		else{
			$nengetu = $para;
		}
		$kotoshi=date('Y');
		//useridを取り出す
		//$usercd=1;
		$usercd = Auth::user()->email;
		session_start();
		$_SESSION["username"]=$usercd;
		$usercd=$_SESSION["username"];

		$nendo = substr($nengetu,0,4);
		$getudo = substr($nengetu,4,2);


		$h_list = DB::table('hiyoust')->join('userinfo', 'hiyoust.shaincd', '=', 'userinfo.id')->orderBy('SINSEICD')->where('shaincd', '=', $usercd)->Where('SHINSEIM','=',$nengetu)->get();

		$arrhlist = array();

		$user_list = DB::table('userinfo')->where('id', '=', $usercd)->get();

		$arruserlist[0] = get_object_vars($user_list[0]);

		if (count($h_list) > 0) {
			for ($i = 0; $i < count($h_list); $i++) {
				$arrhlist[$i] = get_object_vars($h_list[$i]);
				$arrhlist[$i]["SHOUNINSTATUS"] = Func::jyotaihenkan($arrhlist[$i]["SHOUNINSTATUS"]);
			}
			//勤務承認画面へ遷移
			return view('h.hr.list', compact('nendo', 'kotoshi','getudo', 'arrhlist','arruserlist'));
		} else {
			$arrhlist = null;
			return view('h.hr.list', compact('nendo', 'kotoshi','getudo', 'arrhlist','arruserlist'));
		}

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
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
