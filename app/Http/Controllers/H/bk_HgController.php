<?php namespace App\Http\Controllers\H;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\hiyou;
use DB;

use Redirect, Input, Auth;

class HgController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//�������̐\���t���O���`�F�b�N
		//$month_check = 0;
		//if(0!=$month_check){
		//	return view('k.km.show',compact('day_list','sum_kinmutime','sum_zangyoutimef','sum_zangyoutimes','sum_zangyoutimek','sum_shortagetime'));
		//}else{
			$page_day = date('Y-m-d');
			return Redirect::to('h/hg/'.$page_day.'/edit');
		//}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($time)
	{
		//$Ymd = date('Y-m-d',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_m = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		//$Ymd_sinseibi = '�\�����F'.$Ymd_Y.'�N'.$Ymd_m.'��'.$Ymd_d.'��';
		$Ymd_sinseibi = 'Apply:'.$Ymd_Y.'Y'.$Ymd_m.'M'.$Ymd_d.'D';

		return view('h.hg.create',compact('Ymd_sinseibi'));
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
	 * @return Response
	 */
	public function show()
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $time
	 * @return Response
	 */
	public function edit($time)
	{
		$Ymd = date('Y-m-d',strtotime($time));
		$Ymd_Y = date('Y',strtotime($time));
		$Ymd_m = date('m',strtotime($time));
		$Ymd_d = date('d',strtotime($time));
		$Ymd_sinseibi = '�\�����F'.$Ymd_Y.'�N'.$Ymd_m.'��'.$Ymd_d.'��';
		//$Ymd_sinseibi = $Ymd_Y.'�N'.$Ymd_m.'���x�Ζ��\';
		$day_list =$this -> getHgbyday($Ymd);
		//$list = hiyou::find($id);
		//var_dump($Ymd_sinseibi);

		//���z�v�Z�p
		$sum_sonotahi = 0;//�\�����z

		//���z���v
		foreach($day_list as $key => $value){
			$sum_sonotahi += $value->SONOTAHI;

		}
		$sum_sonotahi = number_format($sum_sonotahi, 2);

		return view('h.hg.edit',compact('day_list','Ymd_sinseibi','sum_sonotahi'));
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

	public function getHgbyday($Ymd){

		if($Ymd == null){
			$Ymd = date('Y-m-d');
		}

		//�����������[�UID
		$user_id = '10049';//���[�U�[ID���擾

		//��������:�N����
		$month_start = date("Ym01", strtotime($Ymd));//'2016-03-01'
		$month_end   = date("Ymt", strtotime($Ymd)); //������

		//���������̔�p�̐��Z�\������sql
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
