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
//update profil new mobile
//Route::get("/collection/profile/update", "ProfileController@profileMobile");
Route::put("/collection/profile/update", "ProfileController@profileUpdateMobile");

//Route::get("/server-time", function() {
//  echo tglIndo(date("Y-m-d H:i:s"), "SHORT");
//});
Route::get("/server-time", "DashboardController@tesTgl2");

Route::get('/pdf', function(){
  $fpdf = new Fpdf();
  $fpdf->AddPage();
  $fpdf->SetFont('Arial','B',16);
  $fpdf->Cell(40,10,'Hello World!');
  $fpdf->Output();
  exit;

  //Fpdf::AddPage();
  //Fpdf::SetFont('Arial','B',16);
  //Fpdf::Cell(40,10,'Hello World!');
  //Fpdf::Output();
  //exit;
});
/*
App::missing(function($exception) {
  //return Response::view('errors.missing', array(), 404);
  //return "Missing configuration";

  //$url = Request::fullUrl();
  //$userAgent = Request::header('user-agent');
  //Log::warning("404 for URL: $url requested by user agent: $userAgent");
  //return Response::view('errors.not-found', array(), 404);

  Session::flush();
  return Redirect::to('login');
});
*/

Route::group(['prefix' => 'api'], function () {
  Route::post('change', 'ApiController@updatePassword');
});
?>
