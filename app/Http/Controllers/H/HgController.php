<?php namespace App\Http\Controllers\H;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\hiyou;
use App\hiyout;
use DB;
use App\Func;

use Redirect, Input, Auth;

class HgController extends Controller {

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
        //勤務管理表月表当月データあるチェック
        //$wlist= DB::table('hiyous')->where('shaincd', '=', $usercd)->Where('SINSEIDATE','=',$nengetu)->get();
        $hiyoustlist= DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('SHINSEIM','=',$nengetu)->Where('SINSEICD','=',2)->get();


        //勤務管理表日表と月表両方ともデータある場合
        if(count($hiyoustlist)>0){

            $ar=get_object_vars($hiyoustlist["0"]);
            //今月分の申請フラグをチェック
            $month_check=$ar["SHOUNINSTATUS"];
            //却下の場合
            if($month_check==4){
                //エディタ画面に遷移する
                return Redirect::to('h/hg/'.$page_day.'/edit');
                //申請と承認の場合
            }elseif($month_check==2 || $month_check==3){
                //show画面に遷移する
                return Redirect::to('h/hg/'.$page_day);
            }
            //ほかの場合
        }else{
                return Redirect::to('h/hg/'.$page_day.'/edit');
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
        $page_day=date('Y-m-d');
        //$username = 1;//session から取得予定　修正予定
        //既存情報かとうかにチェック
        session_start();
        //$_SESSION["niki"]=$page_day;
        //$_SESSION["username"]=$username;
        $usercd=$_SESSION["username"];

        //テーブル配列
        for ($i = 0; $i < count(Input::get('sinseidate')); $i++) {
            //画面から情報を取得
            $sinseidate = Input::get('sinseidate.' . $i);
            $utiwake = Input::get('utiwake.' . $i);
            $sonotahi = Input::get('sonotahi.' . $i);
            $SINSEICD = 2;

            //画面から備考情報を取得
            $bikou = Input::get('bikou.' . $i);

            //テーブルのidを取得
            $hglist_id = Input::get('id.'.$i);

            //日付はない場合は保存しない
            if(null != $sinseidate){
                //return Redirect::back()->withInput()->withErrors('保存失败！');
                if(null != $hglist_id ) {
                    //一行のデータを取り出す
                    $wlist=hiyou::find($hglist_id);
                    //項目設定
                    //$wlist=new hiyou();
                    $wlist->SINSEIDATE = $sinseidate;
                    $wlist->UTIWAKE = $utiwake;
                    $wlist->SONOTAHI = $sonotahi;
                    $wlist->SINSEICD = $SINSEICD;
                    $wlist->SHAINCD = $usercd;
                    $wlist->BIKOU = $bikou;
                    $wlist->updated_at = date('Y-m-d');
                    //データをデータベースに更新
                    $wlist->save();
                }elseif(null == $hglist_id){
                    //新規登録
                    $wlist=new hiyou();
                    $wlist->SINSEIDATE = $sinseidate;
                    $wlist->UTIWAKE = $utiwake;
                    $wlist->SONOTAHI = $sonotahi;
                    $wlist->SINSEICD = $SINSEICD;
                    $wlist->SHAINCD = $usercd;
                    $wlist->BIKOU = $bikou;
                    $wlist->updated_at = date('Y-m-d');
                    //データをデータベースに登録
                    $wlist->save();
                }
            }else{
                return Redirect::back()->withInput()->withErrors('日付を選んでください！');
            }
        }

        //データ更新
        for ($j = 0; $j < count(Input::get('idD')); $j++) {
            //テーブルのidを取得
            $delList_id = Input::get('idD.'.$j);

            if(null != $delList_id ) {
                //一行のデータを取り出す
                $dlist = hiyou::find($delList_id);
                //データをデータベースに削除
                if(!is_null($dlist)) {
                    $dlist->delete();
                }
            }
        }

        //edit関数に遷移
        return Redirect::to('h/hg/'.$page_day.'/edit');
    }
    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show($time)
    {
        session_start();
        $niki=$time;
        //年度取得
        $nengetu=date('Ym',strtotime($niki));
        //ユーザーコード取得
        //$username=$_SESSION["username"];
        //
        //$usercd = "1";
        $usercd = $_SESSION["username"];

        $Ymd = date('Ymd',strtotime($time));
        $Ymd_Y = date('Y',strtotime($time));
        $Ymd_m = date('m',strtotime($time));
        $Ymd_d = date('d',strtotime($time));
        $Ymd_sinseibi = '申請日：'.$Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';
        $Ymd_niki = $Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';
        $Ymd_sinseigetsu = '申請：'.$Ymd_Y.'-'.$Ymd_m;

        //計算用
        $sum_sonotahi = 0;//申請金額
        //日別表からデータを取り出す
        $day_list = Func::getHgbyMonth($Ymd, $usercd);
        //データなし場合
        if (count($day_list) == 0) {
            //登録後のデータを取得
            $day_list = Func::getHgbyMonth($Ymd, $usercd);
            //edit画面に遷移する
            return view('h.hg.edit', compact('day_list','Ymd_sinseibi','sum_sonotahi','Ymd_niki'));
        } else {

            //合計
            foreach($day_list as $key => $value){
                $sum_sonotahi += $value->SONOTAHI;
            }
        }
        //費用類別=2
        $SINSEICD = 2;

        //dbから月別表の当年月のデータを取り出す
        $hiyoustlist= DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('shinseim','=',$nengetu)->Where('SINSEICD','=',2)->get();

        if(count($hiyoustlist)<=0) {
            //なし場合、新規クラス
            $wlist = new hiyout();
        }else{
            $hiyoustlist_0=$hiyoustlist[0];
            $shounincheck=$hiyoustlist_0->SHOUNINSTATUS;

            //ある場合、修正
            $arr_hiyoustlist=get_object_vars($hiyoustlist[0]);
            $wlist=hiyout::find($arr_hiyoustlist["id"]);
        }
        $wlist->SHAINCD = $usercd;
        $wlist->SHINSEIM=$nengetu;
        $wlist->SINSEIY = date('Y',strtotime($niki));
        $wlist->HIYOUST =$sum_sonotahi;//有給回数
        $wlist->SHINSEIDATE=date('Ymd'); //申請日
        $wlist->SINSEICD = $SINSEICD;//費用類別
        $wlist->SHOUNINSTATUS='2';//承認状態 2:申請　3:承認　4:却下
        $wlist->SHOUNINSHACD="";//承認者コード
        $wlist->SHOUNINSHANM="";//承認者名
        $wlist->SHOUNINDATE="";//承認日
        $wlist->BKU="";//備考
        $wlist->save();

        $sum_sonotahi = '¥'.number_format($sum_sonotahi, 0);

        //show画面に遷移する
        return view('h.hg.show', compact('day_list','Ymd_sinseigetsu','sum_sonotahi','Ymd_niki'));

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
        $Ymd_sinseibi = '申請日：'.$Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';
        $Ymd_niki = $Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';
        $Ymd_sinseigetsu = '申請：'.$Ymd_Y.'-'.$Ymd_m;

        //計算用
        $sum_sonotahi = 0;//申請金額
        //日別表からデータを取り出す
        $day_list = Func::getHgbyMonth($Ymd, $usercd);
        //データなし場合
        if (count($day_list) == 0) {
            //登録後のデータを取得
            $day_list = Func::getHgbyMonth($Ymd, $usercd);
            //edit画面に遷移する
            return view('h.hg.edit', compact('day_list','Ymd_sinseibi','sum_sonotahi','Ymd_niki'));
        } else {

            //合計
            foreach($day_list as $key => $value){
                $sum_sonotahi += $value->SONOTAHI;
                }
            }

        $sum_sonotahi = '¥'.number_format($sum_sonotahi, 0);

        //月別表当月データあるチェック
        $wlist = DB::table('hiyoust')->where('shaincd', '=', $usercd)->Where('SHINSEIM', '=', $Ymd_Y . $Ymd_m)->Where('SINSEICD','=',2)->get();
        if (count($wlist) > 0) {
            $ar = get_object_vars($wlist["0"]);
            //今月分の申請フラグをチェック
            $month_check = $ar["SHOUNINSTATUS"];
            //申請と承認の場合
            if ($month_check == 2 || $month_check == 3) {
                return view('h.hg.show', compact('day_list','Ymd_sinseigetsu','sum_sonotahi','Ymd_niki'));
            } else {
                //ほかの場合
                return view('h.hg.edit', compact('day_list','Ymd_sinseibi','sum_sonotahi','Ymd_niki'));
            }
        }else{
            //月別表当月データなし場合
            return view('h.hg.edit', compact('day_list','Ymd_sinseibi','sum_sonotahi','Ymd_niki'));
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


    public function getHgbyday($Ymd, $user_id){

        if($Ymd == null){
            $Ymd = date('Y-m-d');
        }

        //検索条件ユーザID
        //$user_id = '10049';//ユーザーIDを取得

        //検索条件:年月日
        $month_start = date("Ym01", strtotime($Ymd));//'2016-03-01'
        $month_end   = date("Ymt", strtotime($Ymd)); //月末日

        //条件日期の費用の精算申請情報のsql
        $month_sql = "	 SELECT "
            ." hiyous.id,"
            ." hiyous.SHAINCD,"
            ." hiyous.SINSEICD,"
            ." hiyous.SINSEIDATE,"
            ." hiyous.HASSEIDATE,"
            ." hiyous.SONOTAHI,"
            ." hiyous.IKISAKI,"
            ." hiyous.UTIWAKE,"
            ." hiyous.BIKOU"
            ." FROM hiyous "
            ." WHERE hiyous.SHAINCD = ".$user_id
            ."	AND "
            ." hiyous.SINSEICD = '2'"
            ."	AND "
            ." hiyous.HASSEIDATE "
            ."	BETWEEN "
            ."'".$month_start."'"
            ."	AND "
            ."'".$month_end."'";

        $month_list = DB::select($month_sql);

        return $month_list;
    }
}
