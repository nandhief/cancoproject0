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
            $nasabah = DB::select('SELECT * FROM coll_tabungan ct WHERE ct.PRSH_ID = ?', [
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
            $tgl = DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->where('BU_TYPE', 'BU_TABUNGAN')->first();
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
                        DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->where('BU_TYPE', 'BU_TABUNGAN')->update([
                            'BU_TGL' => date("Y-m-d"),
                            'BU_FILE_PATH' => $upload,
                            'PRSH_ID' => $user->PRSH_ID,
                            'U_ID' => $user->U_ID,
                            'BU_TYPE' => 'BU_TABUNGAN'
                        ]);
                        $bud_id = DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->where('BU_TYPE', 'BU_TABUNGAN')->first()->BU_ID;
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
                            if($column == 0 && strtoupper($titles[0][$column]) != "KODE_GROUP") return composeReply("ERROR","Kolom ke-1 HARUS bernama KODE_GROUP");
                            if($column == 1 && strtoupper($titles[0][$column]) != "CAB") return composeReply("ERROR","Kolom ke-2 HARUS bernama CAB");
                            if($column == 2 && strtoupper($titles[0][$column]) != "NO_REKENING") return composeReply("ERROR","Kolom ke-3 HARUS bernama NO_REKENING");
                            if($column == 3 && strtoupper($titles[0][$column]) != "NASABAH_ID") return composeReply("ERROR","Kolom ke-4 HARUS bernama ID_NASABAH");
                            if($column == 4 && strtoupper($titles[0][$column]) != "NAMA_NASABAH") return composeReply("ERROR","Kolom ke-5 HARUS bernama NAMA_NASABAH");
                            if($column == 5 && strtoupper($titles[0][$column]) != "PONSEL") return composeReply("ERROR","Kolom ke-6 HARUS bernama PONSEL");
                            if($column == 6 && strtoupper($titles[0][$column]) != "ALAMAT") return composeReply("ERROR","Kolom ke-7 HARUS bernama ALAMAT");
                            if($column == 7 && strtoupper($titles[0][$column]) != "SETOR_MINIMUM") return composeReply("ERROR","Kolom ke-8 HARUS bernama SETOR_MINIMUM");
                            $data[strtoupper($titles[0][$column])] = $body[$row][$column];
                        }
                        $table[$row] = $data;
                        if(isset($table[$row]) && !empty($table[$row]["NASABAH_ID"])) {
                            /* Cek kode group by perusahaan */
                            $collect = DB::table('coll_user')->where('U_KODE_TABUNGAN', $table[$row]["KODE_GROUP"])->where('PRSH_ID', $user->PRSH_ID)->first();
                            if (count($collect) < 1) {
                                $collect = DB::table("coll_user")->whereNull("U_KODE_TABUNGAN")->first();
                            }

                            /* Cek Data Nasabah Tabungan */
                            $tabungan = DB::table('coll_tabungan')->where('REK', $table[$row]['NO_REKENING'])->where('CUST_ID', $table[$row]["NASABAH_ID"])->where('PRSH_ID', $user->PRSH_ID)->first();
                            if (count($tabungan) < 1) {
                                DB::table('coll_tabungan')->insert([
                                    'BU_ID' => $bud_id,
                                    'USERID' => $collect->U_ID,
                                    'USERBIGID' => $collect->USERBIGID,
                                    'KODE_GROUP' => $collect->U_KODE_TABUNGAN,
                                    'REK' => $table[$row]['NO_REKENING'],
                                    'CAB' => $table[$row]['CAB'],
                                    'CUST_ID' => $table[$row]["NASABAH_ID"],
                                    'CUST_NAMA' => $table[$row]["NAMA_NASABAH"],
                                    'CUST_ALAMAT' => $table[$row]["ALAMAT"],
                                    'CUST_PONSEL' => $table[$row]["PONSEL"],
                                    'SETOR_MINIMUM' => $table[$row]["SETOR_MINIMUM"],
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

    public function update()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $tgl = DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->where('BU_TYPE', 'BU_TABUNGAN_NON_AKTIF')->first();
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
                $upload = "uploads/tabungan_nonaktif-".createSlug($user->U_ID)."-".date("YmdHis.").$fileext;
                if (move_uploaded_file($file, $upload) == true) {
                    DB::beginTransaction();
                    if (empty($tgl)) {
                        $bud_id = DB::table('coll_batch_upload')->insertGetId([
                            'BU_TGL' => date("Y-m-d"),
                            'BU_FILE_PATH' => $upload,
                            'PRSH_ID' => $user->PRSH_ID,
                            'U_ID' => $user->U_ID,
                            'BU_TYPE' => 'BU_TABUNGAN_NON_AKTIF'
                        ]);
                    } else {
                        DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->where('BU_TYPE', 'BU_TABUNGAN_NON_AKTIF')->update([
                            'BU_TGL' => date("Y-m-d"),
                            'BU_FILE_PATH' => $upload,
                            'PRSH_ID' => $user->PRSH_ID,
                            'U_ID' => $user->U_ID,
                            'BU_TYPE' => 'BU_TABUNGAN_NON_AKTIF'
                        ]);
                        $bud_id = DB::table('coll_batch_upload')->where('BU_TGL', date('Y-m-d'))->where('PRSH_ID', $user->PRSH_ID)->where('BU_TYPE', 'BU_TABUNGAN_NON_AKTIF')->first()->BU_ID;
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
                            if($column == 0 && strtoupper($titles[0][$column]) != "NO_REKENING") return composeReply("ERROR","Kolom ke-1 HARUS bernama NO_REKENING");
                            $data[strtoupper($titles[0][$column])] = $body[$row][$column];
                        }
                        $table[$row] = $data;
                        if(isset($table[$row]) && !empty($table[$row]["NO_REKENING"])) {
                            /* Cek Data Nasabah Tabungan */
                            $tabungan = DB::table('coll_tabungan')->where('REK', $table[$row]['NO_REKENING'])->where('PRSH_ID', $user->PRSH_ID)->first();
                            if (count($tabungan) > 0) {
                                DB::table('coll_tabungan')->where('REK', $table[$row]['NO_REKENING'])->update([
                                    'TUTUP' => true,
                                ]);
                            }
                        }
                    }
                    DB::commit();
                    return composeReply("SUCCESS", "Data Nasabah Tabungan telah dinonaktifkan dan data telah diperbarui");
                }
            }
            if (!empty(Input::get('NO_REKENING'))) {
                /* Cek Data Nasabah Tabungan */
                $tabungan = DB::table('coll_tabungan')->where('REK', Input::get('NO_REKENING'))->where('PRSH_ID', $user->PRSH_ID)->first();
                if (count($tabungan) > 0) {
                    DB::table('coll_tabungan')->where('REK', Input::get('NO_REKENING'))->update([
                        'TUTUP' => true,
                    ]);
                }
                return composeReply("SUCCESS", "Data Nasabah Tabungan " . $tabungan->REK . " telah dinonaktifkan dan data telah diperbarui");
            }
            return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function report_view()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $userId = Session::get('SESSION_USER_ID', '');
            $user = DB::table('coll_user')->where('U_ID',$userId)->first();
            if (Input::get('type') == 'bulanan') {
                empty(Input::get('periode')) ? ($periode = date('Y-m')) : ($periode = Input::get('periode'));
                $array_periode = explode('-', $periode);
                $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $array_periode[1], $array_periode[0]);
                $awal = $periode . '-01';
                $akhir = $periode . '-' . $daysOfMonth;
                $tabungan = $this->query_report($awal, $akhir, $user->PRSH_ID);
            }
            if (Input::get('type') == 'collector') {
                empty(Input::get('laporanTglAwal')) ? ($awal = date('Y-m') . '-01') : ($awal = date_format(date_create(Input::get('laporanTglAwal')), 'Y-m-d'));
                empty(Input::get('laporanTglAkhir')) ? ($akhir = date('Y-m-d')) : ($akhir = date_format(date_create(Input::get('laporanTglAkhir')), 'Y-m-d'));
                if (Input::get('laporanCollector') == 'ALL') {
                    $tabungan = $this->query_report($awal, $akhir, $user->PRSH_ID);
                } else {
                    $tabungan = $this->query_report($awal, $akhir, $user->PRSH_ID, Input::get('laporanCollector'));
                }
            }
            return View::make('dashboard.tabungan.laporan_view')
                    ->with("ctlUserData", $user)
                    ->with('tabungans', $tabungan)
                    ->with("ctlNavMenu", "mCollLaporan");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function report_download()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $userId = Session::get('SESSION_USER_ID', '');
            $user = DB::table('coll_user')->where('U_ID',$userId)->first();
            if (Input::get('type') == 'bulanan') {
                empty(Input::get('periode')) ? ($periode = date('Y-m')) : ($periode = Input::get('periode'));
                $array_periode = explode('-', $periode);
                $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $array_periode[1], $array_periode[0]);
                $awal = $periode . '-01';
                $akhir = $periode . '-' . $daysOfMonth;
                $tabungan = $this->query_report($awal, $akhir, $user->PRSH_ID);
            }
            if (Input::get('type') == 'collector') {
                empty(Input::get('laporanTglAwal')) ? ($awal = date('Y-m') . '-01') : ($awal = date_format(date_create(Input::get('laporanTglAwal')), 'Y-m-d'));
                empty(Input::get('laporanTglAkhir')) ? ($akhir = date('Y-m-d')) : ($akhir = date_format(date_create(Input::get('laporanTglAkhir')), 'Y-m-d'));
                if (Input::get('laporanCollector') == 'ALL') {
                    $tabungan = $this->query_report($awal, $akhir, $user->PRSH_ID);
                } else {
                    $tabungan = $this->query_report($awal, $akhir, $user->PRSH_ID, Input::get('laporanCollector'));
                }
            }
            Excel::create('Pintech Mobile App Report Tabungan', function($excel) use($user, $awal, $akhir, $tabungan) {
                $excel->setTitle('Pintech Mobile App System Report ' . $awal . " Sampai " . $akhir);
                $excel->setCreator('Pintech Mobile App System')->setCompany($user->PRSH_ID);
                $excel->setDescription('Laporan Tabungan');
                $excel->sheet('Sheet 1', function ($sheet) use ($user, $awal, $akhir, $tabungan) {
                    $sheet->setOrientation('landscape');
                    $sheet->setCellValue('N1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0, 0, 0, date('m'), date('d'), date('Y')) ));
                    $sheet->getStyle('N1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                    $sheet->setCellValue('A1', "PINTECH MOBILE APP REPORT " . $awal . "-" . $akhir);
                    $sheet->mergeCells('A1:N1');
                    $sheet->getRowDimension('1')->setRowHeight(30);
                    $sheet->getColumnDimension('A')->setWidth(50);
                    $sheet->setCellValue('A2', "TANGGAL");
                    $sheet->getRowDimension('2')->setRowHeight(30);
                    $sheet->getColumnDimension('A')->setWidth(30);
                    $sheet->setCellValue('B2', "USER ID");
                    $sheet->getColumnDimension('B')->setWidth(35);
                    $sheet->setCellValue('C2', "NAMA COLLECTOR");
                    $sheet->getColumnDimension('C')->setWidth(30);
                    $sheet->setCellValue('D2', "KODE GROUP");
                    $sheet->getColumnDimension('D')->setWidth(30);
                    $sheet->setCellValue('E2', "CAB");
                    $sheet->getColumnDimension('E')->setWidth(30);
                    $sheet->setCellValue('F2', "NO REKENING");
                    $sheet->getColumnDimension('F')->setWidth(35);
                    $sheet->setCellValue('G2', "ID NASABAH");
                    $sheet->getColumnDimension('G')->setWidth(35);
                    $sheet->setCellValue('H2', "NAMA NASABAH");
                    $sheet->getColumnDimension('H')->setWidth(50);
                    $sheet->setCellValue('I2', "ALAMAT");
                    $sheet->getColumnDimension('I')->setWidth(70);
                    $sheet->setCellValue('J2', "HP");
                    $sheet->getColumnDimension('J')->setWidth(20);
                    $sheet->setCellValue('K2', "SETORAN");
                    $sheet->getColumnDimension('K')->setWidth(30);
                    $sheet->setCellValue('L2', "TANGGAL SETORAN");
                    $sheet->getColumnDimension('L')->setWidth(30);
                    $sheet->setCellValue('M2', "WAKTU");
                    $sheet->getColumnDimension('M')->setWidth(100);
                    $sheet->setCellValue('N2', "KETERANGAN");
                    $sheet->getColumnDimension('M')->setWidth(100);
                    $sheet->getStyle('A2:N2')->getFont()->setBold(true);
                    $row = 3;
                    $tgl = '';
                    foreach ($tabungan as $key => $value) {
                        if ($tgl == date('Y-m-d', strtotime($value->TGL_SETORAN))) {
                            $sheet->setCellValue('A' . $row, '');
                        } else {
                            $sheet->setCellValue('A' . $row, tglIndo(date('Y-m-d', strtotime($value->TGL_SETORAN)), "SHORT"));
                            $sheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                        }
                        $tgl = date('Y-m-d', strtotime($value->TGL_SETORAN));

                        /*  */
                        $sheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('B' . $row, $value->{"USERBIGID"});
                        $sheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('C' . $row, $value->{"U_NAMA"});
                        $sheet->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('D' . $row, $value->{"COLLECT_KODE"});
                        $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('E' . $row, $value->{"CAB"});
                        $sheet->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('F' . $row, $value->{"REK"});
                        $sheet->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('G' . $row, $value->{"CUST_ID"});
                        $sheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('H' . $row, $value->{"CUST_NAMA"});
                        $sheet->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('I' . $row, $value->{"CUST_ALAMAT"});
                        $sheet->getStyle('J')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('J' . $row, $value->{"CUST_PONSEL"});
                        $sheet->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('K' . $row, $value->{"SETORAN"});
                        $sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('L' . $row, date('d-m-Y', strtotime($value->{"TGL_SETORAN"})));
                        $sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('M' . $row, date('H:i:s', strtotime($value->{"TGL_SETORAN"})));
                        $sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('N' . $row, $value->{"KETERANGAN"});
                        $row++;
                    }
                    $sheet->getStyle('A1:N1')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle('A2:N2')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'top' => [
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                            ],
                            'bottom' => [
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                            ],
                        ],
                        'fill' => [
                            'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startcolor' => [
                                'argb' => 'FFA0A0A0'
                            ],
                            'endcolor' => [
                                'argb' => 'FFFFFFFF'
                            ],
                        ],
                    ]);
                    $sheet->getStyle('A2')->applyFromArray([
                        'borders' => [
                            'left' => [
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                    $sheet->getStyle('N2')->applyFromArray([
                        'borders' => [
                            'right' => [
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                });
                $excel->setActiveSheetIndex(0);
            })->download('xlsx');
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function report_view_admin()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $userId = Session::get('SESSION_USER_ID', '');
            $user = DB::table('coll_user')->where('U_ID', $userId)->first();
            if (Input::get('type') == 'admin') {
                empty(Input::get('laporanTglAwal')) ? ($awal = date('Y-m') . '-01') : ($awal = date_format(date_create(Input::get('laporanTglAwal')), 'Y-m-d'));
                empty(Input::get('laporanTglAkhir')) ? ($akhir = date('Y-m-d')) : ($akhir = date_format(date_create(Input::get('laporanTglAkhir')), 'Y-m-d'));
                if (Input::get('bpr') == 'ALL') {
                    $tabungan = $this->query_report_admin($awal, $akhir);
                } else {
                    $tabungan = $this->query_report_admin($awal, $akhir, Input::get('bpr'));
                }
            }
            return View::make('dashboard.tabungan.laporan_view_admin')
                    ->with("ctlUserData", $user)
                    ->with('tabungans', $tabungan)
                    ->with("ctlNavMenu", "mCollLaporan");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function report_download_admin()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $userId = Session::get('SESSION_USER_ID', '');
            $user = DB::table('coll_user')->where('U_ID',$userId)->first();
            if (Input::get('type') == 'admin') {
                empty(Input::get('laporanTglAwal')) ? ($awal = date('Y-m') . '-01') : ($awal = date_format(date_create(Input::get('laporanTglAwal')), 'Y-m-d'));
                empty(Input::get('laporanTglAkhir')) ? ($akhir = date('Y-m-d')) : ($akhir = date_format(date_create(Input::get('laporanTglAkhir')), 'Y-m-d'));
                if (Input::get('bpr') == 'ALL') {
                    $tabungan = $this->query_report_admin($awal, $akhir);
                } else {
                    $tabungan = $this->query_report_admin($awal, $akhir, Input::get('bpr'));
                }
            }
            Excel::create('Pintech Mobile App Report Tabungan', function($excel) use($user, $awal, $akhir, $tabungan) {
                $excel->setTitle('Pintech Mobile App System Report ' . $awal . " Sampai " . $akhir);
                $excel->setCreator('Pintech Mobile App System')->setCompany('PINTECH');
                $excel->setDescription('Laporan Tabungan');
                $excel->sheet('Sheet 1', function ($sheet) use ($user, $awal, $akhir, $tabungan) {
                    $sheet->setOrientation('landscape');
                    $sheet->setCellValue('N1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0, 0, 0, date('m'), date('d'), date('Y')) ));
                    $sheet->getStyle('N1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
                    $sheet->setCellValue('A1', "PINTECH MOBILE APP REPORT " . $awal . "-" . $akhir);
                    $sheet->mergeCells('A1:N1');
                    $sheet->getRowDimension('1')->setRowHeight(30);
                    $sheet->getColumnDimension('A')->setWidth(50);
                    $sheet->setCellValue('A2', "TANGGAL");
                    $sheet->getRowDimension('2')->setRowHeight(30);
                    $sheet->getColumnDimension('A')->setWidth(30);
                    $sheet->setCellValue('B2', "USER ID");
                    $sheet->getColumnDimension('B')->setWidth(35);
                    $sheet->setCellValue('C2', "NAMA COLLECTOR");
                    $sheet->getColumnDimension('C')->setWidth(30);
                    $sheet->setCellValue('D2', "KODE GROUP");
                    $sheet->getColumnDimension('D')->setWidth(30);
                    $sheet->setCellValue('E2', "CAB");
                    $sheet->getColumnDimension('E')->setWidth(30);
                    $sheet->setCellValue('F2', "NO REKENING");
                    $sheet->getColumnDimension('F')->setWidth(35);
                    $sheet->setCellValue('G2', "ID NASABAH");
                    $sheet->getColumnDimension('G')->setWidth(35);
                    $sheet->setCellValue('H2', "NAMA NASABAH");
                    $sheet->getColumnDimension('H')->setWidth(50);
                    $sheet->setCellValue('I2', "ALAMAT");
                    $sheet->getColumnDimension('I')->setWidth(70);
                    $sheet->setCellValue('J2', "HP");
                    $sheet->getColumnDimension('J')->setWidth(20);
                    $sheet->setCellValue('K2', "SETORAN");
                    $sheet->getColumnDimension('K')->setWidth(30);
                    $sheet->setCellValue('L2', "TANGGAL SETORAN");
                    $sheet->getColumnDimension('L')->setWidth(30);
                    $sheet->setCellValue('M2', "WAKTU");
                    $sheet->getColumnDimension('M')->setWidth(100);
                    $sheet->setCellValue('N2', "KETERANGAN");
                    $sheet->getColumnDimension('M')->setWidth(100);
                    $sheet->getStyle('A2:N2')->getFont()->setBold(true);
                    $row = 3;
                    $tgl = '';
                    foreach ($tabungan as $key => $value) {
                        if ($tgl == date('Y-m-d', strtotime($value->TGL_SETORAN))) {
                            $sheet->setCellValue('A' . $row, '');
                        } else {
                            $sheet->setCellValue('A' . $row, tglIndo(date('Y-m-d', strtotime($value->TGL_SETORAN)), "SHORT"));
                            $sheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                        }
                        $tgl = date('Y-m-d', strtotime($value->TGL_SETORAN));

                        /*  */
                        $sheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('B' . $row, $value->{"USERBIGID"});
                        $sheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('C' . $row, $value->{"U_NAMA"});
                        $sheet->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('D' . $row, $value->{"COLLECT_KODE"});
                        $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('E' . $row, $value->{"CAB"});
                        $sheet->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('F' . $row, $value->{"REK"});
                        $sheet->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('G' . $row, $value->{"CUST_ID"});
                        $sheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('H' . $row, $value->{"CUST_NAMA"});
                        $sheet->getStyle('I')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('I' . $row, $value->{"CUST_ALAMAT"});
                        $sheet->getStyle('J')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('J' . $row, $value->{"CUST_PONSEL"});
                        $sheet->getStyle('K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('K' . $row, $value->{"SETORAN"});
                        $sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('L' . $row, date('d-m-Y', strtotime($value->{"TGL_SETORAN"})));
                        $sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('M' . $row, date('H:i:s', strtotime($value->{"TGL_SETORAN"})));
                        $sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValue('N' . $row, $value->{"KETERANGAN"});
                        $row++;
                    }
                    $sheet->getStyle('A1:N1')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle('A2:N2')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'top' => [
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                            ],
                            'bottom' => [
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                            ],
                        ],
                        'fill' => [
                            'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startcolor' => [
                                'argb' => 'FFA0A0A0'
                            ],
                            'endcolor' => [
                                'argb' => 'FFFFFFFF'
                            ],
                        ],
                    ]);
                    $sheet->getStyle('A2')->applyFromArray([
                        'borders' => [
                            'left' => [
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                    $sheet->getStyle('N2')->applyFromArray([
                        'borders' => [
                            'right' => [
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                });
                $excel->setActiveSheetIndex(0);
            })->download('xlsx');
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function query_report($awal, $akhir, $prsh, $collect = null)
    {
        $tabungan = [];
        if (is_null($collect)) {
            $tabungan = DB::select('SELECT cth.TH_ID, ct.USERBIGID TARGET_BIGID, ct.USERID TARGET, ct.KODE_GROUP,
                cth.COLL_ID, cth.KODE_GROUP COLLECT_KODE, ct.REK, ct.CUST_ID, ct.CUST_NAMA,
                ct.CUST_PONSEL, ct.CUST_ALAMAT, cth.TGL_SETORAN, cth.SETORAN, cu.*, ct.CAB, cth.KETERANGAN
                FROM coll_tabungan_history cth
                INNER JOIN coll_tabungan ct ON ct.ID = cth.T_ID
                INNER JOIN coll_user cu ON cth.COLL_ID = cu.U_ID
                WHERE (DATE(cth.TGL_SETORAN) BETWEEN ? AND ?) AND cth.PRSH_ID = ?', [
                $awal,
                $akhir,
                $prsh,
            ]);
        } else {
            $tabungan = DB::select('SELECT cth.TH_ID, ct.USERBIGID TARGET_BIGID, ct.USERID TARGET, ct.KODE_GROUP,
                cth.COLL_ID, cth.KODE_GROUP COLLECT_KODE, ct.REK, ct.CUST_ID, ct.CUST_NAMA,
                ct.CUST_PONSEL, ct.CUST_ALAMAT, cth.TGL_SETORAN, cth.SETORAN, cu.*, ct.CAB, cth.KETERANGAN
                FROM coll_tabungan_history cth
                INNER JOIN coll_tabungan ct ON ct.ID = cth.T_ID
                INNER JOIN coll_user cu ON cth.COLL_ID = cu.U_ID
                WHERE (DATE(cth.TGL_SETORAN) BETWEEN ? AND ?) AND cth.PRSH_ID = ? AND cth.COLL_ID = ?', [
                $awal,
                $akhir,
                $prsh,
                $collect,
            ]);
        }
        return $tabungan;
    }

    public function query_report_admin($awal, $akhir, $prsh = null)
    {
        $tabungan = [];
        if (is_null($prsh)) {
            $tabungan = DB::select('SELECT cth.TH_ID, ct.USERBIGID TARGET_BIGID, ct.USERID TARGET, ct.KODE_GROUP,
                cth.COLL_ID, cth.KODE_GROUP COLLECT_KODE, ct.REK, ct.CUST_ID, ct.CUST_NAMA,
                ct.CUST_PONSEL, ct.CUST_ALAMAT, cth.TGL_SETORAN, cth.SETORAN, cu.*, ct.CAB, cth.KETERANGAN
                FROM coll_tabungan_history cth
                INNER JOIN coll_tabungan ct ON ct.ID = cth.T_ID
                INNER JOIN coll_user cu ON cth.COLL_ID = cu.U_ID
                WHERE (DATE(cth.TGL_SETORAN) BETWEEN ? AND ?)', [
                $awal,
                $akhir,
            ]);
        } else {
            $tabungan = DB::select('SELECT cth.TH_ID, ct.USERBIGID TARGET_BIGID, ct.USERID TARGET, ct.KODE_GROUP,
                cth.COLL_ID, cth.KODE_GROUP COLLECT_KODE, ct.REK, ct.CUST_ID, ct.CUST_NAMA,
                ct.CUST_PONSEL, ct.CUST_ALAMAT, cth.TGL_SETORAN, cth.SETORAN, cu.*, ct.CAB, cth.KETERANGAN
                FROM coll_tabungan_history cth
                INNER JOIN coll_tabungan ct ON ct.ID = cth.T_ID
                INNER JOIN coll_user cu ON cth.COLL_ID = cu.U_ID
                WHERE (DATE(cth.TGL_SETORAN) BETWEEN ? AND ?) AND cth.PRSH_ID = ?', [
                $awal,
                $akhir,
                $prsh,
            ]);
        }
        return $tabungan;
    }
}
