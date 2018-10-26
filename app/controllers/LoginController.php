<?php
class LoginController extends BaseController {
	public function showLogin() {
		return View::make("login.login-page");
	}

	public function processLogin() {
		if(null === Input::get("mobile") || trim(Input::get("mobile")) === "") {
			$accessMode = "WEB";
		}
		else {
			$accessMode = "MOBILE";
		}


		if(null === Input::get("loginEmail") || trim(Input::get("loginEmail")) === "") {
			if($accessMode == "WEB") {
				return Redirect::to('login')->with('ctlError','Periksa kembali data login Anda');
			}
			else {
				return composeReply("ERROR", "Periksa kembali data login Anda");
			}
		}

		$loginEmail = Input::get("loginEmail");
		$loginPassword = Input::get("loginPassword");
		//Log::info('loginEmail : '.$loginEmail.' - loginPassword : '.$loginPassword);
		// select query dengan parameter
		$errMsg = "";
		$users = DB::select("SELECT * FROM coll_user WHERE (U_ID = ? OR U_EMAIL = ? OR U_TELPON = ?) AND U_STATUS = 'USER_ACTIVE'", array($loginEmail, $loginEmail, $loginEmail));
		if(count($users) > 0) {	
			$userId = $users[0]->{"U_ID"};
			if($users[0]->{"U_PASSWORD_HASH"} !== md5($userId.$loginPassword))	$errMsg = "Periksa kembali data login Anda";

			if($users[0]->{"PRSH_ID"} != "-") {
				$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", $users[0]->{"PRSH_ID"})->first();
				if(count($prshData) <= 0)										$errMsg = "Perusahaan user tidak dikenal";
				if($prshData->{"PRSH_STATUS_AKTIF"} != "Y")	$errMsg = "Perusahaan user tidak aktif"; 
			}	

			if($errMsg == "") {
				$loginToken = substr(md5($users[0]->{"U_NAMA"}.date("Y-m-d H:i:s")), 0,30);
				DB::table("coll_user")->where("U_ID",$users[0]->{"U_ID"})->update(array(
					'U_LOGIN_TOKEN' => $loginToken,
					'U_LOGIN_WAKTU' => date("Y-m-d H:i:s")
				));

				if($accessMode == "WEB") {
					Session::put('SESSION_USER_NAME', $users[0]->{"U_NAMA"});
					Session::put('SESSION_USER_ID', $users[0]->{"U_ID"});
					Session::put('SESSION_USER_ROLE', $users[0]->{"U_GROUP_ROLE"});
					Session::put('SESSION_LOGIN_TOKEN', $loginToken);
					if(isset($prshData)) {
						Session::put('SESSION_COMPANY_NAME', $prshData->{"PRSH_NAMA"});
						Session::put('SESSION_COMPANY_ID', $users[0]->{"PRSH_ID"});
						Session::put('SESSION_COMPANY_ADDRESS', $prshData->{"PRSH_ALAMAT"});
						Session::put('SESSION_COMPANY_CITY', $prshData->{"PRSH_KOTA"});
					}
					
					return Redirect::to("dashboard")	
						->with("ctlLogin",$users[0]->{"U_ID"})
						->with("ctlUserName",$users[0]->{"U_NAMA"});
				}
				else {
					return composeReply2("SUCCESS", "Login sukses", array(
						'LOGIN_TOKEN' => $loginToken,
						'LOGIN_USER_NAME' => $users[0]->{"U_NAMA"},
						'LOGIN_USER_ID' => $users[0]->{"U_ID"},
						'LOGIN_USER_ROLE' => $users[0]->{"U_GROUP_ROLE"},
						'LOGIN_COMPANY_NAME' => $prshData->{"PRSH_NAMA"},
						'LOGIN_COMPANY_ID' => $users[0]->{"PRSH_ID"},
						'LOGIN_EMAIL' => $users[0]->{"U_EMAIL"},
						'LOGIN_PHONE' => $users[0]->{"U_TELPON"},
						'LOGIN_IDNOTA' => $users[0]->{"U_NOTA_ID"}
					));
				}
			}
			else {
				if($accessMode == "WEB") {
					//automatically set session named ctlError
					return Redirect::to('login')->with('ctlError', $errMsg);
				}
				else {
					return composeReply("ERROR", $errMsg);
				}
			}
		}
		else {
			if($accessMode == "WEB") {
				//automatically set session named ctlError
				return Redirect::to('login')->with('ctlError','Akun salah atau akun tidak aktif');
			}
			else {
				return composeReply("ERROR", "Akun salah atau akun tidak aktif");
			}
		}
	}

	public function processLogout() {
		Session::flush();
		return Redirect::to('login');
	}

	public function checkToken() {
		if(null === Input::get("userId") || trim(Input::get("userId")) === "")					return composeReply("ERROR", "Invalid user ID");
		if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")	return composeReply("ERROR", "Invalid login token");
		if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))								return composeReply("ERROR", "Invalid login token");
		
		$userId = Input::get("userId");
		$logToken = Input::get("loginToken");

		// $userData = DB::table("coll_user")
	 //    ->where("U_ID", Input::get("userId"))
	 //    ->where("U_LOGIN_TOKEN", Input::get("loginToken"))
	 //    ->first();
	  $userData = DB::select("SELECT a.*, b.PRSH_ID, b.PRSH_NAMA FROM coll_user AS a INNER JOIN coll_perusahaan AS b ON a.PRSH_ID = b.PRSH_ID WHERE a.U_ID = '$userId' AND a.U_LOGIN_TOKEN = '$logToken' AND a.U_GROUP_ROLE = 'GR_COLLECTOR' AND a.U_STATUS = 'USER_ACTIVE'");

	  if(count($userData) <= 0) return composeReply("ERROR", "Invalid #1");

	  //$arrDiff = array();
	  //$arrDiff = dayDifference2(date("Y-m-d H:i:s"),$userData->{"U_LOGIN_WAKTU"},TRUE);
	  
		//return composeReply("SUCCESS", "Days since last login : ".$arrDiff["DAY"]);
	    $now = date("Y-m-d H:i:s");
	    // $loginToken = substr(md5($userData[0]->{"U_NAMA"}.$now), 0,30);
		if(isLoginValid(Input::get("userId"), Input::get("loginToken"))) {
			return composeReply("SUCCESS", "Login VALID", array(
				//tampilkan di array
				'collector_data' => $userData

			    // "collector_id" => $userData['U_ID'],
    		 //    "collector_nama" => $userData['U_NAMA'],
    			// "collector_rule" => $userData["U_GROUP_ROLE"],
    			// "collector_telpon" => $userData["U_TELPON"],
    			// "collector_email" => $userData["U_EMAIL"],
    			// "collector_login_waktu" => $userData["U_LOGIN_WAKTU"],
    			// "collector_token" => $userData["U_LOGIN_TOKEN"],
    			// "collector_prsh_id" => $userData["PRSH_ID"],
       //          // "collector_prsh_nama" = $userData["PRSH_NAMA"];
			));

		}
		else {
			return composeReply("ERROR", "Login EXPIRED");
		}
	}

	public function mobileLogin() {
		$logEmail = Input::get("loginEmail");
		$logPassword = Input::get("loginPassword");
		//Log::info('loginEmail : '.$loginEmail.' - loginPassword : '.$loginPassword);
		// select query dengan parameter
		$errMsg = "";
		$users = DB::select("SELECT A.*, B.* FROM coll_user AS A INNER JOIN coll_perusahaan AS B ON A.PRSH_ID = B.PRSH_ID WHERE (A.U_ID = ? OR A.U_EMAIL = ? OR A.U_TELPON = ?) AND A.U_STATUS = 'USER_ACTIVE' AND A.U_GROUP_ROLE = 'GR_COLLECTOR'", array($logEmail, $logEmail, $logEmail));
		if(count($users) > 0) {	
			$userId = $users[0]->{"U_ID"};
			if($users[0]->{"U_PASSWORD_HASH"} !== md5($userId.$logPassword))	$errMsg = "Periksa kembali data login Anda";

			if($users[0]->{"PRSH_ID"} != "-") {
				$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", $users[0]->{"PRSH_ID"})->first();
				if(count($prshData) <= 0)										$errMsg = "Perusahaan user tidak dikenal";
				if($prshData->{"PRSH_STATUS_AKTIF"} != "Y")	$errMsg = "Perusahaan user tidak aktif"; 
			}	

			if($errMsg == "") {
				$loginToken = substr(md5($users[0]->{"U_NAMA"}.date("Y-m-d H:i:s")), 0,30);
				DB::table("coll_user")->where("U_ID",$users[0]->{"U_ID"})->update(array(
					'U_LOGIN_TOKEN' => $loginToken,
					'U_LOGIN_WAKTU' => date("Y-m-d H:i:s")
				));

				return composeReply2("SUCCESS", "Login sukses", array(
					'collector_login_token' => $loginToken,
					'collector_big_id' => $users[0]->{"USERBIGID"},
					'collector_nama' => $users[0]->{"U_NAMA"},
					'collector_id' => $users[0]->{"U_ID"},
					'collector_rule' => $users[0]->{"U_GROUP_ROLE"},
					'collector_prsh_nama' => $prshData->{"PRSH_NAMA"},
					'collector_prsh_alamat' => $prshData->{"PRSH_ALAMAT"},
					'collector_prsh_id' => $users[0]->{"PRSH_ID"},
					'collector_email' => $users[0]->{"U_EMAIL"},
					'collector_telpon' => $users[0]->{"U_TELPON"},
					'collector_login_waktu' => $users[0]->{"U_LOGIN_WAKTU"},
					'collector_nota_id' => $users[0]->{"U_NOTA_ID"},
					'collector_path_prsh' => asset_url()."/".$users[0]->{"PRSH_IMG_PATH"},
					'collector_status_collect' => $users[0]->{"U_STATUS_COLLECT"},
					'collector_status_tab' => $users[0]->{"U_STATUS_TAB"},
					'collector_ganti_pass' => $users[0]->{"U_GANTIPASS"},
				));
			}
			else {
				
				return composeReply("ERROR", $errMsg);

			}
		} else {
			return composeReply("ERROR", "Parameter yang anda  masukkan salah");
		}
	}

	public function mobileCheckToken() {
	    if(null === Input::get("userId") || trim(Input::get("userId")) === "")					return composeReply("ERROR", "Invalid user ID");
		if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")	return composeReply("ERROR", "Invalid login token");
		if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))								return composeReply("ERROR", "Invalid login token");
		
		$userData = DB::table("coll_user")
	    ->where("U_ID", Input::get("userId"))
	    ->where("U_LOGIN_TOKEN", Input::get("loginToken"))
	    ->first();

	  if(count($userData) <= 0) return composeReply("ERROR", "Invalid #1");

	  //$arrDiff = array();
	  //$arrDiff = dayDifference2(date("Y-m-d H:i:s"),$userData->{"U_LOGIN_WAKTU"},TRUE);
	  
		//return composeReply("SUCCESS", "Days since last login : ".$arrDiff["DAY"]);
		if(isLoginValid(Input::get("userId"), Input::get("loginToken"))) {
			return composeReply("SUCCESS", "Login VALID");
		}
		else {
			return composeReply("ERROR", "Login EXPIRED,Token tidak sama");
		}	
	}

}
?>