<?php
class CompanyController extends BaseController {
	public function redirectMissing() {
		Session::flush();
		return Redirect::to('login');
	}

	public function listCompanies() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			$prsh = DB::table("coll_perusahaan")->get();
			$comp = DB::select("SELECT * FROM coll_perusahaan");

			$jml = (count($prsh)) + 1;
			if($jml < 10)	$jml = "60255700".$jml;
			if($jml > 10 && $jml < 100)	$jml = "6025570".$jml;

			do {
				$jml = $jml+1;
			} while (DB::table('coll_perusahaan')->where('PRSH_ID', 'PIN' . $jml)->count() > 0);


			foreach ($prsh as $aData) {
				$spv = DB::select("SELECT IFNULL(COUNT(U_ID),0) AS JUMLAH FROM coll_user WHERE PRSH_ID = ? AND U_GROUP_ROLE = 'GR_SUPERVISOR'", array($aData->{"PRSH_ID"}));
				$aData->{"JUMLAH_SPV"} = $spv[0]->{"JUMLAH"};

				$coll = DB::select("SELECT IFNULL(COUNT(U_ID),0) AS JUMLAH FROM coll_user WHERE PRSH_ID = ? AND U_GROUP_ROLE = 'GR_COLLECTOR'", array($aData->{"PRSH_ID"}));
				$aData->{"JUMLAH_COLL"} = $coll[0]->{"JUMLAH"};

				$data = DB::select("SELECT IFNULL(COUNT(BUD_ID),0) AS JUMLAH FROM coll_batch_upload_data WHERE PRSH_ID = ?", array($aData->{"PRSH_ID"}));
				$aData->{"JUMLAH_DATA"} = $data[0]->{"JUMLAH"};

				$maxSpv = DB::select("SELECT * FROM coll_perusahaan WHERE PRSH_ID = ?", array($aData->{"PRSH_ID"}));
				$aData->{"PRSH_MAX_SPV"} = $maxSpv[0]->{"PRSH_MAX_SPV"};
				$aData->{"PRSH_MAX_COLLECT"} = $maxSpv[0]->{"PRSH_MAX_COLLECT"};

				//print_r()
			}

			return View::make("dashboard.comp-mgmt.comp-formlist")
				->with("ctlUserData", $userData)
				->with("ctlPrsh", $prsh)
				->with("ctlUrutan", $jml)
				->with("comp", $comp)
				->with("maxSpve", $maxSpv)
				->with("spv", $spv)
				->with("collect", $coll)
				->with("ctlNavMenu", "mCompMgmt");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

    public function checkLembaga()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
            if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
                Session::flush();
                return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $prsh = DB::table('coll_perusahaan')->where('PRSH_ID', 'PIN'.Input::get('prshId'))->first();
            if (empty($prsh)) {
                return composeReply('SUCCESS', 'User ID tersedia');
            }
            return composeReply('ERROR', 'User ID sudah dipakai');
        } else {
            Session::flush();
            return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
        }
    }

	public function addCompany() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("prshId") || trim(Input::get("prshId")) === "")	return composeReply("ERROR", "ID perusahaan harus diisi");
			$prshId = strtoupper(preg_replace('/\s+/', '', getSetting("COMPANY_PREFIX").Input::get("prshId")));
			$prshData = DB::table('coll_perusahaan')->where('PRSH_ID', $prshId)->first();
			// if(count($prshData) > 0)	return composeReply("ERROR", "ID perusahaan sudah digunakan");
			//Input::get('prshPICTelp') = implode('', Input::get('prshPICTelp'));
		    //dd(Input::get('prshPICTelp'));

			if(null === Input::get("prshNama") || trim(Input::get("prshNama")) === "")				return composeReply("ERROR", "Nama perusahaan harus diisi");
			if(null === Input::get("prshAlamat") || trim(Input::get("prshAlamat")) === "")		return composeReply("ERROR", "Alamat perusahaan harus diisi");
			if(null === Input::get("prshKota") || trim(Input::get("prshKota")) === "")				return composeReply("ERROR", "Kota perusahaan harus diisi");
			if(null === Input::get("prshPICNama") || trim(Input::get("prshPICNama")) === "")	return composeReply("ERROR", "Nama penanggung jawab harus diisi");
			if(null === Input::get("prshPICTelp") || trim(Input::get("prshPICTelp")) === "")	return composeReply("ERROR", "Nomor telepon penanggung jawab harus diisi");
			if(null === Input::get("prshPICTelpCode") || trim(Input::get("prshPICTelpCode")) === "")	return composeReply("ERROR", "Kode area telepon penanggung jawab harus diisi");

			if(null === Input::get("maxSupervisor") || trim(Input::get("maxSupervisor")) === "") return composeReply("ERROR", "Max Input Supervisor harus diisi");

			if(null === Input::get("maxCollector") || trim(Input::get("maxCollector")) === "") return composeReply("ERROR", "Max Input Collector harus diisi");
		    if(null === Input::get("prshTipe") || trim(Input::get("prshTipe")) === "") return composeReply("ERROR", "Perusahaan Tipe harus diisi");
			$opr = DB::table("coll_perusahaan")->insert(array(
				'PRSH_ID' => $prshId,
				'PRSH_NAMA' => Input::get("prshNama"),
				'PRSH_MAX_COLLECT' => Input::get("maxCollector"),
				'PRSH_MAX_SPV' => Input::get("maxSupervisor"),
				'PRSH_JENIS_TIPE' => Input::get("prshTipe"),
				'PRSH_ALAMAT' => Input::get("prshAlamat"),
				'PRSH_KOTA' => Input::get("prshKota"),
				'PRSH_PIC_NAMA' => Input::get("prshPICNama"),
				'PRSH_PIC_TELP' => (Input::get('prshPICTelpCode') . '-' .Input::get("prshPICTelp")),
				'PRSH_TELP' => Input::get("prshPICHp"),
				'PRSH_STATUS_AKTIF' => 'Y',
				'PRSH_REGISTRAR_U_ID' => $userId
			));

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
            DB::table("coll_perusahaan")->where("PRSH_ID", $prshId)->update(array(
            	'PRSH_IMG_PATH' => $uploadFile
            ));
            return composeReply("SUCCESS", "Data perusahaan telah disimpan beserta logo perusahaan");
          }
        }
        else {
          return composeReply("ERROR","Data perusahaan telah disimpan namun proses upload gagal (file upload tidak terdeteksi server)");
        }
      }

			//if($opr == true) {
				return composeReply("SUCCESS", "Data perusahaan telah disimpan tanpa logo perusahaan");
			//}
			//else {
			//	return composeReply("ERROR", "Gagal menyimpan data perusahaan");
			//}
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function updateCompany() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("prshId") || trim(Input::get("prshId")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");


			$prshData = DB::table('coll_perusahaan')->where('PRSH_ID',Input::get("prshId"))->first();
			if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahaan tidak dikenal");

			if(null === Input::get("prshNama") || trim(Input::get("prshNama")) === "")				return composeReply("ERROR", "Nama perusahaan harus diisi");
			if(null === Input::get("prshAlamat") || trim(Input::get("prshAlamat")) === "")		return composeReply("ERROR", "Alamat perusahaan harus diisi");
			if(null === Input::get("prshKota") || trim(Input::get("prshKota")) === "")				return composeReply("ERROR", "Kota perusahaan harus diisi");
			if(null === Input::get("prshPICNama") || trim(Input::get("prshPICNama")) === "")	return composeReply("ERROR", "Nama penanggung jawab harus diisi");
			if(null === Input::get("prshPICTelp") || trim(Input::get("prshPICTelp")) === "")	return composeReply("ERROR", "Nomor telepon perusahaan harus diisi");
			//if(null === Input::get("prshTelp") || trim(Input::get("prshPICHp")) === "")	return composeReply("ERROR", "Nomor telepon penanggung jawab harus diisi");
			//if(null === Input::get("prshStatus") || trim(Input::get("prshStatus")) === "")		return composeReply("ERROR", "Status perusahaan harus diisi");
			//if(null === Input::get("maxSupervisorE") || trim(Input::get("maxSupervisorE")) === "") return composeReply("ERROR", "Max Input Supervisor harus diisi");

			//if(null === Input::get("maxCollector") || trim(Input::get("maxCollector")) === "") return composeReply("ERROR", "Max Input Collector harus diisi");
			 if(null === Input::get("prshTipe") || trim(Input::get("prshTipe")) === "") return composeReply("ERROR", "Perusahaan Tipe harus diisi");

			 $spvMax = Input::get("prshMaxSupervisor");
			 //$telpSpv = Input::get("prshPICHp");
			//dd($telpSpv);

			DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("prshId"))->update(array(
				'PRSH_NAMA' => Input::get("prshNama"),
				'PRSH_MAX_COLLECT' => Input::get("prshMaxCollector"),
				'PRSH_MAX_SPV' => $spvMax,
				'PRSH_JENIS_TIPE' => Input::get("prshTipe"),
				'PRSH_ALAMAT' => Input::get("prshAlamat"),
				'PRSH_KOTA' => Input::get("prshKota"),
				'PRSH_PIC_NAMA' => Input::get("prshPICNama"),
				'PRSH_TELP' => Input::get("prshTelp"),
				'PRSH_PIC_TELP' => Input::get("prshPICTelp")/*,
				'PRSH_STATUS_AKTIF' => Input::get("prshStatus")*/
			));
			return composeReply("SUCCESS", "Perubahan data perusahaan telah disimpan");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function deleteCompany() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			$prshData = DB::table('coll_perusahaan')->where('PRSH_ID',Input::get("id"))->first();
			if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahaan tidak dikenal");

			DB::table("coll_batch_upload")->where("PRSH_ID", Input::get("id"))->delete();
			DB::table("coll_batch_upload_data")->where("PRSH_ID", Input::get("id"))->delete();
			DB::table("coll_user")->where("PRSH_ID", Input::get("id"))->delete();
			DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("id"))->delete();

			return composeReply("SUCCESS", "Data perusahaan dan semua turunannya telah dihapus");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function getCompanyData() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");

			$prshData = DB::table('coll_perusahaan')->where('PRSH_ID',Input::get("id"))->first();
			if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahaan tidak terdaftar");

			return composeReply("SUCCESS", "Company data", $prshData);
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function updateCompanyStatus() {
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN')))	{
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}

			$userId = Session::get('SESSION_USER_ID', '');
			$userData = DB::table('coll_user')->where('U_ID',$userId)->first();

			if(null === Input::get("id") || trim(Input::get("id")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			$prshData = DB::table('coll_perusahaan')->where('PRSH_ID',Input::get("id"))->first();
			if(count($prshData) <= 0)	return composeReply("ERROR", "Perusahaan tidak terdaftar");

			if(null === Input::get("prshStatus") || trim(Input::get("prshStatus")) === "")	return composeReply("ERROR", "Parameter tidak lengkap");
			if(strtoupper(trim(Input::get("prshStatus"))) == "AKTIF") {
				$status = "Y";
			}
			elseif(strtoupper(trim(Input::get("prshStatus"))) == "NON_AKTIF") {
				$status = "T";
			}
			else {
				return composeReply("ERROR", "Parameter tidak sesuai");
			}

			DB::table("coll_perusahaan")->where("PRSH_ID", Input::get("id"))->update(array(
				'PRSH_STATUS_AKTIF' => $status
			));

			return composeReply("SUCCESS", "Perubahan status perusahaan telah disimpan");
		}
		else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}
}
?>
