<?php
/**
 * Created by PhpStorm.
 * User: symm1
 * Date: 2016/04/26
 * Time: 15:53
 */

namespace App;

use DB;

class Func
{
    //曜日と休日フラグを取得関数
    public static function getYobiToKyuujitsuflg($time){

        $wlist= DB::table('karendas')->Where('nengetu','=',$time)->get();
        $nikiarr=array();
        $yobiarr=array();
        $kflgarr=array();

        if(count($wlist)>0) {

            for($i=0;$i<count($wlist);$i++){
                $ar = get_object_vars($wlist[$i]);
                $nikiarr[$i]=$ar["NENGAPI"];
                $yobiarr[$i]=$ar["YOUBI"];
                $kflgarr[$i]=$ar["KYUUJITUFLG"];
            }
        }
        $arrsogo=array($nikiarr,$yobiarr,$kflgarr);
        return $arrsogo;
    }

//勤務時間、不足時間、残業時間を算出する関数
    public static function getSumTime($stime,$etime,$kyujitsuflg){
        if($stime==null||$etime==null){
            $time_array =array (0,0,0);
            return $time_array;
        }

        $stime = strtotime($stime);
        $etime = strtotime($etime);

        $sum = $etime - $stime;

        $zangyoutimef = 0;
        $shortagetime = 0;

        $timef = 8;



        $sum = date('H',$sum)+date('i',$sum)/60;
        if($sum<=3){
            $sum=$sum;
        }elseif(3<$sum && $sum<=4){
            $sum=3;
        }elseif(4<$sum && $sum<=9){
            $sum=$sum-1;
        }elseif(9<$sum && $sum<=9.5){
            $sum=8;
        }else{
            $a=$sum-9.5;
            if($a<=3){
                $sum=8+$a;
            }elseif(3<$a && $a<=3.5){
                $sum=11;
            }else{
                $sum=8+$a-intval($a/3.5)*0.5;
            }
        }
        //平日の場合
        if($kyujitsuflg==0){
            if($sum>=$timef){
                $zangyoutimef = $sum - $timef;
            }else{
                $shortagetime = $timef-$sum;
            }
        }else{
            //休日の場合
            $zangyoutimef = $sum;
        }
        $time_array =array ($sum,$zangyoutimef,$shortagetime);

        return $time_array;

    }

//ユーザーの当月のデータを取り出す
    public static  function getKmbyMonth($Ymd,$user_id){

        if(null == $Ymd){
            $Ymd = date('Y-m-d');
        }

        //$month_start = date("Y-m-01", strtotime($Ymd));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
        $month_start = date("Y-m-01", strtotime($Ymd));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
       // $month_end = date("Y-m-t", strtotime($Ymd));//date('Y-m-d',strtotime('last day of this month','2015-12-10'));
        $month_end = date("Y-m-t", strtotime("$month_start +1 month -1 day"));




        //条件日期の勤務情報のsql
        $month_sql = "	SELECT "
            ." kms.id,"
            //." date_format(karendas.NENGAPI,'%Y/%c/%d') NENGAPI,"
            ." karendas.NENGAPI,"
            ." kms.SHAINCD,"
            ." kms.STIME,"
            ." kms.ETIME,"
            ." kms.KINMUTIME,"
            ." kms.YOUBI,"
            ." kms.KYUUJITUFLG,"
            ." kms.KINMUKBN,"
            ." kms.ZANGYOUTIMEF,"
            ." kms.ZANGYOUTIMES,"
            ." kms.ZANGYOUTIMEK,"
            ." kms.BKU,"
            ." kms.SHORTAGETIME "
            ."	FROM karendas "
            ."  JOIN kms ON karendas.NENGAPI = kms.NENGAPI " //kms
            ." AND kms.SHAINCD = ".$user_id
            ."	WHERE "
            ." karendas.NENGAPI "
            ."	BETWEEN "
            ."'".$month_start."'"
            ."	AND "
            ."'".$month_end."'";

        $month_list = DB::select($month_sql);

        foreach($month_list as $key =>$value){
            //曜日のコードから文字に変換する
            $month_list[$key]->YOUBI = Func::weekformart(date('w',strtotime($value->NENGAPI)));//$month_list[$key]->YOUBI);
            //年月日をYYYY-MM-DDの形式で表現する
            $month_list[$key]->NENGAPI = date('Y-m-d',strtotime($value->NENGAPI));
        }
        return $month_list;
    }

    //ユーザーの当月のデータを取り出すfor excel
    public static  function getKmforexcel($nengetu,$user_id){

        if(null == $nengetu){
            $nengetu= date('Y-m-d');
        }

        $month_start = date("Y-m-01", strtotime($nengetu));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
        $month_end = date("Y-m-t", strtotime($nengetu));//date('Y-m-d',strtotime('last day of this month','2015-12-10'));

        //条件日期の勤務情報のsql
        $month_sql = "	 SELECT "
            ." kms.NENGAPI AS 'Day',"
            ." kms. AS 'Day of the week',"
            ." kms.KIYOUBINMUKBN AS 'Working arrangement',"
            ." kms.STIME AS 'Clock-in time',"
            ." kms.ETIME AS 'Clock-out time',"
            ." kms.KINMUTIME AS 'Operating time',"
            ." kms.ZANGYOUTIMEF AS 'Normal overtime',"
            ." kms.ZANGYOUTIMEK AS 'work on holiday',"
            ." kms.SHORTAGETIME AS 'Lack of time',"
            ." kms.BKU AS 'Remarks'"
            ."	FROM kms "
            ."	WHERE kms.SHAINCD = ".$user_id
            ."	AND "
            ." kms.NENGETU =".$nengetu;

        $month_list = DB::select($month_sql);

        foreach($month_list as $key =>$value){
            //年月日をYYYY-MM-DDの形式で表現する
            $month_list[$key]->Day = date('m/d',strtotime($value->Day));
        }
        return $month_list;
    }

    //曜日のコードから文字に変換する関数
    public static function weekformart($w){
        $week = array("日", "月", "火", "水", "木", "金", "土","-");
        return $week[$w];
    }

    //曜日の文字からコードに変換する
    public static function weekformartgyaku($w){
        $week = array("日"=>0, "月"=>1, "火"=>2, "水"=>3, "木"=>4, "金"=>5, "土"=>6);
        return $week[$w];
    }

    //状態変換、数字から文字列へ
    public static function jyotaihenkan($jyoutai){
        $arr=array("2"=>"申請","3"=>"承認","4"=>"却下");
        return $arr[$jyoutai];
    }

    //状態変換、数字から文字列へ
    public static function houkokuhenkan($houkoku){
        $arr=array("2"=>"報告済","3"=>"確認","4"=>"却下");
        return $arr[$houkoku];
    }

    //祝日の計算
    public static function ktHolidayName($prmDate)
    {
        $HolidayName = Func::prvHolidayChk($prmDate);

        if ($HolidayName == "") {
            if (date('w',strtotime($prmDate)) == 1) {
                // 月曜以外は振替休日判定不要
                // 5/6(火,水)の判定はprvHolidayChkで処理済
                // 5/6(月)はここで判定する
                $YesterDay =date('Y-m-d',strtotime("$prmDate -1 day"));
                $HolidayName = Func::prvHolidayChk($YesterDay);
                if ($HolidayName != "") {
                    $HolidayName_ret = "振替休日";
                } else {
                    $HolidayName_ret = "";
                }

            } else {
                $HolidayName_ret = "";
            }
        } else {
            $HolidayName_ret = $HolidayName;
        }

        return $HolidayName_ret;
    }


    public static function prvHolidayChk($MyDate)
    {
        $MyYear = date('Y', strtotime($MyDate));
        $MyMonth = date('m', strtotime($MyDate));
        $MyDay = date('d', strtotime($MyDate));
        $Result = "";

        switch ($MyMonth) {
// １月 //
            case "01":
                if ($MyDay == "01") {
                    $Result = "元日";
                } else {
                    $NumberOfWeek = floor($MyDay / 7) + 1;
                    if (($NumberOfWeek == 2) && (date('w', strtotime($MyDate)) == 1)) {
                        $Result = "成人の日";
                    }
                }
                break;
// ２月 //
            case "02":
                if ($MyDay == "11") {
                    $Result = "建国記念の日";
                }
                break;
// ３月 //
            case "03":
                if ($MyDay == Func::prvDayOfSpringEquinox($MyYear)) {  // 1948～2150以外は[99]
                    $Result = "春分の日";                       // が返るので､必ず≠になる
                }
                break;
// ４月 //
            case "04":
                if ($MyDay == 29) {
                    $Result = "昭和の日";
                }
                break;
// ５月 //
            case "05":
                switch ($MyDay) {
                    case 3:  // ５月３日
                        $Result = "憲法記念日";
                        break;
                    case 4:  // ５月４日
                        $Result = "みどりの日";
                        break;
                    case 5:  // ５月５日
                        $Result = "こどもの日";
                        break;
                    case 6:  // ５月６日
                        if ((date('w', strtotime($MyDate)) == 2) || (date('w', strtotime($MyDate)) == 3)) {
                            $Result = "振替休日";    // [5/3,5/4が日曜]ケースのみ、ここで判定}
                            break;
                        }
                }
                break;

// ６月 //
            case "06":
                break;
// ７月 //
            case "07":
                $NumberOfWeek = floor(($MyDay - 1) / 7) + 1;
                if (($NumberOfWeek == 3) && (date('w', strtotime($MyDate)) == 1)) {
                    $Result = "海の日";
                }
                break;
// ８月 //
            case "08":
                if ($MyDay == 11) {
                    if ($MyYear >= 2016) {
                        $Result = "山の日";
                    }
                } else;
                break;
// ９月 //
            case "09":
                //第３月曜日(15～21)と秋分日(22～24)が重なる事はない
                $MyAutumnEquinox = Func::prvDayOfAutumnEquinox($MyYear);
                if ($MyDay == $MyAutumnEquinox) {    // 1948～2150以外は[99]
                    $Result = "秋分の日";           // が返るので､必ず≠になる
                } else {

                    $NumberOfWeek =floor(($MyDay - 1) / 7) + 1;
                    if (($NumberOfWeek == 3) && (date('w',strtotime($MyDate)) == 1)) {
                        $Result = "敬老の日";
                    } else {
                        if (date('w',strtotime($MyDate)) == 2) {
                            if ($MyDay == ($MyAutumnEquinox - 1)) {
                                $Result = "国民の休日";
                            } else;
                        }
                    }

                }
                break;
// １０月 //
            case "10":
                $NumberOfWeek = floor(($MyDay - 1) / 7) + 1;
                if (($NumberOfWeek == 2) && (date('w',strtotime($MyDate)) == 1)) {
                    $Result = "体育の日";
                }

                break;
// １１月 //
            case "11":
                if ($MyDay == 3) {
                    $Result = "文化の日";
                } else {
                    if ($MyDay == 23) {
                        $Result = "勤労感謝の日";
                    }
                }
                break;
// １２月 //
            case "12":
                if ($MyDay == 23) {
                    $Result = "天皇誕生日";
                }
                break;
        }

        return $Result;
    }


    //===================================================================
// 春分/秋分日の略算式は
// 『海上保安庁水路部 暦計算研究会編 新こよみ便利帳』
// で紹介されている式です。
    public static function prvDayOfSpringEquinox($MyYear)
    {
        $SpringEquinox_ret="";

        if ($MyYear <= 2099) {
            $SpringEquinox_ret = floor(20.8431 +(0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
        } else {
            if ($MyYear <= 2150) {
                $SpringEquinox_ret = floor(21.851 +
                    (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
            } else {
                $SpringEquinox_ret = 99;    //2151年以降は略算式が無いので不明
            }
        }

        return $SpringEquinox_ret;
    }


//=====================================================================
//秋分日の計算方法
    public static function prvDayOfAutumnEquinox($MyYear)
    {

        if ($MyYear <= 2099) {
            $AutumnEquinox_ret = floor(23.2488 +
                (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
        } else {
            if ($MyYear <= 2150) {
                $AutumnEquinox_ret = floor(24.2488 +
                    (0.242194 * ($MyYear - 1980)) - floor(($MyYear - 1980) / 4));
            } else {
                $AutumnEquinox_ret = 99;    //2151年以降は略算式が無いので不明
            }}

        return $AutumnEquinox_ret;
    }

    //ユーザーの当月のデータを取り出す
    public static  function getHgbyMonth($Ymd,$user_id){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }

        $month_start = date("Ym01", strtotime($Ymd));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
        $month_end = date("Ymt", strtotime($Ymd));//date('Y-m-d',strtotime('last day of this month','2015-12-10'));

        //条件日期の勤務情報のsql
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
            ." hiyous.SINSEIDATE "
            ."	BETWEEN "
            ."'".$month_start."'"
            ."	AND "
            ."'".$month_end."'"
            ."ORDER BY"
            ." hiyous.SINSEIDATE ";

        $month_list = DB::select($month_sql);

        return $month_list;
    }

    //ユーザーの当月のデータを取り出す
    public static  function getHlbyMonth($Ymd,$user_id){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }

        $month_start = date("Ym01", strtotime($Ymd));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
        $month_end = date("Ymt", strtotime($Ymd));//date('Y-m-d',strtotime('last day of this month','2015-12-10'));

        //条件日期の勤務情報のsql
        $month_sql = "	 SELECT "
            ." hiyous.id,"
            ." hiyous.SHAINCD,"
            ." hiyous.SINSEICD,"
            ." hiyous.SINSEIDATE,"
            ." hiyous.HASSEIDATE,"
            ." hiyous.SONOTAHI,"
            ." hiyous.KOUTUUHI,"
            ." hiyous.SHUKUHAKUHI,"
            ." hiyous.IKISAKI,"
            ." hiyous.UTIWAKE,"
            ." hiyous.BIKOU"
            ." FROM hiyous "
            ." WHERE hiyous.SHAINCD = ".$user_id
            ."	AND "
            ." hiyous.SINSEICD = '3'"
            ."	AND "
            ." hiyous.SINSEIDATE "
            ."	BETWEEN "
            ."'".$month_start."'"
            ."	AND "
            ."'".$month_end."'"
            ."ORDER BY"
            ." hiyous.SINSEIDATE ";

        $month_list = DB::select($month_sql);

        return $month_list;
    }

    //ユーザーの当月のデータを取り出す
    public static  function getHkbyMonth($Ymd,$user_id){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }

        $month_start = date("Ym01", strtotime($Ymd));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
        $month_end = date("Ymt", strtotime($Ymd));//date('Y-m-d',strtotime('last day of this month','2015-12-10'));

        //条件日期の勤務情報のsql
        $month_sql = "	 SELECT "
            ." hiyous.id,"
            ." hiyous.SHAINCD,"
            ." hiyous.SINSEICD,"
            ." hiyous.SINSEIDATE,"
            ." hiyous.HASSEIDATE,"
            ." hiyous.KOUTUUHI,"
            ." hiyous.EKIFROM,"
            ." hiyous.EKITO,"
            ." hiyous.BIKOU,"
            ." hiyous.ROOT"
            ." FROM hiyous "
            ." WHERE hiyous.SHAINCD = ".$user_id
            ."	AND "
            ." hiyous.SINSEICD = '1'"
            ."	AND "
            ." hiyous.TEKIFLG != '1'"
            ."	AND "
            ." hiyous.SINSEIDATE "
            ."	BETWEEN "
            ."'".$month_start."'"
            ."	AND "
            ."'".$month_end."'"
            ."ORDER BY"
            ." hiyous.SINSEIDATE ";

        $month_list = DB::select($month_sql);

        return $month_list;
    }

    //ユーザーの当月のデータを取り出す
    public static  function getHkTbyMonth($Ymd,$user_id){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }

        $month_start = date("Ym01", strtotime($Ymd));//'2015-12-01'; //date('Y-m-d',strtotime('first day of this month','2015-12-10'));
        $month_end = date("Ymt", strtotime($Ymd));//date('Y-m-d',strtotime('last day of this month','2015-12-10'));

        //条件日期の勤務情報のsql
        $month_sql = "	 SELECT "
            ." hiyous.id,"
            ." hiyous.SHAINCD,"
            ." hiyous.SINSEICD,"
            ." hiyous.SINSEIDATE,"
            ." hiyous.HASSEIDATE,"
            ." hiyous.KOUTUUHI,"
            ." hiyous.EKIFROM,"
            ." hiyous.EKITO,"
            ." hiyous.BIKOU,"
            ." hiyous.ROOT"
            ." FROM hiyous "
            ." WHERE hiyous.SHAINCD = ".$user_id
            ."	AND "
            ." hiyous.SINSEICD = '1'"
            ."	AND "
            ." hiyous.TEKIFLG = '1'"
            ."	AND "
            ." hiyous.SINSEIDATE "
            ."	BETWEEN "
            ."'".$month_start."'"
            ."	AND "
            ."'".$month_end."'"
            ."ORDER BY"
            ." hiyous.SINSEIDATE ";

        $month_list = DB::select($month_sql);

        return $month_list;
    }


    //ユーザーの当月のデータを取り出す
    public static  function getZbbyWeek1($Ymd,$user_id,$date1){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.SUPERVISOR,"
            ." zbs.ASSISTSUPERVISOR,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date1'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbbyWeek2($Ymd,$user_id,$date2){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date2'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbbyWeek3($Ymd,$user_id,$date3){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date3'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbbyWeek4($Ymd,$user_id,$date4){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date4'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbbyWeek5($Ymd,$user_id,$date5){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date5'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbbyWeek6($Ymd,$user_id,$date6){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date6'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbbyWeek7($Ymd,$user_id,$date7){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.NAIYO"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.NENGAPI = "."'$date7'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbreport($Ymd,$user_id,$date_start,$date_end){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.REPORT"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.RPTFLG = "."1"
            ."	AND "
            ." zbs.SINSEIDATE "
            ."	BETWEEN "
            ."'".$date_start."'"
            ."	AND "
            ."'".$date_end."'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getZbconfirm($Ymd,$user_id,$date_start,$date_end){

        if(null == $Ymd){
            $Ymd = date('Ymd');
        }


        //条件日期の勤務情報のsql
        $week_sql = "	 SELECT "
            ." zbs.id,"
            ." zbs.CONFIRM"
            ." FROM zbs "
            ." WHERE zbs.SHAINCD = ".$user_id
            ."	AND "
            ." zbs.RPTFLG = "."2"
            ."	AND "
            ." zbs.SINSEIDATE "
            ."	BETWEEN "
            ."'".$date_start."'"
            ."	AND "
            ."'".$date_end."'";

        $week_list = DB::select($week_sql);

        return $week_list;
    }

    public static  function getBu(){

        //条件日期の勤務情報のsql
        $bu_sql = "	 SELECT "
            ."  * "
            ." FROM division "
            ." ORDER BY"
            ." division.DIVISIONCD	";


        $bu_list = DB::select($bu_sql);

        return $bu_list;
    }

    public static  function getMember(){

        //条件日期の勤務情報のsql
        $member_sql = "	 SELECT "
            ." * "
            ." FROM userinfo ";


        $member_list = DB::select($member_sql);

        return $member_list;
    }

    public static  function getMemberIndex($id1,$name1,$division1){


        if(null == $id1 && null != $name1) {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.NAME LIKE '%".$name1."%'"
                . "	AND "
                . " userinfo.DIVISION = ?";

            $member_list = DB::select($member_sql,array($division1));

        }else if(null == $name1 && null != $id1) {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.id = ?"
                . "	AND "
                . " userinfo.DIVISION = ?";

            $member_list = DB::select($member_sql,array($id1,$division1));

        }else if(null == $name1 && null == $id1) {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.DIVISION = ? ";

            $member_list = DB::select($member_sql,array($division1));

        }else {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.NAME LIKE '%".$name1."%'"
                . "	AND "
                . " userinfo.id = ?"
                . "	AND "
                . " userinfo.DIVISION = ?";


            $member_list = DB::select($member_sql,array($id1,$division1));
        }

        if(null == $name1 && "01" == $division1){
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.id = ?";

            $member_list = DB::select($member_sql,array($id1));
        }

        if(null == $id1 && "01" == $division1){
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.NAME LIKE '%".$name1."%'";

            $member_list = DB::select($member_sql);
        }

        if(null != $name1 && null != $id1 && "01" == $division1){
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                . " * "
                . " FROM userinfo "
                . " WHERE userinfo.id = ?"
                . "	AND "
                . " userinfo.NAME LIKE '%".$name1."%'";

            $member_list = DB::select($member_sql,array($id1));
        }

       // $member_list = DB::select($member_sql,array($id1,$name1,$division1));

        return $member_list;
    }


    //承認者と代理承認者をゲット
    public static  function getSupervisor(){

        //条件日期の勤務情報のsql
        $member_sql = "	 SELECT "
            ." userinfo.id,"
            ." userinfo.NAME,"
            ." userinfo.DIVISION"
            ." FROM userinfo "
            . " WHERE userinfo.permission_id <" ."3"
            . "	AND "
            . " userinfo.permission_id >" ."0";


        $member_list = DB::select($member_sql);

        return $member_list;
    }

    //条件を入れて承認者と代理承認者をゲット
    public static  function getSupervisorIndex($id1,$name1,$division1){

        if(null == $id1 && null != $name1) {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.NAME LIKE '%".$name1."%'"
                . "	AND "
                . " userinfo.DIVISION = ?"
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql,array($division1));

        }else if(null == $name1 && null != $id1) {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.id LIKE ?"
                . "	AND "
                . " userinfo.DIVISION = ?"
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql,array($id1,$division1));

        }else if(null == $name1 && null == $id1) {
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.DIVISION = ? "
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql,array($division1));

        }else{
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.NAME LIKE '%".$name1."%'"
                . "	AND "
                . " userinfo.id LIKE ?"
                . "	AND "
                . " userinfo.DIVISION = ?"
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql,array($id1,$division1));
        }

        if(null == $name1 && "01" == $division1){
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.id LIKE ?"
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql,array($id1));
        }

        if(null == $id1 && "01" == $division1){
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.NAME LIKE '%".$name1."%'"
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql);
        }

        if(null != $name1 && null != $id1 && "01" == $division1){
            //条件日期の勤務情報のsql
            $member_sql = "	 SELECT "
                ." userinfo.id,"
                ." userinfo.NAME,"
                ." userinfo.DIVISION"
                . " FROM userinfo "
                . " WHERE userinfo.id LIKE ?"
                . "	AND "
                . " userinfo.NAME LIKE '%".$name1."%'"
                . "	AND "
                . " userinfo.permission_id <" ."3"
                . "	AND "
                . " userinfo.permission_id >" ."0";

            $member_list = DB::select($member_sql,array($id1));
        }

        // $member_list = DB::select($member_sql,array($id1,$name1,$division1));

        return $member_list;
    }

    //承認者と代理承認者をゲット
    public static  function getSupervisorName($supervisorid){

        //条件日期の勤務情報のsql
        $member_sql = "	 SELECT "
            ." userinfo.NAME,"
            ." userinfo.ADDRESS"
            ." FROM userinfo "
            ." WHERE userinfo.id =".$supervisorid;


        $member_list = DB::select($member_sql);

        return $member_list;
    }

    //承認者と代理承認者をゲット for 週報
    public static  function getSupervisorZb($username){

        //条件日期の勤務情報のsql
        $member_sql = "	 SELECT "
            ." userinfo.SUPERVISOR,"
            ." userinfo.ASSISTSUPERVISOR"
            ." FROM userinfo "
            ." WHERE userinfo.id =".$username;


        $member_list = DB::select($member_sql);

        return $member_list;
    }

    //部門名前をゲット
    public static  function getDivisionName(){

        //条件日期の勤務情報のsql
        $member_sql = "	 SELECT "
            ." division.DIVISION"
            ." FROM division "
            ." ORDER BY"
            ." division.DIVISIONCD	";


        $member_list = DB::select($member_sql);

        return $member_list;
    }

    //password reset
    public static  function passwordReset($usercd, $password1){

        //条件日期の勤務情報のsql
        $member_sql = "	 UPDATE "
            ." users "
            ." SET"
            ." users.password = ?"
            ." WHERE users.email = ?";


        $wlist = DB::select( $member_sql,array($password1,$usercd));

        return $wlist;
    }

    //users update
    public static  function usersUpdate($page_day, $id, $name, $password, $address){

        if(null == $page_day){
            $page_day = date('Y-m-d');
        }

        //条件日期の勤務情報のsql
        $wlist_reset = "  UPDATE "
            ." `userinfo` "
            ." SET"
            ." `ADDRESS` = '".$address."'"
            .", `NAME` = '".$name."'"
            .", `PASSWORD` = '".$password."'"
            .", `updated_at` = '".$page_day."'"
            ." WHERE `id` = '".$id."'";

        $wlist = DB::select($wlist_reset);

        //$wlist_reset = "  UPDATE "
        //    ." users "
        //    ." SET"
        //    ." users.id = ?"
        //    ." users.name = ?"
        //    ." users.password = ?"
        //    ." users.updated_at = ?"
        //    ." WHERE users.email = ?";

        //$wlist = DB::select($wlist_reset,array($address, $name, $password, $page_day, $id));

        return $wlist;
    }
}