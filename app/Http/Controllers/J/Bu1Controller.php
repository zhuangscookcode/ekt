<?php namespace App\Http\Controllers\J;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\division;
use DB;
use App\Func;

use Redirect, Input, Auth;

class Bu1Controller extends Controller {

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
    public function store(Request $request)
    {
       //
    }
    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show($info)
    {
        //
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
        $usercd = Auth::user()->email;
        session_start();
        $_SESSION["username"]=$usercd;
        $usercd=$_SESSION["username"];

        $Ymd = date('Ymd');
        $Ymd_Y = date('Y');
        $Ymd_m = date('m');
        $Ymd_d = date('d');
        $Ymd_niki = $Ymd_Y.'-'.$Ymd_m.'-'.$Ymd_d.'';



        $arr_info = explode("+", $info);

        //idを取り出す
        for ($i = 3; $i < count($arr_info); $i++) {

            $id = $arr_info[$i-1];

            $division = $arr_info[$i];

            //データ更新
            if(0 != $id ) {
                //一行のデータを取り出す
                $wlist = division::find($id);
                $wlist->DIVISION = $division;
                $wlist->SHAINCD = $usercd;
                $wlist->updated_at = date('Y-m-d');
                //データをデータベースに登録
                $wlist->save();

            }

        }


        //更新後のデータを取り出す
        $bu_list  = Func::getBu();


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
