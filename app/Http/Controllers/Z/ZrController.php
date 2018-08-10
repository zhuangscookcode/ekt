<?php namespace App\Http\Controllers\Z;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Func;
use Redirect, Input, Auth;

class ZrController extends Controller
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
		//$usercd=1;
		$usercd = Auth::user()->email;
		session_start();
		$_SESSION["username"]=$usercd;
		$usercd=$_SESSION["username"];

		$nendo = substr($nengetu,0,4);
		$getudo = substr($nengetu,4,2);


		$zb_list = DB::table('zbgs')->join('userinfo', 'zbgs.shaincd', '=', 'userinfo.id')->orderBy('SHINSEIDATE')->where('shaincd', '=', $usercd)->Where('SHINSEIM','=',$nengetu)->get();

		$arrzblist = array();

		$user_list = DB::table('userinfo')->where('id', '=', $usercd)->get();

		$arruserlist[0] = get_object_vars($user_list[0]);

		if (count($zb_list) > 0) {
			for ($i = 0; $i < count($zb_list); $i++) {
				$arrzblist[$i] = get_object_vars($zb_list[$i]);
				$arrzblist[$i]["SHOUNINSTATUS"] = Func::houkokuhenkan($arrzblist[$i]["SHOUNINSTATUS"]);
			}
			//勤務承認画面へ遷移
			return view('z.zr.list', compact('nendo', 'kotoshi','getudo', 'arrzblist','arruserlist'));
		} else {
			$arrzblist = null;
			return view('z.zr.list', compact('nendo', 'kotoshi','getudo', 'arrzblist','arruserlist'));
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
