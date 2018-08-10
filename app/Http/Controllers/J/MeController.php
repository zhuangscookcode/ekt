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

class MeController extends Controller {

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


        return Redirect::to('j/me/'.$page_day.'/edit');


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
    public function store(Request $time)
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

        //画面から情報を取得
        $id = Input::get('id');
        $name = Input::get('name');
        $furikana = Input::get('furikana');
        $position = Input::get('position');
        $division = Input::get('division');
        $permission_id = Input::get('permission_id');
        $password = Input::get('password');
        $supervisorid = Input::get('supervisorid');
        $supervisor = 0;
        $assistsupervisor = 0;
        $supervisormail = 0;
        if ($supervisorid != null) {
            //get 承認者名前
            $arrsupervisor = Func::getSupervisorName($supervisorid);
            if($arrsupervisor == null){
                return Redirect::back()->withInput()->withErrors('承認者は見つかりません！');
            }
            $supervisor = $arrsupervisor[0]->NAME;
            //
            $supervisormail = $arrsupervisor[0]->ADDRESS;
        }
        $assistsupervisorid = Input::get('assistsupervisorid');
        if ($assistsupervisorid != null) {
            //get 代理承認者名前
            $arrassistsupervisor = Func::getSupervisorName($assistsupervisorid);
            if($arrassistsupervisor == null){
                return Redirect::back()->withInput()->withErrors('代理承認者は見つかりません！');
            }
            $assistsupervisor = $arrassistsupervisor[0]->NAME;
            //
        }
        $address = Input::get('address');
        $firstday = Input::get('firstday');
        $bikou = Input::get('bikou');


        // depulicate id判断
        if(null != $id ) {
            if (Userinfo::find($id)){
                return Redirect::back()->withInput()->withErrors('社員番号は重複しています！');
            }
        }

        //codeはない場合は保存しない
            if(null != $id ) {
                //項目設定
                //$wlist=new hiyou();
                $wlist=new Userinfo();
                $wlist->id = $id;
                $wlist->NAME = $name ;
                $wlist->FURIKANA = $furikana;
                $wlist->POSITION = $position ;
                $wlist->DIVISION = $division ;
                $wlist->permission_id = $permission_id;
                $wlist->PASSWORD = $password;
                $wlist->SUPERVISORID = $supervisorid;
                $wlist->SUPERVISOR = $supervisor;
                $wlist->ASSISTSUPERVISORID= $assistsupervisorid;
                $wlist->ASSISTSUPERVISOR= $assistsupervisor;
                $wlist->ADDRESS = $address;
                $wlist->FIRSTDAY = $firstday;
                $wlist->BIKOU = $bikou;
                $wlist->SUPERVISORMAIL = $supervisormail;
                $wlist->updated_at = date('Y-m-d');
                //データをデータベースに更新
                $wlist->save();
            }

        if(null != $id ) {
            //項目設定
            //$wlist=new hiyou();
            $wlist=new User();
            $wlist->id = $address;
            $wlist->name = $name ;
            $wlist->password = $password;
            $wlist->email = $id;
            $wlist->updated_at = date('Y-m-d');
            //データをデータベースに更新
            $wlist->save();
        }

        /*
        if(null != $id ) {
            //項目設定
            //$wlist=new hiyou();
            $wlist=new Roleuser();
            $wlist->id = $id;
            $wlist->role_id = $permission_id;
            $wlist->updated_at = date('Y-m-d');
            //データをデータベースに更新
            $wlist->save();
        }
        */


        //edit関数に遷移
        return Redirect::to('j/me/'.$page_day.'/edit');
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
    public function edit($time)
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

        //部門テーブルを取り出す
        $division_list = Func::getDivisionName();


        return view('j.me.edit', compact('Ymd_niki','division_list'));

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
