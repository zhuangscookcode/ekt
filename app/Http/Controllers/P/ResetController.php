<?php namespace App\Http\Controllers\P;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Userinfo;
use App\User;
use DB;
use App\Func;

use Redirect, Input, Auth;

class ResetController extends Controller {

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


        return Redirect::to('p/reset/'.$page_day.'/edit');


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
        $password1 = Input::get('password1');
        $password2 = Input::get('password2');

        if($password1 == $password2) {
            //userinfo table更新
            $wlist = Userinfo::find($usercd);
            $wlist->PASSWORD = $password1;
            $wlist->updated_at = date('Y-m-d');
            //データをデータベースに更新
            $wlist->save();


            //users table更新
            Func::passwordReset($usercd, $password1);


        }else{
            return Redirect::back()->withInput()->withErrors('入力したパスワードは一致していません！');
        }


        //edit関数に遷移
        return Redirect::to('p/reset/'.$page_day.'/edit');
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


        return view('p.reset.reset', compact('Ymd_niki','usercd'));

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
