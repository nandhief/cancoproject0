<?php
class DashboardController extends BaseController {
	public function redirectMissing() {
		Session::flush();
		return Redirect::to('login');
	}

	public function dashboardAdmin() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			//if($userData->{"U_GROUP_ROLE"} === "GR_ADMINISTRATOR") {
				if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
	        $periode = date("Y-m-d");
	        $periode_dmY = date("d-m-Y");
	        $tgl = date("d");
	        $bln = date("m");
	        $thn = date("Y");
	      }
	      else {
	        $periode = Input::get("periode");
	        $arrTgl = explode("-", $periode);
	        $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
	        $tgl = $arrTgl[2];
	        $bln = $arrTgl[1];
	        $thn = $arrTgl[0];
	      }

	      $jmlTagihan = 0;
	      $jmlBayar = 0;
	      $tdkBayar = 0;
	      $tdkBertemu = 0;
	      $dataBayar = array();
	      $dataTdkBayar = array();
	      $dataTdkBertemu = array();
	      $dataJadwal = array();
	      $dataByDate = null;

	      //UPDATE
	      //$batchUpload = DB::select("SELECT * FROM coll_batch_upload WHERE PRSH_ID = ? AND BU_TGL <= ? ORDER BY BU_ID DESC LIMIT 0,1", array($userData->{"PRSH_ID"}, $periode));
	      $batchUpload = DB::table("coll_batch_upload")
	      	/*->where("BU_TGL", "<=", $periode)*/
	      	->where("BU_TGL", "<=", date("Y-m-d"))
	      	//->where("PRSH_ID", $userData->{"PRSH_ID"})
	      	->orderBy("BU_ID", "desc")
	      	->first();
	      if(count($batchUpload) > 0) {
	      	$ringkasan = DB::select("SELECT IFNULL(SUM(X.BUD_PINJ_JUMLAH),0) AS BUD_PINJ_JUMLAH, IFNULL(SUM(X.BUD_PINJ_JUMLAH_BAYAR),0) AS BUD_PINJ_JUMLAH_BAYAR FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS ORDER BY X.BUD_COLL_U_ID", array($batchUpload->{"BU_ID"}));

	      	$jmlTagihan = $ringkasan[0]->{"BUD_PINJ_JUMLAH"};
	      	$jmlBayar = $ringkasan[0]->{"BUD_PINJ_JUMLAH_BAYAR"};

	      	$ringkasan = DB::select("SELECT IFNULL(COUNT(X.BUD_ID),0) AS JUMLAH FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS = 'ST_TIDAK_BAYAR'", array($batchUpload->{"BU_ID"}));
	      	$tdkBayar = $ringkasan[0]->{"JUMLAH"};

	      	$ringkasan = DB::select("SELECT IFNULL(COUNT(X.BUD_ID),0) AS JUMLAH FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS = 'ST_TIDAK_DITEMUKAN'", array($batchUpload->{"BU_ID"}));
	      	$tdkBertemu = $ringkasan[0]->{"JUMLAH"};

	      	$dataBayar = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_BAYAR','ST_BAYAR_PARSIAL')", array($batchUpload->{"BU_ID"}));

		      $dataTdkBayar = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_TIDAK_BAYAR')", array($batchUpload->{"BU_ID"}));

		      $dataTdkBertemu = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_TIDAK_DITEMUKAN')", array($batchUpload->{"BU_ID"}));

		      $dataJadwal = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_JADWAL')", array($batchUpload->{"BU_ID"}));
	      }

	      //------
	      //$jadwal = DB::select("SELECT A.*,B.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID WHERE MONTH(A.J_TGL) = ? AND YEAR(A.J_TGL) = ? AND A.PRSH_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($bln, $thn, $userData->{"PRSH_ID"}));
	      $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
	      for($i=1; $i<=$daysOfMonth; $i++) {
	      	$aDate = $thn."-".$bln."-".$i;
	      	if(intval($i) < 10)	$aDate = $thn."-".$bln."-0".$i;
	      	$jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID WHERE A.J_TGL = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($aDate));

	      	$arrJadwal = array();
	      	$jmlJadwal = 0;
	      	$jmlTdkBayar = 0;
	      	$jmlTdkBertemu = 0;
	      	$jmlTagihan = 0;
	      	$jmlBayar = 0;
	      	$totalBayar = 0;
	      	foreach ($jadwal as $aData) {
	      		if($aData->{"J_STATUS"} == "ST_JADWAL")						$jmlJadwal++;
	      		if($aData->{"J_STATUS"} == "ST_TIDAK_BAYAR")			$jmlTdkBayar++;
	      		if($aData->{"J_STATUS"} == "ST_TIDAK_DITEMUKAN")	$jmlTdkBertemu++;

	      		$jmlTagihan += $aData->{"J_PINJ_JUMLAH"};

	      		if($aData->{"J_STATUS"} == "ST_BAYAR") {
	      			//jika ditemukan status bayar, cek apakah pembayaran trjd pd tgl yg sdg dilooping
	      			if($aData->{"TGL_STATUS"} == $aDate) {
	      				$jmlBayar++;
	      				$totalBayar += $aData->{"J_PINJ_JUMLAH_BAYAR"};
	      			}
	      		}

	      		if($aData->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
	      			if($aData->{"TGL_STATUS"} == $aDate) {
	      				$jmlBayar++;
	      				$totalBayar += $aData->{"J_PINJ_JUMLAH_BAYAR"};
	      			}
	      		}

	      		$collectorData = DB::table("coll_user")->where("U_ID", $aData->{"J_COLL_U_ID"})->first();
	      		$aData->{"COLLECTOR_NAMA"} = $collectorData->{"U_NAMA"};
	      	}
	      	$arrData[$i-1] = array(
	      		'TGL' => $aDate,
	      		'TGL_FORMATTED' => tglIndo($aDate,"SHORT"),
	      		'DATA_JADWAL' => $jadwal,
	      		'SUMMARY_TAGIHAN' => $jmlTagihan,
	      		'SUMMARY_TAGIHAN_FORMATTED' => number_format($jmlTagihan),
	      		'SUMMARY_BAYAR_JML' => $jmlBayar,
	      		'SUMMARY_BAYAR_NOMINAL' => $totalBayar,
	      		'SUMMARY_BAYAR_NOMINAL_FORMATTED' => number_format($totalBayar),
	      		'SUMMARY_JADWAL_JML' => $jmlJadwal,
	      		'SUMMARY_TIDAK_BAYAR_JML' => $jmlTdkBayar,
	      		'SUMMARY_TIDAK_BERTEMU_JML' => $jmlTdkBertemu
	      	);

	      	if($aDate == $periode)	$dataByDate = $arrData[$i-1];
	      }

	      //----
	      $ringkasanPerCollector = DB::select("SELECT SUM(Y.BUD_PINJ_JUMLAH_BAYAR) AS JUMLAH_BAYAR, SUM(Y.BUD_PINJ_JUMLAH) AS JUMLAH_TAGIHAN,X.J_COLL_U_ID, Z.U_NAMA FROM coll_jadwal AS X INNER JOIN coll_batch_upload_data AS Y ON X.BUD_ID = Y.BUD_ID INNER JOIN coll_user AS Z ON X.J_COLL_U_ID = Z.U_ID WHERE X.J_TGL = ? GROUP BY J_COLL_U_ID, U_NAMA", array($thn."-".$bln."-".$tgl));

				return View::make("dashboard.dashboard-admin")	
					->with("ctlUserData",$userData)
					->with("ctlJmlTagihan", $jmlTagihan)
					->with("ctlJmlBayar", $jmlBayar)
					->with("ctlJmlTdkBayar", $tdkBayar)
					->with("ctlJmlTdkBertemu", $tdkBertemu)
					->with("ctlDataBayar", $dataBayar)
					->with("ctlDataTdkBayar", $dataTdkBayar)
					->with("ctlDataTdkBertemu", $dataTdkBertemu)
					->with("ctlDataJadwal", $dataJadwal)
					->with("ctlPeriode", $periode)
					->with("ctlPeriode_dmY", $periode_dmY)
					->with("ctlNavMenu", "mCollDashboard")
					->with("ctlSummary", $arrData)
					->with("ctlSummaryByDate", $dataByDate)
					->with("ctlSummaryByCollector", $ringkasanPerCollector)
					->with("ctlFilterMonth", $bln)
					->with("ctlFilterYear", $thn);
			// }	
			// else {
			// 	return Redirect::to("/company");
			// }				
		}
		else {
			Session::flush();
			return Redirect::to('/login')->with('ctlError','Please login for system access');
		}
	}

	public function dashboardMain() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			//if($userData->{"U_GROUP_ROLE"} === "GR_ADMINISTRATOR") {
				if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
	        $periode = date("Y-m-d");
	        $periode_dmY = date("d-m-Y");
	        $tgl = date("d");
	        $bln = date("m");
	        $thn = date("Y");
	      }
	      else {
	        $periode = Input::get("periode");
	        $arrTgl = explode("-", $periode);
	        $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
	        $tgl = $arrTgl[2];
	        $bln = $arrTgl[1];
	        $thn = $arrTgl[0];
	      }

	      $jmlTagihan = 0;
	      $jmlBayar = 0;
	      $tdkBayar = 0;
	      $tdkBertemu = 0;
	      $dataBayar = array();
	      $dataTdkBayar = array();
	      $dataTdkBertemu = array();
	      $dataJadwal = array();
	      $dataByDate = null;

	      //UPDATE
	      //$batchUpload = DB::select("SELECT * FROM coll_batch_upload WHERE PRSH_ID = ? AND BU_TGL <= ? ORDER BY BU_ID DESC LIMIT 0,1", array($userData->{"PRSH_ID"}, $periode));
	      $batchUpload = DB::table("coll_batch_upload")
	      	/*->where("BU_TGL", "<=", $periode)*/
	      	->where("BU_TGL", "<=", date("Y-m-d"))
	      	->where("PRSH_ID", $userData->{"PRSH_ID"})
	      	->orderBy("BU_ID", "desc")
	      	->first();
	      if(count($batchUpload) > 0) {
	      	$ringkasan = DB::select("SELECT IFNULL(SUM(X.BUD_PINJ_JUMLAH),0) AS BUD_PINJ_JUMLAH, IFNULL(SUM(X.BUD_PINJ_JUMLAH_BAYAR),0) AS BUD_PINJ_JUMLAH_BAYAR FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS ORDER BY X.BUD_COLL_U_ID", array($batchUpload->{"BU_ID"}));

	      	$jmlTagihan = $ringkasan[0]->{"BUD_PINJ_JUMLAH"};
	      	$jmlBayar = $ringkasan[0]->{"BUD_PINJ_JUMLAH_BAYAR"};

	      	$ringkasan = DB::select("SELECT IFNULL(COUNT(X.BUD_ID),0) AS JUMLAH FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS = 'ST_TIDAK_BAYAR'", array($batchUpload->{"BU_ID"}));
	      	$tdkBayar = $ringkasan[0]->{"JUMLAH"};

	      	$ringkasan = DB::select("SELECT IFNULL(COUNT(X.BUD_ID),0) AS JUMLAH FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS = 'ST_TIDAK_DITEMUKAN'", array($batchUpload->{"BU_ID"}));
	      	$tdkBertemu = $ringkasan[0]->{"JUMLAH"};

	      	$dataBayar = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_BAYAR','ST_BAYAR_PARSIAL')", array($batchUpload->{"BU_ID"}));

		      $dataTdkBayar = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_TIDAK_BAYAR')", array($batchUpload->{"BU_ID"}));

		      $dataTdkBertemu = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_TIDAK_DITEMUKAN')", array($batchUpload->{"BU_ID"}));

		      $dataJadwal = DB::select("SELECT X.*, B.BU_TGL, C.U_NAMA FROM (SELECT A.*,'".$periode."' AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X  INNER JOIN coll_batch_upload AS B ON X.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON X.BUD_COLL_U_ID = C.U_ID AND X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_STATUS IN ('ST_JADWAL')", array($batchUpload->{"BU_ID"}));
	      }

	      //------
	      //$jadwal = DB::select("SELECT A.*,B.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID WHERE MONTH(A.J_TGL) = ? AND YEAR(A.J_TGL) = ? AND A.PRSH_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($bln, $thn, $userData->{"PRSH_ID"}));
	      $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
	      for($i=1; $i<=$daysOfMonth; $i++) {
	      	$aDate = $thn."-".$bln."-".$i;
	      	if(intval($i) < 10)	$aDate = $thn."-".$bln."-0".$i;
	      	$jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID WHERE A.J_TGL = ? AND A.PRSH_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($aDate, $userData->{"PRSH_ID"}));

	      	$arrJadwal = array();
	      	$jmlJadwal = 0;
	      	$jmlTdkBayar = 0;
	      	$jmlTdkBertemu = 0;
	      	$jmlTagihan = 0;
	      	$jmlBayar = 0;
	      	$totalBayar = 0;
	      	foreach ($jadwal as $aData) {
	      		if($aData->{"J_STATUS"} == "ST_JADWAL")						$jmlJadwal++;
	      		if($aData->{"J_STATUS"} == "ST_TIDAK_BAYAR")			$jmlTdkBayar++;
	      		if($aData->{"J_STATUS"} == "ST_TIDAK_DITEMUKAN")	$jmlTdkBertemu++;

	      		$jmlTagihan += $aData->{"J_PINJ_JUMLAH"};

	      		if($aData->{"J_STATUS"} == "ST_BAYAR") {
	      			//jika ditemukan status bayar, cek apakah pembayaran trjd pd tgl yg sdg dilooping
	      			if($aData->{"TGL_STATUS"} == $aDate) {
	      				$jmlBayar++;
	      				$totalBayar += $aData->{"J_PINJ_JUMLAH_BAYAR"};
	      			}
	      		}

	      		if($aData->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
	      			if($aData->{"TGL_STATUS"} == $aDate) {
	      				$jmlBayar++;
	      				$totalBayar += $aData->{"J_PINJ_JUMLAH_BAYAR"};
	      			}
	      		}

	      		$collectorData = DB::table("coll_user")->where("U_ID", $aData->{"J_COLL_U_ID"})->first();
	      		$aData->{"COLLECTOR_NAMA"} = $collectorData->{"U_NAMA"};
	      	}
	      	$arrData[$i-1] = array(
	      		'TGL' => $aDate,
	      		'TGL_FORMATTED' => tglIndo($aDate,"SHORT"),
	      		'DATA_JADWAL' => $jadwal,
	      		'SUMMARY_TAGIHAN' => $jmlTagihan,
	      		'SUMMARY_TAGIHAN_FORMATTED' => number_format($jmlTagihan),
	      		'SUMMARY_BAYAR_JML' => $jmlBayar,
	      		'SUMMARY_BAYAR_NOMINAL' => $totalBayar,
	      		'SUMMARY_BAYAR_NOMINAL_FORMATTED' => number_format($totalBayar),
	      		'SUMMARY_JADWAL_JML' => $jmlJadwal,
	      		'SUMMARY_TIDAK_BAYAR_JML' => $jmlTdkBayar,
	      		'SUMMARY_TIDAK_BERTEMU_JML' => $jmlTdkBertemu
	      	);

	      	if($aDate == $periode)	$dataByDate = $arrData[$i-1];
	      }

	      //----
	      $ringkasanPerCollector = DB::select("SELECT SUM(Y.BUD_PINJ_JUMLAH_BAYAR) AS JUMLAH_BAYAR, SUM(Y.BUD_PINJ_JUMLAH) AS JUMLAH_TAGIHAN,X.J_COLL_U_ID, Z.U_NAMA FROM coll_jadwal AS X INNER JOIN coll_batch_upload_data AS Y ON X.BUD_ID = Y.BUD_ID INNER JOIN coll_user AS Z ON X.J_COLL_U_ID = Z.U_ID WHERE X.J_TGL = ? AND X.PRSH_ID = ? GROUP BY J_COLL_U_ID, U_NAMA", array($thn."-".$bln."-".$tgl, $userData->{"PRSH_ID"}));

				return View::make("dashboard.dashboard-main")	
					->with("ctlUserData",$userData)
					->with("ctlJmlTagihan", $jmlTagihan)
					->with("ctlJmlBayar", $jmlBayar)
					->with("ctlJmlTdkBayar", $tdkBayar)
					->with("ctlJmlTdkBertemu", $tdkBertemu)
					->with("ctlDataBayar", $dataBayar)
					->with("ctlDataTdkBayar", $dataTdkBayar)
					->with("ctlDataTdkBertemu", $dataTdkBertemu)
					->with("ctlDataJadwal", $dataJadwal)
					->with("ctlPeriode", $periode)
					->with("ctlPeriode_dmY", $periode_dmY)
					->with("ctlNavMenu", "mCollDashboard")
					->with("ctlSummary", $arrData)
					->with("ctlSummaryByDate", $dataByDate)
					->with("ctlSummaryByCollector", $ringkasanPerCollector)
					->with("ctlFilterMonth", $bln)
					->with("ctlFilterYear", $thn);
			// }	
			// else {
			// 	return Redirect::to("/company");
			// }				
		}
		else {
			Session::flush();
			return Redirect::to('/login')->with('ctlError','Please login for system access');
		}
	}

	public function getSummary() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y-m-d");
      }
      else {
        $periode = Input::get("periode");
      }

      
			return composeReply("SUCCESS", "Summary data");
		}
		else {
			Session::flush();
			return composeReply("ERROR", "Silahkan login dahulu");
		}	
	}

	public function tesTgl() {
		$date = date("Y-m-d", strtotime("2017-05-11"));
		$days = 10;

		$targetDate = addDaysWithDate($date,$days)."<br>";

		if(date("Y-m-d") > $targetDate)	$targetDate .= "LEBIH BESAR";
		if(date("Y-m-d") < $targetDate)	$targetDate .= "KURANG DARI";

		return $targetDate;
	}
	/*
	public function backupDatabase() {
		$backupFile = backupTables("localhost","root","md123","sia","*");
		forceDownload($backupFile,$backupFile);
	}
	*/
	public function tesTgl2() {
		return "Server time from controller : ".tglIndo(date("Y-m-d H:i:s"),"SHORT");
	}
}
?>