<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

date_default_timezone_set('Asia/Jakarta');

Route::get('/', function() {
  return Redirect::to('dashboard');
});

Route::post('/tester', 'UserController@tambahUser');

Route::get("/login","LoginController@showLogin");
Route::post("/login","LoginController@processLogin");
Route::get("/logout","LoginController@processLogout");
Route::post("/logout","LoginController@processLogout");

Route::get("/dashboard","DashboardController@dashboardMain");
Route::get("/dashboard/admin", "DashboardController@dashboardAdmin");

//route new admin
Route::get("/admin/collection/jadwal", "CollectionController@listJadwalAdmin");
Route::get("/admin/collection/tabungan", "CollectionController@listTabunganAdmin");
Route::get("/admin/collection/tabungan/laporan", "CollectionController@adminTabunganDownload");
Route::get("/admin/collection/tabungan/laporan/view", "CollectionController@adminTabunganView");

Route::get("/collection/collector", "CollectionController@formlistCollector");
Route::post("/collection/collector", "CollectionController@submitCollector");
Route::get("/collection/collector/detail", "CollectionController@listDetailCollector");

Route::get("/collection/jadwal-penagihan", "CollectionController@formlistJadwal");
Route::post("/collection/jadwal-penagihan", "CollectionController@submitJadwal");
// Route::delete("/collection/jadwal-penagihan", "CollectionController@deleteJadwal");
Route::post("/collection/jadwal-penagihan/delete", "CollectionController@deleteJadwal");
Route::get("/collection/jadwal-penagihan/{buId}", "CollectionController@listDetailJadwal");

//admin marker maps
Route::get("/admin/collection/monitoring", "CollectionController@displayMonitoringAdmin");
Route::post("/admin/collection/monitoring", "CollectionController@displayMonitoringAdmin");
Route::get("/admin/collection/monitoring/position", "CollectionController@getPositionAdmin");
//end marker

Route::get("/collection/monitoring", "CollectionController@displayMonitoring");
Route::post("/collection/monitoring", "CollectionController@displayMonitoring");
Route::get("/collection/monitoring/position", "CollectionController@getPosition");

///route tabungan web
Route::get("/collection/tabungan", "CollectionController@formlistTabungan");
Route::post("/collection/tabungan/store", "CollectionController@submitTabungan");
// Route::delete("/collection/tabungan", "CollectionController@deleteTabungan");
Route::post("/collection/tabungan/delete", "CollectionController@deleteTabungan");
Route::get("/collection/tabungan/detail/{buId}", "CollectionController@detailTabungan");

//route api tabungan
Route::get("/collection/api/tabungan", "CollectionController@apiGetTabungan");
Route::get("/collection/api/tabungan/history", "CollectionController@apiGetHistory");
Route::post("/collection/api/tabungan/store", "CollectionController@apiUpdateTabungan");

//end route

Route::get("/collection/laporan", "CollectionController@listLaporan");
Route::get("/collection/laporan/download", "CollectionController@downloadLaporan");
Route::get("/collection/laporan/view", "CollectionController@listLaporanView");

Route::get("/collection/api/jadwal", "CollectionController@apiListJadwal");
Route::post("/collection/api/jadwal", "CollectionController@apiListJadwal");
Route::post("/collection/api/jadwal/update", "CollectionController@apiUpdateJadwal");
Route::post("/collection/api/jadwal/nontarget", "CollectionController@apiUpdateJadwalX");
//api list full jadwal
Route::get("/collection/api/jadwal/full", "CollectionController@apiListJadwalFull");
Route::get("/collection/api/jadwal/search", "ApiController@search_penagihan");

Route::get("/collection/api/receipt", "CollectionController@apiGetReceipt");
Route::post("/collection/api/receipt", "CollectionController@apiGetReceipt");

Route::get("/collection/api/dashboard", "CollectionController@apiGetSummary");
Route::post("/collection/api/dashboard", "CollectionController@apiGetSummary");

Route::get("/collection/api/start-check-in", "CollectionController@apiRegisterStartCheckIn");
Route::post("/collection/api/start-check-in", "CollectionController@apiRegisterStartCheckIn");

Route::get("/collection", function() {
  return Redirect::to("/collection/jadwal-penagihan");
});
Route::post("/collection", function() {
  return Redirect::to("/collection/jadwal-penagihan");
});

//user management
Route::get("/user", "UserController@listUsers");
Route::get("/user/detail", "UserController@getUserData");
Route::post("/user", "UserController@addUser");
Route::post("/user/check", "UserController@check");
Route::post("/user/checkuser", "UserController@checkuser");
Route::post("/user/kodecheck", "UserController@kodecheck");
Route::put("/user", "UserController@updateUser");
// Route::delete("/user", "UserController@deleteUser");
Route::post("/user/delete", "UserController@deleteUser");
Route::get('/user/change-password', "UserController@editPassword");
Route::post('/user/change-password', "UserController@updatePassword");

//admin management
Route::post("/admin/delete", "UserController@deleteAdmin");
Route::get("/admin", "UserController@listAdmin");
Route::post("/admin/store", "UserController@addAdmin");
Route::get("/admin/laporan", "CollectionController@adminReport");
Route::get("/admin/laporan/download", "CollectionController@adminReportDownload");
Route::get("/admin/laporan/view", "CollectionController@adminReportView");
//end route

Route::post("/user/reset-password", "UserController@resetUserPassword");
Route::get("/user/reset-password", "UserController@resetUserPassword");

Route::post("/user/update-status", "UserController@updateUserStatus");
Route::get("/user/update-status", "UserController@updateUserStatus");

//company management
Route::get("/company", "CompanyController@listCompanies");
Route::post("/company/check", "CompanyController@checkLembaga");
Route::get("/company/detail", "CompanyController@getCompanyData");
Route::post("/company", "CompanyController@addCompany");
Route::post("/company-update", "CompanyController@updateCompany");
Route::post("/company/delete", "CompanyController@deleteCompany");

Route::post("/company/update-status", "CompanyController@updateCompanyStatus");
Route::get("/company/update-status", "CompanyController@updateCompanyStatus");

//profile
Route::get("/profil", "ProfileController@formProfile");
Route::put("/profil", "ProfileController@updateProfile");

Route::get("/settings", "SettingController@formSettings");
Route::post("/settings", "SettingController@updateSettings");

Route::get("/ref/{refCategory}", "CollectionController@getReference");

//admin settings
Route::get("/adm/settings", "SettingController@formAdmSettings");
Route::post("/adm/settings", "SettingController@updateAdmSettings");

//testing
Route::get("/tes", "LoginController@checkToken");
Route::get("/tes1", "DashboardController@tesTgl");
Route::get("/tes3", function() {
  $dateA = ("2017-08-09 10:22:34");
  $dateB = ("2017-08-10 15:11:11");
  $arrDiff = dayDifference2($dateA, $dateB, false);
  echo "DAY : ".$arrDiff["DAY"]."<br>";
  echo "MONTH : ".$arrDiff["MONTH"]."<br>";
  echo "YEAR : ".$arrDiff["YEAR"]."<br>";
  echo "HOUR : ".$arrDiff["HOUR"]."<br>";
  echo "MINUTE : ".$arrDiff["MINUTE"]."<br>";
  echo "SECOND : ".$arrDiff["SECOND"]."<br>";
});

//API New
//login server new mobile
Route::get('/login/api', 'LoginController@mobileLogin');
Route::post('/login/api', 'LoginController@mobileLogin');
//api check token
Route::get('/login/checktoken', 'LoginController@mobileCheckToken');
Route::put("/collection/profile/update", "ProfileController@profileUpdateMobile");

Route::get("/server-time", "DashboardController@tesTgl2");

/**
 * Tabungan
 */
Route::get('tabungan', 'TabunganController@index');
Route::post('tabungan/store', 'TabunganController@store');
Route::post('tabungan/update', 'TabunganController@update');
Route::get('tabungan/laporan/view', 'TabunganController@report_view');
Route::get('tabungan/laporan/download', 'TabunganController@report_download');
Route::get('admin/tabungan/laporan/view', 'TabunganController@report_view_admin');
Route::get('admin/tabungan/laporan/download', 'TabunganController@report_download_admin');
Route::get('tabungan/{tgl}', 'TabunganController@show');

/**
 * Marketing
 */
Route::get('marketing', 'MarketingController@index');
Route::post('marketing', 'MarketingController@update');

/**
 * Direksi Monitor
 */
Route::get('direksi', 'DireksiController@index');
Route::match(['get', 'post'], 'direksi/monitor/jadwal/{id}', 'DireksiController@monitor_jadwal');
Route::match(['get', 'post'], 'direksi/monitor/route', 'DireksiController@monitor_route');

Route::group(['prefix' => 'api'], function () {
  Route::post('change', 'ApiController@updatePassword');
  Route::get('list', 'ApiController@list_bayar');
  Route::get('search', 'ApiController@search');
  /**
   * Api Tabungan
   */
  Route::post('tabungan', 'ApiController@tabungan');
  Route::get('tabungan/summary', 'ApiController@summary_tabungan');
  Route::get('tabungan/history', 'ApiController@history');
  /**
   * Api Marketing
   */
  Route::get('marketing', 'MarketingController@api_index');
  Route::get('marketing/search', 'MarketingController@api_search');
  Route::get('marketing/search/{status}', 'MarketingController@api_search_status');
  Route::post('marketing', 'MarketingController@api_store');
  /**
   * Api Direksi
   */
  Route::match(['get', 'post'], 'direksi/monitor', 'DireksiController@api_monitor');
  Route::match(['get', 'post'], 'direksi/monitor/jadwal', 'DireksiController@api_monitor_list');
  Route::match(['get', 'post'], 'direksi/monitor/jadwal/{id}', 'DireksiController@api_monitor_jadwal');
  Route::match(['get', 'post'], 'direksi/monitor/route', 'DireksiController@api_monitor_route');
});

Route::get('test', function () {
    $users = DB::table('coll_user')->selectRaw('U_ID, U_PASSWORD, U_LOGIN_TOKEN')->get();
    return composeReply2('SUCCESS', 'USER', $users);
    return 'test';
    function bulan($bulan) {
        Switch ($bulan){
            case 1 : $bulan="JANUARI";
                Break;
            case 2 : $bulan="FEBRUARI";
                Break;
            case 3 : $bulan="MARET";
                Break;
            case 4 : $bulan="APRIL";
                Break;
            case 5 : $bulan="MEI";
                Break;
            case 6 : $bulan="JUNI";
                Break;
            case 7 : $bulan="JULI";
                Break;
            case 8 : $bulan="AGUSTUS";
                Break;
            case 9 : $bulan="SEPTEMBER";
                Break;
            case 10 : $bulan="OKTOBER";
                Break;
            case 11 : $bulan="NOVEMBER";
                Break;
            case 12 : $bulan="DESEMBER";
                Break;
            }
        return $bulan;
    }
    $userId = Session::get('SESSION_USER_ID', '');
    $userData = DB::table('coll_user')->where('U_ID',$userId)->first();
    $prshData = Input::get("collector");
    $tgl_awal = Input::get("tglAwal");
    $awal = $tAwal = date("Y-m-d", strtotime($tgl_awal));
    $tgl_akhir = Input::get("tglAkhir");
    $akhir = $tAkhir = date("Y-m-d", strtotime($tgl_akhir));
    $bln = date('m', strtotime($tgl_akhir));
    $thn = date('Y', strtotime($tgl_akhir));
    $rptType = 'RPT_COLLECTING_QUERY';
    if($prshData == "ALL") {
        $jadwal = DB::table("coll_batch_upload_data")->join("coll_perusahaan", "coll_batch_upload_data.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->join("coll_jadwal", "coll_batch_upload_data.BUD_ID", "=", "coll_jadwal.BUD_ID")->whereBetween("coll_jadwal.J_TGL", array($tAwal, $tAkhir ))->get();
    } else {
        $jadwal = DB::table("coll_batch_upload_data")->join("coll_perusahaan", "coll_batch_upload_data.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->join("coll_jadwal", "coll_batch_upload_data.BUD_ID", "=", "coll_jadwal.BUD_ID")->whereBetween("coll_jadwal.J_TGL", array($tAwal, $tAkhir ))->where("coll_batch_upload_data.PRSH_ID", $prshData)->get();
    }
    Excel::create('Pintech Mobile App Report', function($excel) use($rptType, $userData, $bln, $thn, $awal, $akhir, $prshData, $tAwal, $tAkhir, $jadwal) {
        $rptName = getReferenceInfo("REPORT_TYPE", $rptType);
        $excel->setTitle('Pintech Mobile App System Report '.$thn."_".$bln);
        $excel->setCreator('Pintech Mobile App System')->setCompany($prshData);
        $excel->setDescription('Laporan hasil collecting');
        $excel->sheet('Sheet 1', function ($sheet) use($rptType, $userData, $bln, $thn, $awal, $akhir, $prshData, $tAwal, $tAkhir, $jadwal) {
            $sheet->setOrientation('landscape');
            $sheet->setCellValue('M1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
            $sheet->getStyle('M1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
            $sheet->setCellValue('A1', "PINTECH MOBILE APP REPORT DARI TANGGAL " . $awal . " -  " .$akhir);
            $sheet->mergeCells('A1:AA1');
            $sheet->getRowDimension('1')->setRowHeight(30);
            $sheet->getColumnDimension('A')->setWidth(50);
            $sheet->setCellValue('A2', "TANGGAL");
            $sheet->getRowDimension('2')->setRowHeight(30);
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->setCellValue('B2', "NAMA COLLECTOR");
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->setCellValue('C2', "KODE GROUP");
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->setCellValue('D2', "CAB");
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->setCellValue('E2', "NO REKENING");
            $sheet->getColumnDimension('E')->setWidth(35);
            $sheet->setCellValue('F2', "ID NASABAH");
            $sheet->getColumnDimension('F')->setWidth(35);
            $sheet->setCellValue('G2', "NAMA NASABAH");
            $sheet->getColumnDimension('G')->setWidth(50);
            $sheet->setCellValue('H2', "ALAMAT");
            $sheet->getColumnDimension('H')->setWidth(70);
            $sheet->setCellValue('I2', "HP");
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->setCellValue('J2', "AGUNAN");
            $sheet->getColumnDimension('J')->setWidth(30);
            $sheet->setCellValue('K2', "JML PINJAMAN");
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->setCellValue('L2', "SALDO NOMINATIF");
            $sheet->getColumnDimension('L')->setWidth(30);
            $sheet->setCellValue('M2', "FP");
            $sheet->getColumnDimension('M')->setWidth(25);
            $sheet->setCellValue('N2', "FB");
            $sheet->getColumnDimension('N')->setWidth(25);
            $sheet->setCellValue('O2', "POKOK/BLN");
            $sheet->getColumnDimension('O')->setWidth(35);
            $sheet->setCellValue('P2', "BUNGA/BLN");
            $sheet->getColumnDimension('P')->setWidth(35);
            $sheet->setCellValue('Q2', "KOLEKTIBILITAS");
            $sheet->getColumnDimension('Q')->setWidth(20);
            $sheet->setCellValue('R2', "ANGSURAN KE");
            $sheet->getColumnDimension('R')->setWidth(38);
            $sheet->setCellValue('S2', "JANGKA WAKTU");
            $sheet->getColumnDimension('S')->setWidth(35);
            $sheet->setCellValue('T2', "TGL REALISASI");
            $sheet->getColumnDimension('T')->setWidth(35);
            $sheet->setCellValue('U2', "TGL UPLOAD");
            $sheet->getColumnDimension('U')->setWidth(35);
            $sheet->setCellValue('V2', "TGL JADWAL");
            $sheet->getColumnDimension('V')->setWidth(35);
            $sheet->setCellValue('W2', "TUNGG POKOK");
            $sheet->getColumnDimension('W')->setWidth(40);
            $sheet->setCellValue('X2', "TUNGG BUNGA");
            $sheet->getColumnDimension('X')->setWidth(40);
            $sheet->setCellValue('Y2', "TUNGG DENDA");
            $sheet->getColumnDimension('Y')->setWidth(40);
            $sheet->setCellValue('Z2', "TAGIHAN");
            $sheet->getColumnDimension('Z')->setWidth(40);
            $sheet->setCellValue('AA2', "STATUS");
            $sheet->getColumnDimension('AA')->setWidth(40);
            $sheet->setCellValue('AB2', "KETERANGAN");
            $sheet->getColumnDimension('AB')->setWidth(35);
            $sheet->setCellValue('AC2', "BAYAR POKOK");
            $sheet->getColumnDimension('AC')->setWidth(35);
            $sheet->setCellValue('AD2', "BAYAR BUNGA");
            $sheet->getColumnDimension('AD')->setWidth(35);
            $sheet->setCellValue('AE2', "BAYAR DENDA");
            $sheet->getColumnDimension('AE')->setWidth(35);
            $sheet->setCellValue('AF2', "TOTAL BAYAR");
            $sheet->getColumnDimension('AF')->setWidth(35);
            $sheet->setCellValue('AG2', "TANGGAL BAYAR");
            $sheet->getColumnDimension('AG')->setWidth(35);
            $sheet->setCellValue('AH2', "JAM BAYAR");
            $sheet->getColumnDimension('AH')->setWidth(35);
            $sheet->getStyle('A2:AI2')->getFont()->setBold(true);
            $grandTotalBayar = 0;
            $row = 3;
            $tgl = '';
            foreach ($jadwal as $key => $value) {
                if ($tgl == date('Y-m-d', strtotime($value->{'J_TGL'}))) {
                    $sheet->setCellValue('A' . $row, '');
                } else {
                    $sheet->setCellValue('A' . $row, tglIndo(date('Y-m-d', strtotime($value->{'J_TGL'})), "SHORT"));
                    $sheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                }
                $tgl = date('Y-m-d', strtotime($value->{'J_TGL'}));

                $sheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('B' . $row, $value->{"J_COLL_U_ID"});
                $sheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('C' . $row, $value->{"BUD_KODE_GROUP"});
                $sheet->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('D' . $row, $value->{"BUD_CAB"});
                $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('E' . $row, $value->{"BUD_PINJ_ID"});
                $sheet->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('F' . $row, $value->{"BUD_CUST_ID"});
                $sheet->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('G' . $row, $value->{"BUD_CUST_NAMA"});
                $sheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('H' . $row, $value->{"BUD_CUST_ALAMAT"});
                $sheet->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('I' . $row, $value->{"BUD_CUST_PONSEL"});
                $sheet->getStyle('J')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('J' . $row, $value->{"BUD_AGUNAN"});
                $sheet->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('K' . $row, $value->{"BUD_JML_PINJAMAN"});
                $sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('L' . $row, date('d-m-Y', strtotime($value->{"BUD_JML_PINJAMAN"})));
                $sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('M' . $row, $value->{"BUD_FP"});
                $sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('N' . $row, $value->{"BUD_FB"});
                $sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('O' . $row, $value->{"BUD_BLN_POKOK"});
                $sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('P' . $row, $value->{"BUD_BLN_BUNGA"});
                $sheet->getStyle('Q')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Q' . $row, $value->{"BUD_KOLEKTIBILITAS"});
                $sheet->getStyle('R')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('R' . $row, $value->{"BUD_PINJ_PERIODE"});
                $sheet->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('S' . $row, $value->{"BUD_PINJ_MASA_KREDIT"});
                $sheet->getStyle('T')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('T' . $row, $value->{"BUD_PINJ_TGL_KREDIT"});
                $sheet->getStyle('U')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('U' . $row, $value->{"BUD_PINJ_TGL_JADWAL"});
                $sheet->getStyle('V')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('V' . $row, $value->{"BUD_TGL_DEPAN_JADWAL"});
                $sheet->getStyle('W')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('W' . $row, $value->{"BUD_PINJ_POKOK"});
                $sheet->getStyle('X')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Y' . $row, $value->{"BUD_PINJ_BUNGA"});
                $sheet->getStyle('Y')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Z' . $row, $value->{"BUD_PINJ_DENDA"});
                $sheet->getStyle('Z')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AA' . $row, $value->{"BUD_PINJ_JUMLAH"});
                $sheet->getStyle('AA')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                if($value->{"J_STATUS"} == "ST_BAYAR" || $value->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
                    $sheet->setCellValue('AB' . $row, tglIndo($value->{"BUD_PINJ_TGL_BAYAR"}, "SHORT"));
                $sheet->getStyle('AB')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                } else {
                    $sheet->setCellValue('AB'.$row, "-");
                }
                $sheet->setCellValue('AB'.$row, getReferenceInfo("STATUS_COLLECTION",$value->{"J_STATUS"}));
                $sheet->setCellValue('AC'.$row, $value->{"BUD_KETERANGAN"});
                $sheet->setCellValue('AD'.$row, $value->{"BUD_EDIT_POKOK"});
                $sheet->getStyle('AD')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AE'.$row, $value->{"BUD_EDIT_BUNGA"});
                $sheet->getStyle('AE')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AF'.$row, $value->{"BUD_EDIT_DENDA"});
                $sheet->getStyle('AF')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AG'.$row, $value->{"J_PINJ_JUMLAH_BAYAR"});
                $sheet->getStyle('AG')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AH'.$row, tglIndo($value->{"BUD_PINJ_TGL_BAYAR"}, "LONG"));
                $sheet->setCellValue('AI'.$row, date("H:i:s", strtotime($value->{"BUD_PINJ_TGL_BAYAR"})));
                $row++;
            }
            $sheet->mergeCells('A'.$row.':AF'.$row);

          $sheet->getStyle("A".$row.":B".$row)->applyFromArray(array(
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
          ));

          $sheet->setCellValue('A'.$row, " T  O  T  A  L ");
          $sheet->getStyle('A'.$row.':AF'.$row)->getFont()->setBold(true);
          $sheet->setCellValue('AG'.$row, $grandTotalBayar);

          // Set style for header row using alternative method

           $sheet->getStyle('A1:AI1')->applyFromArray(
            array(
              'font'    => array(
                'bold'      => true,
                'size'=>12
              ),
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
              )
              )
          );

          $sheet->getStyle('A2:AI2')->applyFromArray(
            array(
              'font'    => array(
                'bold'      => true
              ),
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
              ),
              'borders' => array(
                'top'     => array(
                  'style' => PHPExcel_Style_Border::BORDER_THICK
                ),
                'bottom'     => array(
                  'style' => PHPExcel_Style_Border::BORDER_THICK
                )
              ),
              'fill' => array(
                'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startcolor' => array(
                  'argb' => 'FFA0A0A0'
                ),
                'endcolor'   => array(
                  'argb' => 'FFFFFFFF'
                )
              )
            )
          );

          $sheet->getStyle('A2')->applyFromArray(
            array(
              'borders' => array(
                'left'     => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
            )
          );

          $sheet->getStyle('AI2')->applyFromArray(
            array(
              'borders' => array(
                'right'     => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
            )
          );
        });
        $excel->setActiveSheetIndex(0);
    })->download('xlsx');
});
