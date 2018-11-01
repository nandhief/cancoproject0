<?php

class TabunganController extends BaseController {

	public function index()
	{
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $tabungans = DB::select('SELECT DATE(TGL_SETORAN) TGL, COUNT(*) JUMLAH, SUM(SETORAN) TOTAL
                FROM coll_tabungan_history
                WHERE PRSH_ID = ?
                GROUP BY DATE(CREATED_AT)', [
                    $user->PRSH_ID
                ]);
            $nasabah = DB::select('SELECT * FROM coll_tabungan ct INNER JOIN coll_customers cc ON ct.CUST_ID = cc.CUST_ID WHERE ct.PRSH_ID = ?', [
                $user->PRSH_ID
            ]);
            return View::make('dashboard.tabungan.index')
                    ->with("ctlUserData", $user)
                    ->with('tabungans', $tabungans)
                    ->with('nasabah', $nasabah)
                    ->with("ctlNavMenu", "mCollTabungan");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

	public function store()
	{
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}
			$user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $tgl = DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->first();
            if (isset($_FILES['tabungan'])) {
                $files = $_FILES['tabungan'];
                $filename = $files['name'];
                $filesize = $files['size'];
                $file = $files['tmp_name'];
                $filetype = $files['type'];
                $ext = explode('.', $filename);
                $fileext = strtolower(end($ext));
                $exts = ["xls","xlsx","XLS","XLSX"];
                if (in_array($fileext, $exts) === false) return composeReply("ERROR","Harap pilih file Excel");
                if ($filesize > 2048000) return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");
                $upload = "uploads/tabungan-".createSlug($user->U_ID)."-".date("YmdHis.").$fileext;
                if (move_uploaded_file($file, $upload) == true) {
                    DB::beginTransaction();
                    if (empty($tgl)) {
                        $bud_id = DB::table('coll_batch_upload')->insertGetId([
                            'BU_TGL' => date("Y-m-d"),
                            'BU_FILE_PATH' => $upload,
                            'PRSH_ID' => $user->PRSH_ID,
                            'U_ID' => $user->U_ID,
                            'BU_TYPE' => 'BU_TABUNGAN'
                        ]);
                    } else {
                        DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->update([
                            'BU_TGL' => date("Y-m-d"),
                            'BU_FILE_PATH' => $upload,
                            'PRSH_ID' => $user->PRSH_ID,
                            'U_ID' => $user->U_ID,
                            'BU_TYPE' => 'BU_TABUNGAN'
                        ]);
                        $bud_id = DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->first()->BU_ID;
                    }
                    if(!isset($bud_id) || $bud_id <= 0) {
                        DB::rollback();
                        return composeReply("ERROR", "Proses penyimpanan data tabungan mengalami kegagalan");
                    }
                    $objPHPExcel = PHPExcel_IOFactory::load($upload);
                    $objWorksheet = $objPHPExcel->getActiveSheet();

                    $highestRow = $objWorksheet->getHighestRow();
                    $highestColumn = $objWorksheet->getHighestColumn();
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                    $titles = $objWorksheet->rangeToArray('A1:' . $highestColumn . "1");
                    $body = $objWorksheet->rangeToArray('A2:' . $highestColumn . $highestRow);
                    $table = [];
                    for ($row=0; $row <= $highestRow - 2; $row++) {
                        $data = [];
                        for ($column=0; $column <= $highestColumnIndex - 1; $column++) {
                            if($column == 0 && $titles[0][$column] != "CAB") return composeReply("ERROR","Kolom ke-1 HARUS bernama CAB");
                            if($column == 1 && $titles[0][$column] != "NO_REKENING") return composeReply("ERROR","Kolom ke-2 HARUS bernama NO_REKENING");
                            if($column == 2 && $titles[0][$column] != "NASABAH_ID") return composeReply("ERROR","Kolom ke-3 HARUS bernama ID_NASABAH");
                            if($column == 3 && $titles[0][$column] != "NAMA_NASABAH") return composeReply("ERROR","Kolom ke-4 HARUS bernama NAMA_NASABAH");
                            if($column == 4 && $titles[0][$column] != "ALAMAT") return composeReply("ERROR","Kolom ke-5 HARUS bernama ALAMAT");
                            $data[$titles[0][$column]] = $body[$row][$column];
                        }
                        $table[$row] = $data;
                        if(isset($table[$row]) && !empty($table[$row]["NASABAH_ID"])) {
                            /* Cek Nasabah */
                            $nasabah = DB::table("coll_customers")->where("CUST_ID", trim($table[$row]["NASABAH_ID"]))->first();
                            if (count($nasabah) < 1) {
                                DB::table("coll_customers")->insert(array(
                                    'CUST_ID' => trim($table[$row]["NASABAH_ID"]),
                                    'CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                                    'CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                                ));
                                $nasabah = DB::table("coll_customers")->where("CUST_ID", trim($table[$row]["NASABAH_ID"]))->first();
                            } else {
                                /* Update Jika Ada Perubahan */
                                if(trim(strtoupper($nasabah->{"CUST_NAMA"})) != trim(strtoupper($table[$row]["NAMA_NASABAH"]))) {
                                    DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["NASABAH_ID"]))->update(array(
                                        'CUST_NAMA' => $table[$row]["NAMA_NASABAH"]
                                    ));
                                }
                                if(trim(strtoupper($nasabah->{"CUST_ALAMAT"})) != trim(strtoupper($table[$row]["ALAMAT"]))) {
                                    DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["NASABAH_ID"]))->update(array(
                                        'CUST_ALAMAT' => $table[$row]["ALAMAT"]
                                    ));
                                }
                            }
                            /* Cek Data Nasabah Tabungan */
                            $tabungan = DB::table('coll_tabungan')->where('REK', $table[$row]['NO_REKENING'])->where('CUST_ID', $nasabah->CUST_ID)->where('PRSH_ID', $user->PRSH_ID)->first();
                            if (count($tabungan) < 1) {
                                DB::table('coll_tabungan')->insert([
                                    'BU_ID' => $bud_id,
                                    'REK' => $table[$row]['NO_REKENING'],
                                    'CAB' => $table[$row]['CAB'],
                                    'CUST_ID' => $nasabah->CUST_ID,
                                    'PRSH_ID' => $user->PRSH_ID
                                ]);
                            } else {
                                DB::table('coll_tabungan')->whereRaw("DATE(CREATED_AT) = ?", [date('Y-m-d')])->update([
                                    'BU_ID' => $bud_id,
                                ]);
                            }
                        }
                    }
                    DB::commit();
                    if (empty($tgl)) {
                        return composeReply("SUCCESS", "Data Nasabah Tabungan telah disimpan");
                    } else {
                        return composeReply("SUCCESS", "Data Nasabah Tabungan telah disimpan dan data telah diperbarui dengan upload terbaru");
                    }
                }
            }
            return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}

	public function show($tgl)
	{
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $tabungans = DB::select('SELECT *
            FROM coll_tabungan_history cth
            INNER JOIN coll_tabungan ct ON cth.T_ID = ct.ID
            INNER JOIN coll_customers cc ON ct.CUST_ID = cc.CUST_ID
            INNER JOIN coll_user cu ON cth.COLL_ID = cu.U_ID
            WHERE DATE(TGL_SETORAN) = ? AND cth.PRSH_ID = ?', [
                $tgl, $user->PRSH_ID
            ]);
            return View::make('dashboard.tabungan.show')
                    ->with("ctlUserData", $user)
                    ->with("tgl", $tgl)
                    ->with('tabungans', $tabungans)
                    ->with("ctlNavMenu", "mCollTabungan");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
	}
}
