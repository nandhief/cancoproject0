<?php
class ProfileController extends BaseController {
	public function redirectMissing() {
		Session::flush();
		return Redirect::to('login');
	}

	public function formProfile() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return composeReply('ERROR','Silahkan login dahulu');
			}

			$userId = Session::get('SESSION_USER_ID', '');			
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			return View::make("dashboard.profile.profile")	
				->with("ctlUserData",$userData)
				->with("ctlNavMenu","mProfil");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Silahkan login terlebih dahulu');
		}
	}

	public function updateProfile() {
		if(null === Input::get("mobile") || trim(Input::get("mobile")) === "") {
			$accessMode = "WEB";
		}
		else {
			$accessMode = "MOBILE";
		}

		if($accessMode == "WEB") {
			if (!Session::has('SESSION_USER_ID') || !Session::has('SESSION_LOGIN_TOKEN')) {
				Session::flush();
				return composeReply("ERROR","Silahkan login terlebih dahulu");
			}

			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return composeReply('ERROR','Silahkan login dahulu');
			}

			$userId = Session::get('SESSION_USER_ID', '');
		}
		if($accessMode == "MOBILE") {
			if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID");
	    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token");
	    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token");

	    $userId = Input::get("userId");
		}

		$userData = DB::table('coll_user')->where('U_ID',$userId)->first();
		if(count($userData) <= 0)	{
			if($accessMode == "WEB")		return composeReply("ERROR","User tidak dikenal");
			if($accessMode == "MOBILE")	return composeReply2("ERROR","User tidak dikenal");
		}

		if(null === Input::get("nama") || trim(Input::get("nama")) == "") {
			if($accessMode == "WEB")		return composeReply("ERROR","Nama harus diisi");
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Nama harus diisi");
		}
		$nama = Input::get("nama");

		if(null === Input::get("email") || trim(Input::get("email")) == "") {
			if($accessMode == "WEB")		return composeReply("ERROR","Email harus diisi");
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Email harus diisi");
		}
		if($userData->{"U_EMAIL"} != trim(Input::get("email"))) { //ada perubahan email
			$cek = DB::select("SELECT IFNULL(COUNT(U_ID),0) AS JUMLAH FROM coll_user WHERE U_EMAIL = ?", array(Input::get("email")));
			if($cek[0]->{"JUMLAH"} > 0)	{
				if($accessMode == "WEB")		return composeReply("ERROR","Email sudah digunakan");
				if($accessMode == "MOBILE")	return composeReply2("ERROR","Email sudah digunakan");
			}
		}
		$email = Input::get("email");

		if(null === Input::get("ponsel") || trim(Input::get("ponsel")) == "") {
			if($accessMode == "WEB")		return composeReply("ERROR","Nomor telepon/ponsel harus diisi");
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Nomor telepon/ponsel harus diisi");
		}
		if($userData->{"U_TELPON"} != formatPonsel(trim(Input::get("ponsel")),"0")) { //ada perubahan ponsel
			$cek = DB::select("SELECT IFNULL(COUNT(U_ID),0) AS JUMLAH FROM coll_user WHERE U_TELPON = ?", array(formatPonsel(trim(Input::get("ponsel")),"0")));
			if($cek[0]->{"JUMLAH"} > 0)	{
				if($accessMode == "WEB")		return composeReply("ERROR","Nomor telepon sudah digunakan");
				if($accessMode == "MOBILE")	return composeReply2("ERROR","Nomor telepon sudah digunakan");
			}
		}
		$ponsel = formatPonsel(trim(Input::get("ponsel")),"0");

		if(null === Input::get("passwordLama") || trim(Input::get("passwordLama")) == "") {
			if($accessMode == "WEB")		return composeReply("ERROR","Untuk melakukan perubahan, password lama harus diisi");
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Untuk melakukan perubahan, password lama harus diisi");
		}
		$passwordLama = Input::get("passwordLama");

		$passwordBaru = Input::get("passwordBaru");
		$passwordKonfirmasi = Input::get("passwordKonfirmasi");
			
		if(md5($userId.$passwordLama) == $userData->{"U_PASSWORD_HASH"}) {
			DB::update("UPDATE coll_user SET U_NAMA = ?, U_EMAIL = ?, U_TELPON = ? WHERE U_ID = ?",array($nama, $email, $ponsel, $userId));

			if(null !== $passwordBaru && trim($passwordBaru) != "") {
				if(null !== $passwordKonfirmasi && trim($passwordKonfirmasi) != "") {
					if($passwordBaru === $passwordKonfirmasi) {
						DB::update("UPDATE coll_user SET U_PASSWORD_HASH = ?, U_PASSWORD = ? WHERE U_ID = ?",array(
							md5($userId.$passwordBaru),
							$passwordBaru,
							$userId
						));
					}
					else {
						if($accessMode == "WEB") 		return composeReply("ERROR", "Untuk mengganti password lama dengan yang baru, isikan password konfirmasi dengan nilai yang sama dengan password baru yang dikehendaki");
						if($accessMode == "MOBILE") return composeReply2("ERROR", "Untuk mengganti password lama dengan yang baru, isikan password konfirmasi dengan nilai yang sama dengan password baru yang dikehendaki");
					}
				}
				else {
					if($accessMode == "WEB") 		return composeReply("ERROR", "Untuk mengganti password lama dengan yang baru, isikan password konfirmasi dengan nilai yang sama dengan password baru yang dikehendaki");
					if($accessMode == "MOBILE") return composeReply2("ERROR", "Untuk mengganti password lama dengan yang baru, isikan password konfirmasi dengan nilai yang sama dengan password baru yang dikehendaki");
				}
			}
			
			if($accessMode == "WEB") 		return composeReply("SUCCESS","Perubahan data telah disimpan");
			if($accessMode == "MOBILE") return composeReply2("SUCCESS","Perubahan data telah disimpan", array(
				'PROFILE_USER_NAME' => $nama,
				'PROFILE_EMAIL' => $email,
				'PROFILE_PHONE' => $ponsel
			));					
		}
		else {
			if($accessMode == "WEB")		return composeReply("ERROR","Untuk melakukan perubahan data, Anda harus memasukkan password lama dengan benar");
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Untuk melakukan perubahan data, Anda harus memasukkan password lama dengan benar");
		}
	}

	public function profileUpdateMobile() {
		if($accessMode == "MOBILE") {
			if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID");
	    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token");
	    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token");

	    $userId = Input::get("userId");
		}

		$userData = DB::table('coll_user')->where('U_ID',$userId)->first();
		if(count($userData) <= 0)	{
			if($accessMode == "MOBILE")	return composeReply2("ERROR","User tidak dikenal");
		}

		if(null === Input::get("nama") || trim(Input::get("nama")) == "") {
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Nama harus diisi");
		}
		$nama = Input::get("nama");

		if(null === Input::get("email") || trim(Input::get("email")) == "") {
			if($accessMode == "MOBILE")	return composeReply2("ERROR","Email harus diisi");
		}
		if($userData->{"U_EMAIL"} != trim(Input::get("email"))) { //ada perubahan email
			$cek = DB::select("SELECT IFNULL(COUNT(U_ID),0) AS JUMLAH FROM coll_user WHERE U_EMAIL = ?", array(Input::get("email")));
			if($cek[0]->{"JUMLAH"} > 0)	{
				return composeReply2("ERROR","Email sudah digunakan");
			}
		}
		$email = Input::get("email");

		if(null === Input::get("ponsel") || trim(Input::get("ponsel")) == "") {
			return composeReply2("ERROR","Nomor telepon/ponsel harus diisi");
		}
		if($userData->{"U_TELPON"} != formatPonsel(trim(Input::get("ponsel")),"0")) { //ada perubahan ponsel
			$cek = DB::select("SELECT IFNULL(COUNT(U_ID),0) AS JUMLAH FROM coll_user WHERE U_TELPON = ?", array(formatPonsel(trim(Input::get("ponsel")),"0")));
			if($cek[0]->{"JUMLAH"} > 0)	{
					return composeReply2("ERROR","Nomor telepon sudah digunakan");
			}
		}
		$ponsel = formatPonsel(trim(Input::get("ponsel")),"0");

		if(null === Input::get("passwordLama") || trim(Input::get("passwordLama")) == "") {
			return composeReply2("ERROR","Untuk melakukan perubahan, password lama harus diisi");
		}
		$passwordLama = Input::get("passwordLama");

		$passwordBaru = Input::get("passwordBaru");
		$passwordKonfirmasi = Input::get("passwordKonfirmasi");
			
		if(md5($userId.$passwordLama) == $userData->{"U_PASSWORD_HASH"}) {
			DB::update("UPDATE coll_user SET U_NAMA = ?, U_EMAIL = ?, U_TELPON = ? WHERE U_ID = ?",array($nama, $email, $ponsel, $userId));

			if(null !== $passwordBaru && trim($passwordBaru) != "") {
				if(null !== $passwordKonfirmasi && trim($passwordKonfirmasi) != "") {
					if($passwordBaru === $passwordKonfirmasi) {
						DB::update("UPDATE coll_user SET U_PASSWORD_HASH = ?, U_PASSWORD = ? WHERE U_ID = ?",array(
							md5($userId.$passwordBaru),
							$passwordBaru,
							$userId
						));
					}
					else {
						 return composeReply2("ERROR", "Untuk mengganti password lama dengan yang baru, isikan password konfirmasi dengan nilai yang sama dengan password baru yang dikehendaki");
					}
				}
				else {
					 return composeReply2("ERROR", "Untuk mengganti password lama dengan yang baru, isikan password konfirmasi dengan nilai yang sama dengan password baru yang dikehendaki");
				}
			}
			
			if($accessMode == "MOBILE") return composeReply2("SUCCESS","Perubahan data telah disimpan", array(
				'PROFILE_USER_NAME' => $nama,
				'PROFILE_EMAIL' => $email,
				'PROFILE_PHONE' => $ponsel
			));					
		}
		else {
			return composeReply2("ERROR","Untuk melakukan perubahan data, Anda harus memasukkan password lama dengan benar");
		}

	}
}
?>