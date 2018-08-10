<?php namespace App\Http\Controllers\J;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\division;
use DB;
use App\Func;

use Redirect, Input, Auth;

class BuController extends Controller {

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


        return Redirect::to('j/bu/'.$page_day.'/edit');


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

        /*
        //テーブル配列
        for ($i = 0; $i < count(Input::get('division')); $i++) {
            //画面から情報を取得
            $code = Input::get('code.' . $i);
            $division = Input::get('division.' . $i);

            //テーブルのidを取得
            $bulist_id = Input::get('id.'.$i);

            //codeはない場合は保存しない
            if(null != $code){
                //return Redirect::back()->withInput()->withErrors('保存失败！');
                if(null != $bulist_id ) {
                    //一行のデータを取り出す
                    $wlist=division::find($bulist_id);
                    //項目設定
                    //$wlist=new hiyou();
                    $wlist->DIVISIONCD = $code;
                    $wlist->DIVISION = $division;
                    $wlist->SHAINCD = $usercd;
                    $wlist->updated_at = date('Y-m-d');
                    //データをデータベースに更新
                    $wlist->save();
                }elseif(null == $bulist_id){
                    //新規登録
                    $wlist=new division();
                    $wlist->DIVISIONCD = $code;
                    $wlist->DIVISION = $division;
                    $wlist->SHAINCD = $usercd;
                    $wlist->updated_at = date('Y-m-d');
                    //データをデータベースに登録
                    $wlist->save();
                }
            }else{
                return Redirect::back()->withInput()->withErrors('部門コードを入力してください！');
            }
        }
        */

        //画面から情報を取得
        $code = Input::get('code1');
        $division = Input::get('division1');

        // depulicate id判断
        if(null != $code ) {
            if (division::find($code)){
                return Redirect::back()->withInput()->withErrors('部門コードは重複しています！');
            }
        }


        if(null != $code) {
            //新規登録
            $wlist = new division();
            $wlist->DIVISIONCD = $code;
            $wlist->DIVISION = $division;
            $wlist->SHAINCD = $usercd;
            $wlist->updated_at = date('Y-m-d');
            //データをデータベースに登録
            $wlist->save();
        }



        //edit関数に遷移
        return Redirect::to('j/bu/'.$page_day.'/edit');
    }
    /**
     * Display the specified resource.
     *
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

        //idを取り出す
        for ($i = 1; $i < count($arr_info); $i++) {

            $id = $arr_info[$i];

            //データ更新
            if(null != $id ) {
                //一行のデータを取り出す
                $dlist = division::find($id);
                //項目設定
                //$wlist=new hiyou();
                if(!is_null($dlist)) {
                    $dlist->delete();
                }
            }

        }


        //更新後のデータを取り出す
        $bu_list  = Func::getBu();


        return view('j.bu.edit', compact('bu_list','Ymd_niki'));
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
        //$usercd = "1";
        session_start();
        $usercd=$_SESSION["username"];

        $Ymd = date('Ymd',strtotime($time));
        $Ymd_Y = date('Y',strtotime($time));
        $Ymd_m = date('m',strtotime($time));
        $Ymd_d = date('d',strtotime($time));
        $Ymd_niki = $Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';

        //データを取り出す
        $bu_list = Func::getBu();
        //データなし場合
        if (count($bu_list) == 0) {
            //登録後のデータを取得
            $bu_list  = Func::getBu();
            //edit画面に遷移する
            return view('j.bu.edit', compact('bu_list','Ymd_niki'));
        }

        return view('j.bu.edit', compact('bu_list','Ymd_niki'));

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
