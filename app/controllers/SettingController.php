<?php
class SettingController extends BaseController {
	public function redirectMissing() {
		Session::flush();
		return Redirect::to('login');
	}

	public function formSettings() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
        return Redirect::to('login')->with('ctlError','Silahkan login terlebih dahulu');
			}

			$userId = Session::get('SESSION_USER_ID', '');			
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", $userData->{"PRSH_ID"})->first();

			return View::make("dashboard.settings.settings")	
				->with("ctlUserData",$userData)
				->with("ctlPrshData", $prshData)
				->with("ctlNavMenu","mSettings");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Silahkan login terlebih dahulu');
		}
	}
	
	public function updateSettings() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return composeReply("ERROR","Harap login terlebih dahulu"); 
      }

      $userId = Session::get('SESSION_USER_ID', '');      
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(isset($_FILES['imgFile'])){
        $fileName = $_FILES['imgFile']['name'];
        $fileSize = $_FILES['imgFile']['size'];
        $fileTmp = $_FILES['imgFile']['tmp_name'];
        $fileType = $_FILES['imgFile']['type'];
        $a = explode(".", $_FILES["imgFile"]["name"]);
        $fileExt = strtolower(end($a));

        $arrFileExt = array("jpg","jpeg","png","JPG","JPEG","PNG");
        if(isset($fileName) && trim($fileName) != "") {
          if(in_array($fileExt,$arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file JPG atau PNG");
          if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file JPG atau PNG dengan ukuran max. 2 MB");
          
          $uploadFile = "uploads/logo-".substr(md5(date("YmdHis")),0,10).".".$fileExt;
          if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {    
            DB::table("coll_perusahaan")->where("PRSH_ID", $userData->{"PRSH_ID"})->update(array(
            	'PRSH_IMG_PATH' => $uploadFile
            ));
            return composeReply("SUCCESS", "Data telah disimpan");
          }         
        }
        else {
          return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
        }
      }
      else {
        return composeReply("ERROR","Harap sertakan file untuk diunggah");
      }
    }
    else {
      Session::flush();
      return composeReply("ERROR","Silahkan login terlebih dahulu");
    }
	}

  public function formAdmSettings() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN') && Session::has('SESSION_USER_ROLE')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Silahkan login terlebih dahulu');
      }

      if(Session::get('SESSION_USER_ROLE') != "GR_ADMINISTRATOR") {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Silahkan login terlebih dahulu untuk akses sistem');
      }

      $userId = Session::get('SESSION_USER_ID', '');      
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      $admSettings = DB::table("coll_settings")->get();

      return View::make("dashboard.settings.adm-settings")  
        ->with("ctlUserData",$userData)
        ->with("ctlAdmSettings", $admSettings)
        ->with("ctlNavMenu","mAdmSettings");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Silahkan login terlebih dahulu');
    }
  }

  public function updateAdmSettings() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN') && Session::has('SESSION_USER_ROLE')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return composeReply('ERROR','Silahkan login dahulu');
      }

      if(Session::get('SESSION_USER_ROLE') != "GR_ADMINISTRATOR") {
        Session::flush();
        return composeReply('ERROR','Maaf, Anda tidak berhak akses halaman ini');
      }

      $userId = Session::get('SESSION_USER_ID', '');      
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null !== Input::get("appName") && trim(Input::get("appName")) !== "") {
        DB::table("coll_settings")
          ->where("SET_ID", "APP_NAME")
          ->update(array("SET_VALUE" => Input::get("appName")));
      }

      if(null !== Input::get("companyPrefix") && trim(Input::get("companyPrefix")) !== "") {
        DB::table("coll_settings")
          ->where("SET_ID", "COMPANY_PREFIX")
          ->update(array("SET_VALUE" => Input::get("companyPrefix")));
      }

      if(null !== Input::get("googleMapsAPIKey") && trim(Input::get("googleMapsAPIKey")) !== "") {
        DB::table("coll_settings")
          ->where("SET_ID", "GOOGLE_MAPS_API_KEY")
          ->update(array("SET_VALUE" => Input::get("googleMapsAPIKey")));
      }

      if(null !== Input::get("intervalMonitoring") && trim(Input::get("intervalMonitoring")) !== "") {
        if(!is_numeric(Input::get("intervalMonitoring"))) {
          return composeReply("ERROR", "Isikan bilangan untuk jeda monitoring lokasi collector");
        }
        else {
          if(floatval(Input::get("intervalMonitoring")) <= 29999) return composeReply("ERROR", "Jeda monitoring minimal 30.000 milidetik");
        }

        DB::table("coll_settings")
          ->where("SET_ID", "MONITORING_INTERVAL_MS")
          ->update(array("SET_VALUE" => Input::get("intervalMonitoring")));
      }

      return composeReply("SUCCESS", "Perubahan data yang valid telah disimpan");
    }
    else {
      Session::flush();
      return composeReply("ERROR","Silahkan login terlebih dahulu");
    }
  }

}
?>