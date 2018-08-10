<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKmgsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kmgs', function(Blueprint $table)
		{
			$table->increments('id');
				$table->string('SHAINCD',40);  //社員コード

				$table->string('NENGETU',6);//年月
				$table->string('NENDO',4);//年度

				$table->string('BUMONCD',5);//部門コード

				$table->float('STANDARDDAY');//月間標準勤務日数
				$table->float('STANDARDTIME');//月間標準勤務時間

				$table->float('KINMUDAY');//勤務日数
				$table->float('KINMUTIME');//勤務時間

				$table->float('ZANGYOUTIMEF');//普通残業
				$table->float('ZANGYOUTIMES');//深夜残業
				$table->float('ZANGYOUTIMEK');//休日残業
				$table->float('SHORTAGETIME');//不足時間

				$table->float('TIKOKUTIME');//遅刻時間
				$table->float('SOUTAITIME');//早退時間

				$table->float('YUUKYUUCNT');//有給回数
				$table->float('HANKYUUCNT');//半休回数

				$table->float('YUUKYUUZAN');//有給残数
				$table->float('DAIKYUUCNT');//代休回数
				$table->float('KEKKINCNT');//欠勤回数
				$table->float('SONOTACNT');//その他回数

				$table->string('UPDATEDATE',8); //更新日

				$table->string('SINSEIDATE',8); //申請日
				$table->tinyInteger('SHOUNINSTATUS');//承認状態 2:申請　3:承認　4:却下

				$table->string('SHOUNINSHACD',7);//承認者コード
				$table->string('SHOUNINSHANM',20);//承認者名
				$table->string('SHOUNINDATE',20);//承認日
				$table->string('BKU',60);//備考
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('kmgs');
	}

}
