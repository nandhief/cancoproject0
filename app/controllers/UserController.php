<?php
class UserController extends BaseController {
	public function redirectMissing() {
		Session::flush();
		return Redirect::to('login');
	}

	public function listUsers() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			//get id user
			$userAll = DB::table("coll_user")->get();

			$jml = (count($userAll)) + 1;
			if($jml < 10)	$jml = "11754664738267800".$jml;
			if($jml > 10 && $jml < 100)	$jml = "1175466473826780".$jml;

			$refGroupRole = DB::table("coll_referensi")->where("R_KATEGORI","GROUP_ROLE")->get();

			$appUsers = DB::select("SELECT A.*,B.R_INFO,IFNULL(C.PRSH_NAMA,'-') AS PRSH_NAMA FROM coll_user AS A INNER JOIN coll_referensi AS B ON A.U_GROUP_ROLE = B.R_ID LEFT JOIN coll_perusahaan AS C ON A.PRSH_ID = C.PRSH_ID WHERE A.U_ID != ? AND A.U_GROUP_ROLE != 'GR_ADMINISTRATOR' OR A.U_SUPERUSER != 'S_USER' AND B.R_KATEGORI = 'GROUP_ROLE'", array($userId));

			$prsh = DB::table("coll_perusahaan")->get();

			return View::make("dashboard.user-mgmt.user-formlist")
				->with("ctlUserData", $userData)
				->with("ctlRefGroupRole", $refGroupRole)
				->with("ctlAppUsers", $appUsers)
				->with("ctlPrsh", $prsh)
				->with("ctrlUrutan", $jml)
				->with("ctlNavMenu", "mUserMgmt");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function listAdmin() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			$userAll = DB::table("coll_user")->where('U_GROUP_ROLE', 'GR_ADMINISTRATOR')->get();

			$jml = (count($userAll)) + 1;
			if($jml < 10)	$jml = "11754664738267800".$jml;
			if($jml > 10 && $jml < 100)	$jml = "1175466473826780".$jml;

			$refGroupRole = DB::table("coll_referensi")->where("R_KATEGORI","GROUP_ROLE")->get();

			$appUsers = DB::select("SELECT A.*,B.R_INFO,IFNULL(C.PRSH_NAMA,'-') AS PRSH_NAMA FROM coll_user AS A INNER JOIN coll_referensi AS B ON A.U_GROUP_ROLE = B.R_ID LEFT JOIN coll_perusahaan AS C ON A.PRSH_ID = C.PRSH_ID WHERE A.U_ID != ? AND B.R_KATEGORI = 'GROUP_ROLE'", array($userId));

			$prsh = DB::table("coll_perusahaan")->get();

			return View::make("dashboard.user-mgmt.admin-formlist")
				->with("ctlUserData", $userData)
				->with("ctlRefGroupRole", $refGroupRole)
				->with("ctlAppUsers", $appUsers)
				->with("ctrlUrutan", $jml)
				->with("ctlPrsh", $prsh)
				->with("ctlNavMenu", "mUserMgmt");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function addAdmin() {
		if(Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError', 'Silahkan Login terlebih dahulu');
			}

		$userId = Session::get('SESSION_USER_ID', '');

		if(null === Input::get("userId") || trim(Input::get("userId")) === "") return composeReply("ERROR", "User ID harus diisi");
		$userId = preg_replace('/\s+/', '', Input::get("userId"));

		if(null === Input::get("userEmail") ||  trim(Input::get("userEmail")) === "") return composeReply("ERROR", "Email harus diisi");
		$userEmail = preg_replace('/\s+/', '', Input::get("userEmail"));

		if(!is_numeric(Input::get("userPonsel")))	return composeReply("ERROR", "Isikan ponsel dengan format 08xxx");
		$userPonsel = formatPonsel(preg_replace('/\s+/', '', Input::get("userPonsel")), "0");

        if(null === Input::get("userPrsh") || trim(Input::get("userPrsh")) === "")	return composeReply("ERROR", "Perusahaan harus diisi");
		if(trim(Input::get("userPrsh") != "-")) {
			$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("userPrsh"))->first();
			if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahan tidak dikenal");
		}

		if(null === Input::get("userNama") || trim(Input::get("userNama")) === "")		return composeReply("ERROR", "Nama user harus diisi");
		if(null === Input::get("userGroup") || trim(Input::get("userGroup")) === "")	return composeReply("ERROR", "Group role user harus diisi");
		if(null === Input::get("userPass") || trim(Input::get("userPass")) === "")		return composeReply("ERROR", "Password harus diisi minimal 6 karakter");
	    // if(!preg_match('(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}', Input::get("userPass"))) return composeReply("ERROR", "Password harus kombinasi karakter");

		if(strlen(Input::get("userPass")) < 6)	return composeReply("ERROR", "Password harus diisi minimal 6 karakter");

		//supervisor
		$opr = DB::table("coll_user")->insert(array(
				'U_ID' => $userId,
				'USERBIGID' => trim(Input::get("userBigId")),
				'U_NAMA' => trim(Input::get("userNama")),
				'U_GROUP_ROLE' => trim(Input::get("userGroup")),
				'U_KODE_GROUP' => trim(Input::get("userKode")),
				'U_TELPON' => $userPonsel,
				'U_EMAIL' => $userEmail,
				'U_PASSWORD' => trim(Input::get("userPass")),
				'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
				'U_STATUS' => 'USER_ACTIVE',
				'PRSH_ID' => '-'
		));

		return composeReply("SUCCESS", "Data user Admin telah disimpan");
	} else {
		Session::flush();
		return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
	}

	}

    public function check()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
            if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
                Session::flush();
                return Redirect::to('login')->with('ctlError', 'Silahkan Login terlebih dahulu');
            }
            if (! empty($userBigId = Input::get('userBigId'))) {
                $userId = DB::table('coll_user')->where('USERBIGID', $userBigId)->where('PRSH_ID', Input::get('userPrsh'))->first();
                if (empty($userId)) {
                    return composeReply('SUCCESS', 'User ID tersedia');
                }
                return composeReply('ERROR', 'User ID sudah dipakai');
            }
        } else {
            Session::flush();
            return composeReply('ERROR', 'Harus Login terlebih dahulu');
        }
    }

	public function checkuser()
	{
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError', 'Silahkan Login terlebih dahulu');
			}
            $value = Input::get('value');
            switch (Input::get('key')) {
                case 'userId':
                    $key = 'U_ID';
                    break;
                case 'userEmail':
                    $key = 'U_EMAIL';
                    break;
                case 'userPonsel':
                    $key = 'U_TELPON';
                    break;
                default:
                    $key = '';
                    break;
            }
            if (! empty($key)) {
                $user = DB::table('coll_user')->where($key, $value)->first();
				if (empty($user)) {
					return composeReply('SUCCESS', $value . ' tersedia');
				}
				return composeReply('ERROR', $value . ' sudah dipakai');
			}
		} else {
			Session::flush();
			return composeReply('ERROR', 'Harus Login terlebih dahulu');
		}
	}

	public function tambahUser() {
		if(Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError', 'Silahkan Login terlebih dahulu');
			}

		$userId = Session::get('SESSION_USER_ID', '');

		if(null === Input::get("userId") || trim(Input::get("userId")) === "") return composeReply("ERROR", "User ID harus diisi");
		$userId = preg_replace('/\s+/', '', Input::get("userId"));
		$userData = DB::table('coll_user')->where('U_ID', $userId)->first();
		if(count($userData) > 0)	return composeReply("ERROR", "User ID sudah digunakan");

		if(null === Input::get("userEmail") ||  trim(Input::get("userEmail")) === "") return composeReply("ERROR", "Email harus diisi");
		$userEmail = preg_replace('/\s+/', '', Input::get("userEmail"));
		if(!preg_match ('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $userEmail)) return composeReply("ERROR", "Isikan Email dengan format xxx@xx.xx");
		$cek = DB::table('coll_user')->where('U_EMAIL', $userEmail)->first();
		if(count($cek) > 0)	return composeReply("ERROR", "Email sudah digunakan");

		if(!is_numeric(Input::get("userPonsel")))	return composeReply("ERROR", "Isikan ponsel dengan format 08xxx");
		$userPonsel = formatPonsel(preg_replace('/\s+/', '', Input::get("userPonsel")), "0");
		$cek = DB::table('coll_user')->where('U_TELPON', $userPonsel)->first();
		if(count($cek) > 0)	return composeReply("ERROR", "Nomor ponsel sudah digunakan");

        if(null === Input::get("userPrsh") || trim(Input::get("userPrsh")) === "")	return composeReply("ERROR", "Perusahaan harus diisi");
		if(trim(Input::get("userPrsh") != "-")) {
			$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("userPrsh"))->first();
			if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahan tidak dikenal");
		}

		if(null === Input::get("userNama") || trim(Input::get("userNama")) === "")		return composeReply("ERROR", "Nama user harus diisi");
		if(null === Input::get("userGroup") || trim(Input::get("userGroup")) === "")	return composeReply("ERROR", "Group role user harus diisi");
		if(null === Input::get("userPass") || trim(Input::get("userPass")) === "")		return composeReply("ERROR", "Password harus diisi minimal 6 karakter");

		if(trim(Input::get("userPrsh")) === "-" && Input::get("userGroup") != "GR_ADMINISTRATOR")	return composeReply("ERROR", "User non-perusahaan hanya bisa diset sebagai anggota grup ".getReferenceInfo("GROUP_ROLE", "GR_ADMINISTRATOR"));

		//jika user perusahaan, hanya boleh diset sbg spv atau coll
		if(trim(Input::get("userPrsh")) !== "-" && Input::get("userGroup") === "GR_ADMINISTRATOR")	return composeReply("ERROR", "User perusahaan hanya bisa diset sebagai anggota grup ".getReferenceInfo("GROUP_ROLE", "GR_SUPERVISOR")." atau grup ".getReferenceInfo("GROUP_ROLE", "GR_COLLECTOR"));

		$allAdmin = DB::select("SELECT * FROM coll_user WHERE U_GROUP_ROLE = 'GR_ADMINISTRATOR'");

		$prshId = Input::get('userPrsh');

		//supervisor
		$allspv = DB::select("SELECT * FROM coll_user WHERE PRSH_ID = '$prshId' AND U_GROUP_ROLE = 'GR_SUPERVISOR'");

		//collector
		$allColl = DB::select("SELECT * FROM coll_user WHERE PRSH_ID = '$prshId' AND U_GROUP_ROLE = 'GR_COLLECTOR'");

		$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("userPrsh"))->first();
		$prshMax = $prshData->{"PRSH_MAX_SPV"};
		$collMax = $prshData->{"PRSH_MAX_COLLECT"};
		$userBigId = Input::get("userBigId");

		$kode_group = Input::get('userKodeGroup');
		$userFind = DB::select("SELECT * FROM coll_user WHERE USERBIGID = '$userBigId' AND U_ID = '$userId' AND U_KODE_GROUP = '$kode_group' AND PRSH_ID = '$prshId'");

		if(Input::get('userGroup') === "GR_SUPERVISOR") {
			if(empty($userFind)) {
				if(count($allspv) < $prshMax) {
				$opr = DB::table("coll_user")->insert(array(
						'U_ID' => $userId,
						'USERBIGID' => $userBigId,
						'U_NAMA' => Input::get("userNama"),
						'U_GROUP_ROLE' => Input::get("userGroup"),
						'U_TELPON' => $userPonsel,
						'U_EMAIL' => $userEmail,
						'U_PASSWORD' => Input::get("userPass"),
						'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
						'U_STATUS' => 'USER_ACTIVE',
						'PRSH_ID' => Input::get("userPrsh"),
						'U_KODE_GROUP' => Input::get("userKodeGroup")
					));
				} else {
					return composeReply("ERROR", "Supervisor Melebihi data yang telah ditentukan");
				}
			} else {
				return composeReply("ERROR", "User ID,Kode Group sudah pernah digunkanan");
			}
		} elseif(Input::get('userGroup') === "GR_COLLECTOR") {
			if(empty($userFind)) {
				if(count($allColl) < $collMax) {
						$opr = DB::table("coll_user")->insert(array(
							'U_ID' => $userId,
							'USERBIGID' => Input::get("userBigId"),
							'U_NAMA' => Input::get("userNama"),
							'U_GROUP_ROLE' => Input::get("userGroup"),
							'U_TELPON' => $userPonsel,
							'U_EMAIL' => $userEmail,
							'U_KODE_GROUP' => Input::get("userKode"),
							'U_PASSWORD' => Input::get("userPass"),
							'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
							'U_STATUS' => 'USER_ACTIVE',
							'U_NOTA_ID' => '0',
							'PRSH_ID' => Input::get("userPrsh"),
							'U_STATUS_COLLECT' => Input::get("status_collect"),
							'U_STATUS_TAB' => Input::get("status_tab")
						));
				} else {
					return composeReply("ERROR", "Collector Melebihi data yang telah ditentukan");
				}
			} else {
				return composeReply("ERROR", "User ID,Kode Group sudah pernah digunkanan");
			}
		}

		if(trim(Input::get('userGroup')) === "GR_ADMINISTRATOR") {
			//if(count($allAdmin) > 0) {
				$opr = DB::table("coll_user")->insert(array(
						'U_ID' => $userId,
						'U_NAMA' => trim(Input::get("userNama")),
						'U_GROUP_ROLE' => trim(Input::get("userGroup")),
						'U_TELPON' => $userPonsel,
						'U_EMAIL' => $userEmail,
						'U_PASSWORD' => trim(Input::get("userPass")),
						'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
						'U_STATUS' => 'USER_ACTIVE',
						'PRSH_ID' => trim(Input::get("userPrsh"))
				));
		    //} else {
		    //	return composeReply("ERROR", "Tester ");
		    //}
		}

		return composeReply("SUCCESS", "Data user telah disimpan");
	} else {
		Session::flush();
		return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
	}

	}

	public function addUser() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("userId") || trim(Input::get("userId")) === "")	return composeReply("ERROR", "User ID harus diisi");
			$userId = preg_replace('/\s+/', '', Input::get("userId"));
			$userData = DB::table('coll_user')->where('U_ID', $userId)->first();
			// if(count($userData) > 0)	return composeReply("ERROR", "User ID sudah digunakan");

			if(null === Input::get("userEmail") || trim(Input::get("userEmail")) === "")	return composeReply("ERROR", "Email harus diisi");
			$userEmail = preg_replace('/\s+/', '', Input::get("userEmail"));
			if(!preg_match ('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $userEmail)) return composeReply("ERROR", "Isikan Email dengan format xxx@xx.xx");
			$cek = DB::table('coll_user')->where('U_EMAIL', $userEmail)->first();
			if(count($cek) > 0)	return composeReply("ERROR", "Email sudah digunakan");

			if(null === Input::get("userPonsel") || trim(Input::get("userPonsel")) === "")	return composeReply("ERROR", "Nomor ponsel harus diisi");
			if(!is_numeric(Input::get("userPonsel")))	return composeReply("ERROR", "Isikan ponsel dengan format 08xxx");
			$userPonsel = formatPonsel(preg_replace('/\s+/', '', Input::get("userPonsel")), "0");
			$cek = DB::table('coll_user')->where('U_TELPON', $userPonsel)->first();
			if(count($cek) > 0)	return composeReply("ERROR", "Nomor ponsel sudah digunakan");

			if(null === Input::get("userPrsh") || trim(Input::get("userPrsh")) === "")	return composeReply("ERROR", "Perusahaan harus diisi");
			if(trim(Input::get("userPrsh") != "-")) {
				$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("userPrsh"))->first();
				if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahan tidak dikenal");
			}


			if(null === Input::get("userNama") || trim(Input::get("userNama")) === "")		return composeReply("ERROR", "Nama user harus diisi");
			if(null === Input::get("userGroup") || trim(Input::get("userGroup")) === "")	return composeReply("ERROR", "Group role user harus diisi");
			if(null === Input::get("userPass") || trim(Input::get("userPass")) === "")		return composeReply("ERROR", "Password harus diisi minimal 6 karakter");
			//$input = Input::get("userPass");
			//if(!preg_match('(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$', $input)) return composeReply("ERROR", "Password Harus Berisi karakter dan angka");
			if(strlen(Input::get("userPass")) < 6)	return composeReply("ERROR", "Password harus diisi minimal 6 karakter");

			//TO DO
			//tambah rule jika non perusahaan hanya boleh input administrator
			if(trim(Input::get("userPrsh")) === "-" && Input::get("userGroup") != "GR_ADMINISTRATOR")	return composeReply("ERROR", "User non-perusahaan hanya bisa diset sebagai anggota grup ".getReferenceInfo("GROUP_ROLE", "GR_ADMINISTRATOR"));

			//jika user perusahaan, hanya boleh diset sbg spv atau coll
			if(trim(Input::get("userPrsh")) !== "-" && Input::get("userGroup") === "GR_ADMINISTRATOR")	return composeReply("ERROR", "User perusahaan hanya bisa diset sebagai anggota grup ".getReferenceInfo("GROUP_ROLE", "GR_SUPERVISOR")." atau grup ".getReferenceInfo("GROUP_ROLE", "GR_COLLECTOR"));
			//perusahaan all user
			$allUser = DB::select("SELECT * FROM coll_user WHERE U_GROUP_ROLE = 'GR_ADMINISTRATOR'");
			//$prshId = Input::get('userPrsh');

			//supervisor
			$allspv = DB::select("SELECT * FROM coll_user WHERE PRSH_ID = '$prshId' AND U_GROUP_ROLE = 'GR_SUPERVISOR'");

			//collector
			$allColl = DB::select("SELECT * FROM coll_user WHERE PRSH_ID = '$prshId' AND U_GROUP_ROLE = 'GR_COLLECTOR'");

			$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("userPrsh"))->first();
			$prshMax = $prshData->{"PRSH_MAX_SPV"};
			$collMax = $prshData->{"PRSH_MAX_COLLECT"};

			// if(Input::get("userGroup") === "GR_ADMINISTRATOR") {
			// 	$opr = DB::table("coll_user")->insert(array(
			// 		'U_ID' => $userId,
			// 		'U_NAMA' => trim(Input::get("userNama")),
			// 		'U_GROUP_ROLE' => trim(Input::get("userGroup")),
			// 		'U_TELPON' => $userPonsel,
			// 		'U_EMAIL' => $userEmail,
			// 		'U_PASSWORD' => trim(Input::get("userPass")),
			// 		'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
			// 		'U_STATUS' => 'USER_ACTIVE',
			// 		'PRSH_ID' => trim(Input::get("userPrsh"))
			// 	));
			if(Input::get("userGroup") === "GR_SUPERVISOR") {
				if(count($allspv) < $prshMax) {
					$opr = DB::table("coll_user")->insert(array(
						'U_ID' => $userId,
						'U_NAMA' => Input::get("userNama"),
						'U_GROUP_ROLE' => Input::get("userGroup"),
						'U_TELPON' => $userPonsel,
						'U_EMAIL' => $userEmail,
						'U_PASSWORD' => Input::get("userPass"),
						'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
						'U_STATUS' => 'USER_ACTIVE',
						'PRSH_ID' => Input::get("userPrsh")
					));
				} else {
					return composeReply("ERROR", "Supervisor Melebihi data yang telah ditentukan");
				}
			} elseif (Input::get("userGroup") === "GR_COLLECTOR") {
				if(count($allColl) < $collMax) {
					$opr = DB::table("coll_user")->insert(array(
						'U_ID' => $userId,
						'U_NAMA' => Input::get("userNama"),
						'U_GROUP_ROLE' => Input::get("userGroup"),
						'U_TELPON' => $userPonsel,
						'U_EMAIL' => $userEmail,
						'U_KODE_GROUP' => Input::get("userKode"),
						'U_PASSWORD' => Input::get("userPass"),
						'U_PASSWORD_HASH' => md5($userId.Input::get("userPass")),
						'U_STATUS' => 'USER_ACTIVE',
						'PRSH_ID' => Input::get("userPrsh")
					));
				} else {
					return composeReply("ERROR", "Collector Melebihi data yang telah ditentukan");
				}
			}

			return composeReply("SUCCESS", "Data user telah disimpan");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function updateUser() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("userId") || trim(Input::get("userId")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");

			$userData = DB::table('coll_user')->where('U_ID',Input::get("userId"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak dikenal");
			//var_dump($userData);

			if(null === Input::get("userEmail") || trim(Input::get("userEmail")) === "")	return composeReply("ERROR", "Email harus diisi");
			$userEmail = preg_replace('/\s+/', '', Input::get("userEmail"));
			if($userData->{"U_EMAIL"} !== $userEmail) {
				$cek = DB::table('coll_user')->where('U_EMAIL', $userEmail)->first();
				if(count($cek) > 0)	return composeReply("ERROR", "Email sudah digunakan");
			}

			if(null === Input::get("userPonsel") || trim(Input::get("userPonsel")) === "")	return composeReply("ERROR", "Nomor ponsel harus diisi");
			if(!is_numeric(Input::get("userPonsel")))	return composeReply("ERROR", "Isikan ponsel dengan format 08xxx");
			$userPonsel = formatPonsel(preg_replace('/\s+/', '', Input::get("userPonsel")), "0");
			if($userData->{"U_TELPON"} !== $userPonsel) {
				$cek = DB::table('coll_user')->where('U_TELPON', $userPonsel)->first();
				if(count($cek) > 0)	return composeReply("ERROR", "Nomor ponsel sudah digunakan");
			}

			if(null === Input::get("userPrsh") || trim(Input::get("userPrsh")) === "")	return composeReply("ERROR", "Perusahaan harus diisi");
			if(trim(Input::get("userPrsh") != "-")) {
				$prshData = DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("userPrsh"))->first();
				if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahan tidak dikenal");
			}

			if(null === Input::get("userNama") || trim(Input::get("userNama")) === "")		return composeReply("ERROR", "Nama user harus diisi");
			if(null === Input::get("userGroup") || trim(Input::get("userGroup")) === "")	return composeReply("ERROR", "Group role user harus diisi");

			//TO DO
			//tambah rule jika non perusahaan hanya boleh input administrator
			if(trim(Input::get("userPrsh")) === "-" && Input::get("userGroup") != "GR_ADMINISTRATOR" && Input::get("userGroup") === "GR_ADMINISTRATOR")	return composeReply("ERROR", "User non-perusahaan hanya bisa diset sebagai anggota grup ".getReferenceInfo("GROUP_ROLE", "GR_ADMINISTRATOR"));

			//jika user perusahaan, hanya boleh diset sbg spv atau coll
			if(trim(Input::get("userPrsh")) !== "-" && Input::get("userGroup") === "GR_ADMINISTRATOR" && Input::get("userGroup") === "GR_ADMINISTRATOR")	return composeReply("ERROR", "User perusahaan hanya bisa diset sebagai anggota grup ".getReferenceInfo("GROUP_ROLE", "GR_SUPERVISOR")." atau grup ".getReferenceInfo("GROUP_ROLE", "GR_COLLECTOR"));
			//if($)

			DB::table("coll_user")->where("U_ID", Input::get("userId"))->update(array(
				'U_NAMA' => Input::get("userNama"),
				'USERBIGID' => Input::get("userBigId"),
				'U_GROUP_ROLE' => Input::get("userGroup"),
				'U_TELPON' => $userPonsel,
				'U_EMAIL' => $userEmail,
				'U_KODE_GROUP' => Input::get("userKode"),
				'PRSH_ID' => Input::get("userPrsh"),
				'U_STATUS_COLLECT' => input::get("status_collect"),
				'U_STATUS_TAB' => Input::get("status_tab")
			));

			return composeReply("SUCCESS", "Perubahan data user telah disimpan");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function deleteUser() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			$userData = DB::table('coll_user')->where('U_ID',Input::get("id"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak dikenal");

			$batchData = DB::table("coll_batch_upload_data")->where("BUD_COLL_U_ID", $userData->{"U_ID"})->get();
			if(count($batchData) > 0)	return composeReply("ERROR", "Data user tidak bisa dihapus karena terdapat riwayat penagihan oleh user tsb");

			DB::table("coll_user")->where("U_ID", Input::get("id"))->delete();

			return composeReply("SUCCESS", "Data telah dihapus");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	//function tambahan
	public function deleteAdmin() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			$userData = DB::table('coll_user')->where('U_ID',Input::get("id"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak dikenal");

			$batchData = DB::table("coll_batch_upload_data")->where("BUD_COLL_U_ID", $userData->{"U_ID"})->get();
			if(count($batchData) > 0)	return composeReply("ERROR", "Data user tidak bisa dihapus karena terdapat riwayat penagihan oleh user tsb");

			DB::table("coll_user")->where("U_ID", Input::get("id"))->delete();

			return composeReply("SUCCESS", "Data telah dihapus");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function resetUserPassword() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			$userData = DB::table('coll_user')->where('U_ID',Input::get("id"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak dikenal");

			$defaultPassword = getSetting("DEFAULT_PASSWORD");

			DB::table("coll_user")->where("U_ID", Input::get("id"))->update(array(
				'U_PASSWORD' => $defaultPassword,
				'U_PASSWORD_HASH' => md5($userData->{"U_ID"}.$defaultPassword),
				'U_LOGIN_TOKEN' => '-'
			));

			return composeReply("SUCCESS", "Perubahan data telah disimpan");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function getUserData() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");

			$userData = DB::table('coll_user')->where('U_ID',Input::get("id"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak terdaftar");

			return composeReply("SUCCESS", "User data", $userData);
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function updateUserStatus() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			//$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			$userData = DB::table('coll_user')->where('U_ID',Input::get("id"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak dikenal");

			if(null === Input::get("userStatus") || trim(Input::get("userStatus")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			if(strtoupper(trim(Input::get("userStatus"))) == "AKTIF") {
				$status = "USER_ACTIVE";
			}
			elseif(strtoupper(trim(Input::get("userStatus"))) == "NON_AKTIF") {
				$status = "USER_INACTIVE";
			}
			else {
				return composeReply("ERROR", "Parameter tidak sesuai");
			}

			DB::table("coll_user")->where("U_ID", Input::get("id"))->update(array(
				'U_STATUS' => $status,
				'U_LOGIN_TOKEN' => '-'
			));

			return composeReply("SUCCESS", "Perubahan data telah disimpan");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

    public function editPassword()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
            if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
                Session::flush();
                return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $userId = Session::get('SESSION_USER_ID', '');
            $userData = DB::table('coll_user')->where('U_ID',$userId)->first();
            return View::make('dashboard.edit-password')
                        ->with("ctlUserData", $userData);
        } else {
            Session::flush();
            return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
        }
    }

	public function updatePassword()
	{
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}
			if (empty(Input::get('user_id')) || empty(Input::get('password'))) {
				return composeReply("ERROR", "Parameter tidak lengkap");
			}
			$userData = DB::table('coll_user')->where('U_ID',Input::get("user_id"))->first();
			if(count($userData) <= 0)	return composeReply("ERROR", "User tidak terdaftar");
			DB::table("coll_user")->where("U_ID", Input::get("user_id"))->update(array(
				'U_PASSWORD' => Input::get('password'),
				'U_PASSWORD_HASH' => md5($userData->{"U_ID"}.Input::get('password')),
				// 'U_LOGIN_TOKEN' => '-',
                'U_GANTIPASS' => true,
			));
			return composeReply("SUCCESS", "Perubahan data telah disimpan");
		} else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}
}
?>
