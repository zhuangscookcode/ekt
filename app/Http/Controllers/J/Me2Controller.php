<?php namespace App\Http\Controllers\J;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Userinfo;
use App\User;
use App\Roleuser;
use DB;
use App\Func;

use Redirect, Input, Auth;

class Me2Controller extends Controller {

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


        return Redirect::to('j/me2/'.$page_day.'/edit');


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


    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show()
    {


    }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $time
    * @return Response
    */
    public function edit($info)
    {
        //
        //
        //$usercd = "1";
        session_start();
        $usercd=$_SESSION["username"];

        $Ymd = date('Ymd');
        $Ymd_Y = date('Y');
        $Ymd_m = date('m');
        $Ymd_d = date('d');
        $Ymd_niki = $Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';

        $arr_info = explode("+", $info);

        $id1 = $arr_info[1];
        $name1 = $arr_info[2];
        $division1 = $arr_info[3];


        if($id1 == null && $name1 == null && $division1 == 01){
            //データを取り出す
            $arrmemberlist = Func::getSupervisor();
        }else{
            $arrmemberlist = Func::getSupervisorIndex($id1,$name1,$division1);
        }

        //部門テーブルを取り出す
        $division_list = Func::getDivisionName();


        $member_list=array();

        for ($i = 0; $i < count($arrmemberlist); $i++) {
            $member_list[$i] = get_object_vars($arrmemberlist[$i]);
            //$member_list[$i]["DIVISION"] = Func::division($member_list[$i]["DIVISION"]);
        }


        return view('j.me2.edit', compact('member_list','Ymd_niki','id1','division1','name1','division_list'));


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
