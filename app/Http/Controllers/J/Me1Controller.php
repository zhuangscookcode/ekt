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

class Me1Controller extends Controller {

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


        return Redirect::to('j/me1/'.$page_day.'/edit');


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
        $page_day=date('Y-m-d');
        //$username = 1;//session から取得予定　修正予定
        //既存情報かとうかにチェック
        session_start();
        //$_SESSION["niki"]=$page_day;
        //$_SESSION["username"]=$username;
        $usercd=$_SESSION["username"];

        $id1 = Input::get('id1');
        $name1 = Input::get('name1');
        $division1 = Input::get('division1');


        //下の画面から情報を取得
        //テーブル配列
        for ($i = 0; $i < count(Input::get('id')); $i++) {
            //画面から情報を取得
            $id = Input::get('id.' . $i);
            $name = Input::get('name.' . $i);
            $furikana = Input::get('furikana.' . $i);
            $position = Input::get('position.' . $i);
            $division = Input::get('division.' . $i);
            $permission_id = Input::get('permission_id.' . $i);
            $password = Input::get('password.' . $i);
            $supervisorid = Input::get('supervisorid.' . $i);
            //get 承認者名前
            $supervisor = null;
            $supervisormail = null;
            if ($supervisorid != 0){
                $arrsupervisor = Func::getSupervisorName($supervisorid);
                if($arrsupervisor == null){
                    return Redirect::back()->withInput()->withErrors('承認者は見つかりません！');
                }
                $supervisor = $arrsupervisor[0]->NAME;
                //
                $supervisormail = $arrsupervisor[0]->ADDRESS;
            }
            //
            //get 代理承認者名前
            $assistsupervisor = null;
            $assistsupervisorid = Input::get('assistsupervisorid.' . $i);
            if ($assistsupervisorid != 0){
                $arrassistsupervisor = Func::getSupervisorName($assistsupervisorid);
                if($arrassistsupervisor == null){
                    return Redirect::back()->withInput()->withErrors('代理承認者は見つかりません！');
                }
                $assistsupervisor = $arrassistsupervisor[0]->NAME;
            }
            //
            $address = Input::get('address.' . $i);
            $firstday = Input::get('firstday.' . $i);
            $bikou = Input::get('bikou.' . $i);

            //idはない場合は保存しない
            if(null != $id ) {
                //一行のデータを取り出す
                $wlist = Userinfo::find($id);
                //項目設定
                //$wlist=new hiyou();
                $wlist->NAME = $name;
                $wlist->FURIKANA = $furikana;
                $wlist->POSITION = $position;
                $wlist->DIVISION = $division;
                $wlist->permission_id = $permission_id;
                $wlist->PASSWORD = $password;
                $wlist->SUPERVISORID = $supervisorid;
                $wlist->SUPERVISOR = $supervisor;
                $wlist->ASSISTSUPERVISORID = $assistsupervisorid;
                $wlist->ASSISTSUPERVISOR = $assistsupervisor;
                $wlist->ADDRESS = $address;
                $wlist->FIRSTDAY = $firstday;
                $wlist->BIKOU = $bikou;
                $wlist->SUPERVISORMAIL = $supervisormail;
                $wlist->updated_at = date('Y-m-d');
                //データをデータベースに更新
                $wlist->save();
            }

            /*
            if(null != $address) {
                //項目設定
                //$wlist=new hiyou();
                $wlist = User::find($address);
                //$wlist->id = $address;
                $wlist->name = $name ;
                $wlist->password = $password;
                $wlist->email = $id;
                $wlist->updated_at = date('Y-m-d');
                //データをデータベースに更新
                $wlist->save();
            }
            */

            //users table更新
            Func::usersUpdate($page_day, $id, $name, $password, $address);

            /*
            if(null != $id ) {
                //項目設定
                //$wlist=new hiyou();
                $wlist = Roleuser::find($id);
                $wlist->role_id = $permission_id;
                $wlist->updated_at = date('Y-m-d');
                //データをデータベースに更新
                $wlist->save();
            }
            */
        }

        //edit関数に遷移

       return Redirect::to('j/me1/'.$page_day.'+'.$id1.'+'.$name1.'+'.$division1.'/edit');
    }

    /**
     * Display the specified resource.
     * 检索
     * @return Response
     */
    public function show($info)
    {
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

        $id1 = null;
        $name1= null;
        $division1 = 01;

        //idを取り出す
        for ($i = 1; $i < count($arr_info); $i++)
        {
            $id = $arr_info[$i];

            //データ更新
            if(null != $id ) {
                //一行のデータを取り出す
                $dlist = Userinfo::find($id);
                //項目設定
                //$wlist=new hiyou();
                if(!is_null($dlist)) {
                    $dlist->delete();
                }
            }

            if(null != $id ) {
                //項目設定
                //$wlist=new hiyou();
                $dlist = User::find($id);
                if(!is_null($dlist)) {
                    $dlist->delete();
                }
            }

            /*
            if(null != $id ) {
                //項目設定
                //$wlist=new hiyou();
                $dlist = Roleuser::find($id);
                if(!is_null($dlist)) {
                    $dlist->delete();
                }
            }
            */
        }

        //更新後のデータを取り出す
        $member_list = Func::getMember();

        //部門テーブルを取り出す
        $division_list = Func::getDivisionName();

        return view('j.me1.edit', compact('member_list','Ymd_niki','id1','division1','name1','division_list'));

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
            $member_list = Func::getMember();
        }else{
           $member_list = Func::getMemberIndex($id1,$name1,$division1);
        }

        //部門テーブルを取り出す
        $division_list = Func::getDivisionName();

        return view('j.me1.edit', compact('member_list','Ymd_niki','id1','division1','name1','division_list'));
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
