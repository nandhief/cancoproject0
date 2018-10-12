<?php
ini_set('max_execution_time', 300);
class CollectionController extends BaseController {
  public function redirectMissing() {
    Session::flush();
    return Redirect::to('login');
  }

  public function formlistJadwal() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      /**
       * Cek periode antara tanggal
       */
      if (!empty(Input::get("dari"))) {
        $dari = date_format(date_create(Input::get('dari')), 'Y-m-d');
      }
      if (!empty(Input::get("sampai"))) {
        $sampai = date_format(date_create(Input::get('sampai')), 'Y-m-d');
      }
      if (isset($dari) && isset($sampai)) {
        $uploads = DB::select("SELECT * FROM coll_batch_upload WHERE BU_TGL BETWEEN ? AND ? AND PRSH_ID = ? AND BU_TYPE = 'BU_JADWAL'", [$dari, $sampai, $userData->{"PRSH_ID"}]);
      } else {
        $uploads = DB::select("SELECT * FROM coll_batch_upload WHERE MONTH(BU_TGL) = ? AND YEAR(BU_TGL) = ? AND PRSH_ID = ? AND BU_TYPE = 'BU_JADWAL'", array($month, $year, $userData->{"PRSH_ID"}));
      }

      foreach ($uploads as $aData) {
        $data = DB::select("SELECT IFNULL(COUNT(BUD_ID),0) AS JUMLAH FROM coll_batch_upload_data WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_DATA"} = $data[0]->{"JUMLAH"};

        $tagihan = DB::select("SELECT IFNULL(SUM(BUD_PINJ_JUMLAH),0) AS JUMLAH FROM coll_batch_upload_data WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_TAGIHAN"} = $tagihan[0]->{"JUMLAH"};

        $bayar = DB::select("SELECT IFNULL(SUM(BUD_PINJ_JUMLAH_BAYAR),0) AS JUMLAH FROM coll_batch_upload_data WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_BAYAR"} = $bayar[0]->{"JUMLAH"};
      }

      return View::make("dashboard.collection.jadwal-formlist")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlUploads", $uploads)
        ->with("ctlNavMenu", "mCollJadwal");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function submitJadwal() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $date_now = date('Y-m-d');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();
      $dataTgl = DB::table('coll_batch_upload')->where('BU_TGL', $date_now)->where('BU_TYPE', 'BU_JADWAL')->where('U_ID', $userId)->first();
      // $dataTgl = DB::select("SELECT * FROM coll_batch_upload WHERE BU_TGL")

      if(isset($_FILES['jdwFile'])){
        $fileName = $_FILES['jdwFile']['name'];
        $fileSize = $_FILES['jdwFile']['size'];
        $fileTmp = $_FILES['jdwFile']['tmp_name'];
        $fileType = $_FILES['jdwFile']['type'];
        $a = explode(".", $_FILES["jdwFile"]["name"]);
        $fileExt = strtolower(end($a));

        $arrFileExt = array("xls","xlsx","XLS","XLSX");
        if(empty($dataTgl)) {
          if(isset($fileName) && trim($fileName) != "") {
            if(in_array($fileExt,$arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file Excel");
            if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");

            $uploadFile = "uploads/jadwal-".createSlug($userId)."-".date("YmdHis").".".$fileExt;
            if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
              DB::beginTransaction();

              $buId = DB::table("coll_batch_upload")->insertGetId(array(
                'BU_TGL' => date("Y-m-d"),
                'BU_FILE_PATH' => $uploadFile,
                'PRSH_ID' => $userData->{"PRSH_ID"},
                'U_ID' => $userData->{"U_ID"},
                'BU_TYPE' => 'BU_JADWAL'
              ));
              if(!isset($buId) || $buId <= 0) {
                DB::rollback();
                return composeReply("ERROR", "Proses penyimpanan data jadwal mengalami kegagalan");
              }

              $objPHPExcel = PHPExcel_IOFactory::load($uploadFile);
              $objWorksheet = $objPHPExcel->getActiveSheet();

              $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
              $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
              $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

              $titles = $objWorksheet->rangeToArray('A1:' . $highestColumn . "1");
              $body = $objWorksheet->rangeToArray('A2:' . $highestColumn . $highestRow);
              $table = array();
              //return composeReply("ERROR", "HIT HERE : ".$highestRow);
              for ($row=0; $row <= $highestRow - 2; $row++) {
                $a = array();
                for ($column=0; $column <= $highestColumnIndex - 1; $column++) {
                  //cek field sama atau tidak dengan data excel
                  if($column == 0 && $titles[0][$column] != "KODE_GROUP")   return composeReply("ERROR","Kolom ke-1 HARUS bernama KODE_GROUP");
                  if($column == 1 && $titles[0][$column] != "NO_REKENING") return composeReply("ERROR","Kolom ke-2 HARUS bernama NO_REKENING");
                  if($column == 2 && $titles[0][$column] != "CAB")    return composeReply("ERROR","Kolom ke-3 HARUS bernama CAB");
                  //$colomn = 2;
                  //if(isset($colomn)) return composeReply("ERROR", "Kolom NO_REKENING tidak boleh kosong");
                  if($column == 3 && $titles[0][$column] != "ID_NASABAH")    return composeReply("ERROR","Kolom ke-4 HARUS bernama ID_NASABAH");
                  if($column == 4 && $titles[0][$column] != "NAMA_NASABAH")           return composeReply("ERROR","Kolom ke-5 HARUS bernama NAMA_NASABAH");
                  if($column == 5 && $titles[0][$column] != "ALAMAT")         return composeReply("ERROR","Kolom ke-6 HARUS bernama ALAMAT");
                  if($column == 6 && $titles[0][$column] != "NO_HP")          return composeReply("ERROR","Kolom ke-7 HARUS bernama NO_HP");
                  if($column == 7 && $titles[0][$column] != "AGUNAN")        return composeReply("ERROR","Kolom ke-8 HARUS bernama AGUNAN");
                  if($column == 8 && $titles[0][$column] != "JML_PINJAMAN")        return composeReply("ERROR","Kolom ke-9 HARUS bernama JML_PINJAMAN");
                  if($column == 9 && $titles[0][$column] != "SALDO_NOMINATIF")        return composeReply("ERROR","Kolom ke-10 HARUS bernama SALDO_NOMINATIF");
                  if($column == 10 && $titles[0][$column] != "FP")    return composeReply("ERROR","Kolom ke-11 HARUS bernama FP");
                  if($column == 11 && $titles[0][$column] != "FB")     return composeReply("ERROR","Kolom ke-12 HARUS bernama FB");
                  if($column == 12 && $titles[0][$column] != "POKOK_BLN")       return composeReply("ERROR","Kolom ke-13 HARUS bernama POKOK_BLN");
                  if($column == 13 && $titles[0][$column] != "BUNGA_BLN")      return composeReply("ERROR","Kolom ke-14 HARUS bernama BUNGA_BLN");
                  if($column == 14 && $titles[0][$column] != "KOLEKTIBILITAS")      return composeReply("ERROR","Kolom ke-15 HARUS bernama KOLEKTIBILITAS");
                  if($column == 15 && $titles[0][$column] != "ANGSURAN_KE")      return composeReply("ERROR","Kolom ke-16 HARUS bernama  ANGSURAN_KE");
                  if($column == 16 && $titles[0][$column] != "JANGKA_WAKTU")         return composeReply("ERROR","Kolom ke-17 HARUS bernama JANGKA_WAKTU");
                  if($column == 17 && $titles[0][$column] != "TGL_REALISASI")         return composeReply("ERROR","Kolom ke-18 HARUS bernama TGL_REALISASI");
                  if($column == 18 && $titles[0][$column] != "TGL_UPLOAD")     return composeReply("ERROR","Kolom ke-19 HARUS bernama TGL_UPLOAD");
                  if($column == 19 && $titles[0][$column] != "TGL_JATUH_TEMPO")        return composeReply("ERROR","Kolom ke-20 HARUS bernama TGL_JATUH_TEMPO");
                  if($column == 20 && $titles[0][$column] != "TUNGG_POKOK")        return composeReply("ERROR","Kolom ke-21 HARUS bernama TUNGG_POKOK");
                  if($column == 21 && $titles[0][$column] != "TUNGG_BUNGA")        return composeReply("ERROR","Kolom ke-22 HARUS bernama TUNGG_BUNGA");
                  if($column == 22 && $titles[0][$column] != "TUNGG_DENDA")        return composeReply("ERROR","Kolom ke-23 HARUS bernama TUNGG_DENDA");
                  if($column == 23 && $titles[0][$column] != "TAGIHAN")        return composeReply("ERROR","Kolom ke-24 HARUS bernama TAGIHAN");
                  // if($column == 23 && $titles[0][$column] != "PEMBAYARAN") return composeReply("ERROR", "Kolom ke-24 HARUS bernama PEMBAYARAN");
                  // if($column == 24 && $titles[0][$column] != "TGL_BAYAR") return composeReply("ERROR", "Kolom ke-25 HARUS bernama TGL_BAYAR");
                  // if($column == 25 && $titles[0][$column] != "STATUS") return composeReply("ERROR", "Kolom ke-26 HARUS bernama STATUS");
                  // if($column == 26 && $titles[0][$column] != "KETERANGAN") return composeReply("ERROR", "Kolom ke-27 HARUS bernama KETERANGAN");
                  //end field if


                  $a[$titles[0][$column]] = $body[$row][$column];
                }
                $table[$row] = $a;
                if(isset($table[$row]) && trim($table[$row]["ID_NASABAH"]) !== "") {
                  //return composeReply("ERROR", "HIT : ".$table[$row]["ID_CUSTOMER"]);
                  $separator = "";
                  if(strpos($table[$row]["TGL_REALISASI"], "-") !== false) $separator = "-";
                  if(strpos($table[$row]["TGL_REALISASI"], "/") !== false) $separator = "/";

                  if($separator == "")  {
                    //return composeReply("ERROR", " ROW : ".$row);
                    break;
                  }

                  $arrTgl = explode($separator, $table[$row]["TGL_REALISASI"]); //menjadi 02-24-17
                  $tglKredit = "20".trim($arrTgl[2])."-".trim($arrTgl[0])."-".trim($arrTgl[1]);
                  //return composeReply("ERROR", "TGL_KREDIT : ".$tglKredit);
                  //new format tanggal
                  $var1 = trim($table[$row]["TGL_REALISASI"]);
                  $date1 = str_replace('/', '-', $var1);
                  $tglKredit1 = date("Y-m-d", strtotime($date1));

                  if(strpos($table[$row]["TGL_UPLOAD"], "-") !== false) $separator = "-";
                  if(strpos($table[$row]["TGL_UPLOAD"], "/") !== false) $separator = "/";
                  // $arrTgl = explode("-", $table[$row]["TGL_UPLOAD"]);
                  // $tglAngsur = "20".trim($arrTgl[2])."-".trim($arrTgl[0])."-".trim($arrTgl[1]);;
                  //new format tanggal
                  $var2 = trim($table[$row]["TGL_UPLOAD"]);
                  $date2 = str_replace('/', '-', $var2);
                  $tglAngsur1 = date("Y-m-d", strtotime($date2));

                  //dd($tglKredit1);


                  $pokokBulan = str_replace(",", "", $table[$row]["POKOK_BLN"]);
                  $pokokBulan = str_replace(".", "", $pokokBulan);

                  $pokokBunga = str_replace(",", "", $table[$row]["BUNGA_BLN"]);
                  $pokokBunga = str_replace(".", "", $pokokBunga);

                  $bayarPokok = str_replace(",", "", $table[$row]["TUNGG_POKOK"]);
                  $bayarPokok = str_replace(".", "", $bayarPokok);

                  $bayarJumlah = str_replace(",", "", $table[$row]["TAGIHAN"]);
                  $bayarJumlah = str_replace(".", "", $bayarJumlah);

                  $bayarBunga = str_replace(",", "", $table[$row]["TUNGG_BUNGA"]);
                  $bayarBunga = str_replace(".", "", $bayarBunga);

                  $bayarDenda = str_replace(",", "", $table[$row]["TUNGG_DENDA"]);
                  $bayarDenda = str_replace(".", "", $bayarDenda);

                  //cek dl apakah sdh ada user kolektor
                  // $kolektor = DB::table("coll_user")->where("U_KODE_GROUP",trim($table[$row]["KODE_GROUP"]))->first();

                  //cek dl apakah sdh ada data nasabah
                  $nasabah = DB::table("coll_customers")->where("CUST_ID", trim($table[$row]["ID_NASABAH"]))->first();
                  if(count($nasabah) <= 0) {
                    DB::table("coll_customers")->insert(array(
                      'CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                      'CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                      'CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                      'CUST_PONSEL' => trim($table[$row]["NO_HP"])
                    ));
                  }
                  else { //cek apakah ada perubahan data
                    if(trim(strtoupper($nasabah->{"CUST_NAMA"})) != trim(strtoupper($table[$row]["NAMA_NASABAH"]))) {
                      DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["ID_NASABAH"]))->update(array(
                        'CUST_NAMA' => $table[$row]["NAMA_NASABAH"]
                      ));
                    }
                    if(trim(strtoupper($nasabah->{"CUST_ALAMAT"})) != trim(strtoupper($table[$row]["ALAMAT"]))) {
                      DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["ID_NASABAH"]))->update(array(
                        'CUST_ALAMAT' => $table[$row]["ALAMAT"]
                      ));
                    }
                    if(trim(strtoupper($nasabah->{"CUST_PONSEL"})) != trim(strtoupper($table[$row]["NO_HP"]))) {
                      DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["ID_NASABAH"]))->update(array(
                        'CUST_PONSEL' => $table[$row]["NO_HP"]
                      ));
                    }
                  }

                  //cek dl apakah sdh ada data pinjaman
                  $pinjaman = DB::table("coll_pinjaman")->where("PINJ_ID", trim($table[$row]["NO_REKENING"]))->first();
                  if(count($pinjaman) <= 0) {
                    DB::table("coll_pinjaman")->insert(array(
                      'PINJ_ID' => trim($table[$row]["NO_REKENING"]),
                      'CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                      'PINJ_JUMLAH' => trim($table[$row]["JML_PINJAMAN"]),
                      'PINJ_MASA_KREDIT' => trim($table[$row]["JANGKA_WAKTU"]),
                      'PINJ_TGL_KREDIT' => $tglKredit1
                    ));
                  }

                  //ambil data ID collector jika ada didatabase
                  $tKgroup = trim($table[$row]["KODE_GROUP"]);
                  $prshId = $userData->{"PRSH_ID"};

                  $collID = DB::table("coll_user")->where("U_KODE_GROUP", "=", $table[$row]["KODE_GROUP"])->first();
                  if (is_null($collID)) {
                    return composeReply("ERROR","KODE_GROUP " .  $tKgroup . ' Tidak Berkaitan dengan collector manapun');
                  }
                  if ($collID->PRSH_ID != $prshId) {
                    $collID = DB::table("coll_user")->whereNull("U_KODE_GROUP")->first();
                  }
                  $collect_id = $collID->{"U_ID"};
                  $coll_nama = $collID->{"U_NAMA"};

                  $budId = DB::table("coll_batch_upload_data")->insertGetId(array(
                    'BU_ID' => $buId,
                    'BUD_KODE_GROUP' => $table[$row]["KODE_GROUP"],
                    'BUD_COLL_U_ID' => $collect_id,
                    'BUD_CAB' => trim($table[$row]["CAB"]),
                    'BUD_PINJ_ID' => trim($table[$row]["NO_REKENING"]),
                    'BUD_PINJ_PERIODE' => trim($table[$row]["ANGSURAN_KE"]),
                    'BUD_CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                    'BUD_CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                    'BUD_CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                    'BUD_CUST_PONSEL' => trim($table[$row]["NO_HP"]),
                    'BUD_AGUNAN' => trim($table[$row]["AGUNAN"]),
                    'BUD_JML_PINJAMAN' => trim($table[$row]["JML_PINJAMAN"]),
                    'BUD_SALDO_NOMINATIF' => trim($table[$row]["SALDO_NOMINATIF"]),
                    'BUD_SISA_KREDIT' => '0',
                    'BUD_FP' => trim($table[$row]["FP"]),
                    'BUD_FB' => trim($table[$row]["FB"]),
                    'BUD_BLN_POKOK' => $pokokBulan,
                    'BUD_BLN_BUNGA' => $pokokBunga,
                    'BUD_KOLEKTIBILITAS' => trim($table[$row]["KOLEKTIBILITAS"]),
                    'BUD_PINJ_MASA_KREDIT' => trim($table[$row]["JANGKA_WAKTU"]),
                    'BUD_PINJ_TGL_KREDIT' => $tglKredit1,
                    'BUD_PINJ_TGL_ANGS' => $tglAngsur1,
                    'BUD_PINJ_TGL_JADWAL' => $tglAngsur1,
                    'BUD_TGL_DEPAN_JADWAL' => trim($table[$row]["TGL_JATUH_TEMPO"]),
                    'BUD_PINJ_POKOK' => $bayarPokok,
                    'BUD_PINJ_BUNGA' => $bayarBunga,
                    'BUD_PINJ_DENDA' => $bayarDenda,
                    'BUD_PINJ_JUMLAH' => $bayarJumlah,
                    'BUD_EDIT_POKOK' => '0',
                    'BUD_EDIT_BUNGA' => '0',
                    'BUD_EDIT_DENDA' => '0',
                    'BUD_PINJ_TGL_BAYAR' => "0000-00-00 00:00:00",
                    'BUD_PINJ_JUMLAH_BAYAR' => '',
                    'BUD_STATUS' => 'ST_JADWAL',
                    'BUD_LOKASI_LAT' => '0',
                    'BUD_LOKASI_LNG' => '0',
                    'PRSH_ID' => $userData->{"PRSH_ID"}
                  ));

                 //var_dump($ddd);
                  //dd($budId);
                  if(!isset($budId) || $budId <= 0) {
                    DB::rollback();
                    return composeReply("ERROR", "Proses penyimpanan data jadwal mengalami kegagalan");
                  }

                  //generate jadwal
                  //TO DO : more complex consideration
                  //misal : data cust id dan pinj id sama, tp periode beda
                  //for($i=0; $i<intval($table[$row]["JANGKA_WAKTU"]); $i++) {
                    //$aTgl = addDaysWithDate($tglAngsur,$i,'Y-m-d');
                    $aTgl = date('Y-m-d');
                    $cek = DB::table("coll_jadwal")
                      ->where("J_TGL", $tglAngsur1)
                      ->where("CUST_ID", trim($table[$row]["ID_NASABAH"]))
                      ->where("PINJ_ID", trim($table[$row]["NO_REKENING"]))
                      ->where("J_PINJ_JUMLAH", $bayarJumlah)
                      ->first();

                    if(count($cek) <= 0) {
                      $jId = DB::table("coll_jadwal")->insertGetId(array(
                        'J_TGL' => $tglAngsur1,
                        'BU_ID' => $buId,
                        'BUD_ID' => $budId,
                        'CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                        'PINJ_ID' => trim($table[$row]["NO_REKENING"]),
                        'J_PINJ_JUMLAH' => $bayarJumlah,
                        'J_PINJ_JUMLAH_BAYAR' => '0',
                        'J_STATUS' => 'ST_JADWAL',
                        'PRSH_ID' => $userData->{"PRSH_ID"},
                        'J_COLL_U_ID' => $collect_id
                      ));
                    }
                    else {
                      DB::table("coll_jadwal")
                        ->where("J_TGL", $aTgl)
                        ->where("CUST_ID", trim($table[$row]["ID_NASABAH"]))
                        ->where("PINJ_ID", trim($table[$row]["NO_REKENING"]))
                        ->where("J_PINJ_JUMLAH", $bayarJumlah)
                        ->update(array(
                            'BUD_ID' => $budId,
                            'BU_ID' => $budId
                          ));
                    }
                  //}
                }
              }
              DB::commit();
              return composeReply("SUCCESS", "Data telah disimpan");
              //return composeReply("SUCCESS", "Hasil : ".$table[0]["ID_COLLECTOR"]." - ".$table[0]["NAMA_COLLECTOR"]." - ".$tglKredit." - ".$tglAngsur." - ".$tglJadwal." - ".$bayarPokok." - ".$bayarJumlah);
            }
          }
          else {
            return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
          }
        } else {
          if(isset($fileName) && trim($fileName) != "") {
            if(in_array($fileExt,$arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file Excel");
            if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");

            $uploadFile = "uploads/jadwal-".createSlug($userId)."-".date("YmdHis").".".$fileExt;
            if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
              DB::beginTransaction();

              /* Update File Upload */
              DB::table("coll_batch_upload")->where('BU_ID', $dataTgl->BU_ID)->where('U_ID', $userId)->update(array(
                'BU_FILE_PATH' => $uploadFile,
              ));

              /* Delete data untuk diganti yang baru */
              DB::table('coll_batch_upload_data')->where('BU_ID', $dataTgl->BU_ID)->delete();

              $objPHPExcel = PHPExcel_IOFactory::load($uploadFile);
              $objWorksheet = $objPHPExcel->getActiveSheet();

              $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
              $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
              $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

              $titles = $objWorksheet->rangeToArray('A1:' . $highestColumn . "1");
              $body = $objWorksheet->rangeToArray('A2:' . $highestColumn . $highestRow);
              $table = array();
              for ($row=0; $row <= $highestRow - 2; $row++) {
                $a = array();
                for ($column=0; $column <= $highestColumnIndex - 1; $column++) {
                  //cek field sama atau tidak dengan data excel
                  if($column == 0 && $titles[0][$column] != "KODE_GROUP")   return composeReply("ERROR","Kolom ke-1 HARUS bernama KODE_GROUP");
                  if($column == 1 && $titles[0][$column] != "NO_REKENING") return composeReply("ERROR","Kolom ke-2 HARUS bernama NO_REKENING");
                  if($column == 2 && $titles[0][$column] != "CAB")    return composeReply("ERROR","Kolom ke-3 HARUS bernama CAB");
                  //$colomn = 2;
                  //if(isset($colomn)) return composeReply("ERROR", "Kolom NO_REKENING tidak boleh kosong");
                  if($column == 3 && $titles[0][$column] != "ID_NASABAH")    return composeReply("ERROR","Kolom ke-4 HARUS bernama ID_NASABAH");
                  if($column == 4 && $titles[0][$column] != "NAMA_NASABAH")           return composeReply("ERROR","Kolom ke-5 HARUS bernama NAMA_NASABAH");
                  if($column == 5 && $titles[0][$column] != "ALAMAT")         return composeReply("ERROR","Kolom ke-6 HARUS bernama ALAMAT");
                  if($column == 6 && $titles[0][$column] != "NO_HP")          return composeReply("ERROR","Kolom ke-7 HARUS bernama NO_HP");
                  if($column == 7 && $titles[0][$column] != "AGUNAN")        return composeReply("ERROR","Kolom ke-8 HARUS bernama AGUNAN");
                  if($column == 8 && $titles[0][$column] != "JML_PINJAMAN")        return composeReply("ERROR","Kolom ke-9 HARUS bernama JML_PINJAMAN");
                  if($column == 9 && $titles[0][$column] != "SALDO_NOMINATIF")        return composeReply("ERROR","Kolom ke-10 HARUS bernama SALDO_NOMINATIF");
                  if($column == 10 && $titles[0][$column] != "FP")    return composeReply("ERROR","Kolom ke-11 HARUS bernama FP");
                  if($column == 11 && $titles[0][$column] != "FB")     return composeReply("ERROR","Kolom ke-12 HARUS bernama FB");
                  if($column == 12 && $titles[0][$column] != "POKOK_BLN")       return composeReply("ERROR","Kolom ke-13 HARUS bernama POKOK_BLN");
                  if($column == 13 && $titles[0][$column] != "BUNGA_BLN")      return composeReply("ERROR","Kolom ke-14 HARUS bernama BUNGA_BLN");
                  if($column == 14 && $titles[0][$column] != "KOLEKTIBILITAS")      return composeReply("ERROR","Kolom ke-15 HARUS bernama KOLEKTIBILITAS");
                  if($column == 15 && $titles[0][$column] != "ANGSURAN_KE")      return composeReply("ERROR","Kolom ke-16 HARUS bernama  ANGSURAN_KE");
                  if($column == 16 && $titles[0][$column] != "JANGKA_WAKTU")         return composeReply("ERROR","Kolom ke-17 HARUS bernama JANGKA_WAKTU");
                  if($column == 17 && $titles[0][$column] != "TGL_REALISASI")         return composeReply("ERROR","Kolom ke-18 HARUS bernama TGL_REALISASI");
                  if($column == 18 && $titles[0][$column] != "TGL_UPLOAD")     return composeReply("ERROR","Kolom ke-19 HARUS bernama TGL_UPLOAD");
                  if($column == 19 && $titles[0][$column] != "TGL_JATUH_TEMPO")        return composeReply("ERROR","Kolom ke-20 HARUS bernama TGL_JATUH_TEMPO");
                  if($column == 20 && $titles[0][$column] != "TUNGG_POKOK")        return composeReply("ERROR","Kolom ke-21 HARUS bernama TUNGG_POKOK");
                  if($column == 21 && $titles[0][$column] != "TUNGG_BUNGA")        return composeReply("ERROR","Kolom ke-22 HARUS bernama TUNGG_BUNGA");
                  if($column == 22 && $titles[0][$column] != "TUNGG_DENDA")        return composeReply("ERROR","Kolom ke-23 HARUS bernama TUNGG_DENDA");
                  if($column == 23 && $titles[0][$column] != "TAGIHAN")        return composeReply("ERROR","Kolom ke-24 HARUS bernama TAGIHAN");


                  $a[$titles[0][$column]] = $body[$row][$column];
                }
                $table[$row] = $a;
                if(isset($table[$row]) && trim($table[$row]["ID_NASABAH"]) !== "") {
                  //return composeReply("ERROR", "HIT : ".$table[$row]["ID_CUSTOMER"]);
                  $separator = "";
                  if(strpos($table[$row]["TGL_REALISASI"], "-") !== false) $separator = "-";
                  if(strpos($table[$row]["TGL_REALISASI"], "/") !== false) $separator = "/";

                  if($separator == "")  {
                    //return composeReply("ERROR", " ROW : ".$row);
                    break;
                  }

                  $arrTgl = explode($separator, $table[$row]["TGL_REALISASI"]); //menjadi 02-24-17
                  $tglKredit = "20".trim($arrTgl[2])."-".trim($arrTgl[0])."-".trim($arrTgl[1]);

                  //new format tanggal
                  $var1 = trim($table[$row]["TGL_REALISASI"]);
                  $date1 = str_replace('/', '-', $var1);
                  $tglKredit1 = date("Y-m-d", strtotime($date1));

                  if(strpos($table[$row]["TGL_UPLOAD"], "-") !== false) $separator = "-";
                  if(strpos($table[$row]["TGL_UPLOAD"], "/") !== false) $separator = "/";
                  // $arrTgl = explode("-", $table[$row]["TGL_UPLOAD"]);
                  // $tglAngsur = "20".trim($arrTgl[2])."-".trim($arrTgl[0])."-".trim($arrTgl[1]);
                  //new format tanggal
                  $var2 = trim($table[$row]["TGL_UPLOAD"]);
                  $date2 = str_replace('/', '-', $var2);
                  $tglAngsur1 = date("Y-m-d", strtotime($date2));

                  $pokokBulan = str_replace(",", "", $table[$row]["POKOK_BLN"]);
                  $pokokBulan = str_replace(".", "", $pokokBulan);

                  $pokokBunga = str_replace(",", "", $table[$row]["BUNGA_BLN"]);
                  $pokokBunga = str_replace(".", "", $pokokBunga);

                  $bayarPokok = str_replace(",", "", $table[$row]["TUNGG_POKOK"]);
                  $bayarPokok = str_replace(".", "", $bayarPokok);

                  $bayarJumlah = str_replace(",", "", $table[$row]["TAGIHAN"]);
                  $bayarJumlah = str_replace(".", "", $bayarJumlah);

                  $bayarBunga = str_replace(",", "", $table[$row]["TUNGG_BUNGA"]);
                  $bayarBunga = str_replace(".", "", $bayarBunga);

                  $bayarDenda = str_replace(",", "", $table[$row]["TUNGG_DENDA"]);
                  $bayarDenda = str_replace(".", "", $bayarDenda);

                  //cek dl apakah sdh ada data nasabah
                  $nasabah = DB::table("coll_customers")->where("CUST_ID", trim($table[$row]["ID_NASABAH"]))->first();
                  if(count($nasabah) <= 0) {
                    DB::table("coll_customers")->insert(array(
                      'CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                      'CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                      'CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                      'CUST_PONSEL' => trim($table[$row]["NO_HP"])
                    ));
                  }
                  else {
                    //cek apakah ada perubahan data
                    if(trim(strtoupper($nasabah->{"CUST_NAMA"})) != trim(strtoupper($table[$row]["NAMA_NASABAH"]))) {
                      DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["ID_NASABAH"]))->update(array(
                        'CUST_NAMA' => $table[$row]["NAMA_NASABAH"]
                      ));
                    }
                    if(trim(strtoupper($nasabah->{"CUST_ALAMAT"})) != trim(strtoupper($table[$row]["ALAMAT"]))) {
                      DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["ID_NASABAH"]))->update(array(
                        'CUST_ALAMAT' => $table[$row]["ALAMAT"]
                      ));
                    }
                    if(trim(strtoupper($nasabah->{"CUST_PONSEL"})) != trim(strtoupper($table[$row]["NO_HP"]))) {
                      DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["ID_NASABAH"]))->update(array(
                        'CUST_PONSEL' => $table[$row]["NO_HP"]
                      ));
                    }
                  }

                  //cek dl apakah sdh ada data pinjaman
                  $pinjaman = DB::table("coll_pinjaman")->where("PINJ_ID", trim($table[$row]["NO_REKENING"]))->first();
                  if(count($pinjaman) <= 0) {
                    DB::table("coll_pinjaman")->insert(array(
                      'PINJ_ID' => trim($table[$row]["NO_REKENING"]),
                      'CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                      'PINJ_JUMLAH' => trim($table[$row]["JML_PINJAMAN"]),
                      'PINJ_MASA_KREDIT' => trim($table[$row]["JANGKA_WAKTU"]),
                      'PINJ_TGL_KREDIT' => $tglKredit1
                    ));
                  }

                  //ambil data ID collector jika ada didatabase
                  $tKgroup = trim($table[$row]["KODE_GROUP"]);
                  $prshId = $userData->{"PRSH_ID"};

                  $collID = DB::table("coll_user")->where("U_KODE_GROUP", "=", $table[$row]["KODE_GROUP"])->first();
                  if (is_null($collID)) {
                    return composeReply("ERROR","KODE_GROUP " .  $tKgroup . ' Tidak Berkaitan dengan collector manapun');
                  }
                  if ($collID->PRSH_ID != $prshId) {
                    $collID = DB::table("coll_user")->whereNull("U_KODE_GROUP")->first();
                  }
                  $collect_id = $collID->{"U_ID"};
                  $coll_nama = $collID->{"U_NAMA"};

                  $budId = DB::table("coll_batch_upload_data")->insertGetId(array(
                    'BU_ID' => $dataTgl->BU_ID,
                    'BUD_KODE_GROUP' => $table[$row]["KODE_GROUP"],
                    'BUD_COLL_U_ID' => $collect_id,
                    'BUD_CAB' => trim($table[$row]["CAB"]),
                    'BUD_PINJ_ID' => trim($table[$row]["NO_REKENING"]),
                    'BUD_PINJ_PERIODE' => trim($table[$row]["ANGSURAN_KE"]),
                    'BUD_CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                    'BUD_CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                    'BUD_CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                    'BUD_CUST_PONSEL' => trim($table[$row]["NO_HP"]),
                    'BUD_AGUNAN' => trim($table[$row]["AGUNAN"]),
                    'BUD_JML_PINJAMAN' => trim($table[$row]["JML_PINJAMAN"]),
                    'BUD_SALDO_NOMINATIF' => trim($table[$row]["SALDO_NOMINATIF"]),
                    'BUD_SISA_KREDIT' => '0',
                    'BUD_FP' => trim($table[$row]["FP"]),
                    'BUD_FB' => trim($table[$row]["FB"]),
                    'BUD_BLN_POKOK' => $pokokBulan,
                    'BUD_BLN_BUNGA' => $pokokBunga,
                    'BUD_KOLEKTIBILITAS' => trim($table[$row]["KOLEKTIBILITAS"]),
                    'BUD_PINJ_MASA_KREDIT' => trim($table[$row]["JANGKA_WAKTU"]),
                    'BUD_PINJ_TGL_KREDIT' => $tglKredit1,
                    'BUD_PINJ_TGL_ANGS' => $tglAngsur1,
                    'BUD_PINJ_TGL_JADWAL' => $tglAngsur1,
                    'BUD_TGL_DEPAN_JADWAL' => trim($table[$row]["TGL_JATUH_TEMPO"]),
                    'BUD_PINJ_POKOK' => $bayarPokok,
                    'BUD_PINJ_BUNGA' => $bayarBunga,
                    'BUD_PINJ_DENDA' => $bayarDenda,
                    'BUD_PINJ_JUMLAH' => $bayarJumlah,
                    'BUD_EDIT_POKOK' => '0',
                    'BUD_EDIT_BUNGA' => '0',
                    'BUD_EDIT_DENDA' => '0',
                    'BUD_PINJ_TGL_BAYAR' => "0000-00-00 00:00:00",
                    'BUD_PINJ_JUMLAH_BAYAR' => '',
                    'BUD_STATUS' => 'ST_JADWAL',
                    'BUD_LOKASI_LAT' => '0',
                    'BUD_LOKASI_LNG' => '0',
                    'PRSH_ID' => $userData->{"PRSH_ID"}
                  ));

                 //var_dump($ddd);
                  //dd($budId);
                  if(!isset($budId) || $budId <= 0) {
                    DB::rollback();
                    return composeReply("ERROR", "Proses penyimpanan data jadwal mengalami kegagalan");
                  }

                  //generate jadwal
                  //TO DO : more complex consideration
                  //misal : data cust id dan pinj id sama, tp periode beda
                  //for($i=0; $i<intval($table[$row]["JANGKA_WAKTU"]); $i++) {
                    //$aTgl = addDaysWithDate($tglAngsur,$i,'Y-m-d');
                    $aTgl = date('Y-m-d');
                    $cek = DB::table("coll_jadwal")
                      ->where("J_TGL", $tglAngsur1)
                      ->where("CUST_ID", trim($table[$row]["ID_NASABAH"]))
                      ->where("PINJ_ID", trim($table[$row]["NO_REKENING"]))
                      ->where("J_PINJ_JUMLAH", $bayarJumlah)
                      ->first();

                    if(count($cek) <= 0) {
                      $jId = DB::table("coll_jadwal")->insertGetId(array(
                        'J_TGL' => $tglAngsur1,
                        'BU_ID' => $dataTgl->BU_ID,
                        'BUD_ID' => $budId,
                        'CUST_ID' => trim($table[$row]["ID_NASABAH"]),
                        'PINJ_ID' => trim($table[$row]["NO_REKENING"]),
                        'J_PINJ_JUMLAH' => $bayarJumlah,
                        'J_PINJ_JUMLAH_BAYAR' => '0',
                        'J_STATUS' => 'ST_JADWAL',
                        'PRSH_ID' => $userData->{"PRSH_ID"},
                        'J_COLL_U_ID' => $collect_id
                      ));
                    }
                    else {
                      DB::table("coll_jadwal")
                        ->where("J_TGL", $aTgl)
                        ->where("CUST_ID", trim($table[$row]["ID_NASABAH"]))
                        ->where("PINJ_ID", trim($table[$row]["NO_REKENING"]))
                        ->where("J_PINJ_JUMLAH", $bayarJumlah)
                        ->update(array(
                            'BUD_ID' => $budId,
                            'BU_ID' => $dataTgl->BU_ID
                          ));
                    }
                  //}
                }
              }
              DB::commit();
              return composeReply("SUCCESS", "Data telah disimpan dan data telah diperbarui dengan upload terbaru");
              //return composeReply("SUCCESS", "Hasil : ".$table[0]["ID_COLLECTOR"]." - ".$table[0]["NAMA_COLLECTOR"]." - ".$tglKredit." - ".$tglAngsur." - ".$tglJadwal." - ".$bayarPokok." - ".$bayarJumlah);
            }
          }
          else {
            return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
          }
          // return composeReply("ERROR", "Proses upload hanya bisa satu kali dalam sehari");
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

  public function listJadwalAdmin() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      /**
       * Cek periode antara tanggal
       */
      if (!empty(Input::get("dari"))) {
        $dari = date_format(date_create(Input::get('dari')), 'Y-m-d');
      }
      if (!empty(Input::get("sampai"))) {
        $sampai = date_format(date_create(Input::get('sampai')), 'Y-m-d');
      }
      if (isset($dari) && isset($sampai)) {
	    $uploads = DB::select("SELECT * FROM coll_batch_upload AS A INNER JOIN coll_perusahaan AS B ON A.PRSH_ID = B.PRSH_ID  WHERE A.BU_TGL BETWEEN ? AND ? AND A.BU_TYPE = 'BU_JADWAL'", [$dari, $sampai]);
      } else {
		  $uploads = DB::select("SELECT * FROM coll_batch_upload AS A INNER JOIN coll_perusahaan AS B ON A.PRSH_ID = B.PRSH_ID  WHERE MONTH(A.BU_TGL) = ? AND YEAR(A.BU_TGL) = ? AND A.BU_TYPE = 'BU_JADWAL'", array($month, $year));
      }

      foreach ($uploads as $aData) {
        $data = DB::select("SELECT IFNULL(COUNT(BUD_ID),0) AS JUMLAH FROM coll_batch_upload_data WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_DATA"} = $data[0]->{"JUMLAH"};

        $tagihan = DB::select("SELECT IFNULL(SUM(BUD_PINJ_JUMLAH),0) AS JUMLAH FROM coll_batch_upload_data WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_TAGIHAN"} = $tagihan[0]->{"JUMLAH"};

        $bayar = DB::select("SELECT IFNULL(SUM(BUD_PINJ_JUMLAH_BAYAR),0) AS JUMLAH FROM coll_batch_upload_data WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_BAYAR"} = $bayar[0]->{"JUMLAH"};
      }

      return View::make("dashboard.collection.jadwal-admin")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlUploads", $uploads)
        ->with("ctlNavMenu", "mCollJadwal");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  //controller tabungan
  public function formlistTabungan() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      /**
       * Cek periode antara tanggal
       */
      if (!empty(Input::get("dari"))) {
        $dari = date_format(date_create(Input::get('dari')), 'Y-m-d');
      }
      if (!empty(Input::get("sampai"))) {
        $sampai = date_format(date_create(Input::get('sampai')), 'Y-m-d');
      }
      if (isset($dari) && isset($sampai)) {
        $uploads = DB::select("SELECT * FROM coll_batch_upload WHERE BU_TGL BETWEEN ? AND ? AND PRSH_ID = ? AND BU_TYPE = 'BU_TABUNGAN'", [$dari, $sampai, $userData->{"PRSH_ID"}]);
      } else {
        $uploads = DB::select("SELECT * FROM coll_batch_upload WHERE MONTH(BU_TGL) = ? AND YEAR(BU_TGL) = ? AND PRSH_ID = ? AND BU_TYPE = 'BU_TABUNGAN'", array($month, $year, $userData->{"PRSH_ID"}));
      }

        $uploads = DB::select("SELECT * FROM coll_batch_upload WHERE MONTH(BU_TGL) = ? AND YEAR(BU_TGL) = ? AND PRSH_ID = ? AND BU_TYPE = 'BU_TABUNGAN'", array($month, $year, $userData->{"PRSH_ID"}));
      foreach ($uploads as $aData) {
        $data = DB::select("SELECT IFNULL(COUNT(BT_ID),0) AS JUMLAH FROM coll_batch_tabungan WHERE BU_ID = ?", array($aData->{"BU_ID"}));
        $aData->{"JUMLAH_DATA"} = $data[0]->{"JUMLAH"};
      }

      return View::make("dashboard.collection.jadwal-tabungan")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlUploads", $uploads)
        ->with("ctlNavMenu", "mCollTabungan");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function listTabunganAdmin() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      // $uploads = DB::select("SELECT * FROM coll_batch_upload AS A INNER JOIN coll_perusahaan AS B ON A.PRSH_ID = B.PRSH_ID  WHERE MONTH(A.BU_TGL) = ? AND YEAR(A.BU_TGL) = ? AND A.BU_TYPE = 'BU_TABUNGAN'", array($month, $year));

      /**
       * Cek periode antara tanggal
       */
      if (!empty(Input::get("dari"))) {
        $dari = date_format(date_create(Input::get('dari')), 'Y-m-d');
      }
      if (!empty(Input::get("sampai"))) {
        $sampai = date_format(date_create(Input::get('sampai')), 'Y-m-d');
      }
      if (isset($dari) && isset($sampai)) {
      $uploads = DB::select("SELECT * FROM coll_batch_upload AS A INNER JOIN coll_perusahaan AS B ON A.PRSH_ID = B.PRSH_ID  WHERE A.BU_TGL BETWEEN ? AND ? AND A.BU_TYPE = 'BU_TABUNGAN'", [$dari, $sampai]);
      } else {
      $uploads = DB::select("SELECT * FROM coll_batch_upload AS A INNER JOIN coll_perusahaan AS B ON A.PRSH_ID = B.PRSH_ID  WHERE MONTH(A.BU_TGL) = ? AND YEAR(A.BU_TGL) = ? AND A.BU_TYPE = 'BU_TABUNGAN'", array($month, $year));
      }

      foreach ($uploads as $aData) {
        if($aData->{"BU_ID"} == "") {
          $data = DB::select("SELECT IFNULL(COUNT(BT_ID),0) AS JUMLAH FROM coll_batch_tabungan");
          $aData->{"JUMLAH_DATA"} = $data[0]->{"JUMLAH"};
        } else {
          $data = DB::select("SELECT IFNULL(COUNT(BT_ID),0) AS JUMLAH FROM coll_batch_tabungan WHERE BU_ID = ?", array($aData->{"BU_ID"}));
          $aData->{"JUMLAH_DATA"} = $data[0]->{"JUMLAH"};
        }
      }

      return View::make("dashboard.collection.admin-tabungan")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlUploads", $uploads)
        ->with("ctlNavMenu", "mCollTabungan");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function submitTabungan() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $date_now = date('Y-m-d');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();
      $dataTgl = DB::table('coll_batch_upload')->where('BU_TGL', $date_now)->where('BU_TYPE', 'BU_TABUNGAN')->where('U_ID',$userId)->first();

      if(isset($_FILES['jdwFile'])){
        $fileName = $_FILES['jdwFile']['name'];
        $fileSize = $_FILES['jdwFile']['size'];
        $fileTmp = $_FILES['jdwFile']['tmp_name'];
        $fileType = $_FILES['jdwFile']['type'];
        $a = explode(".", $_FILES["jdwFile"]["name"]);
        $fileExt = strtolower(end($a));

        $arrFileExt = array("xls","xlsx","XLS","XLSX");
        if(empty($dataTgl)) {
            if(isset($fileName) && trim($fileName) != "") {
              if(in_array($fileExt,$arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file Excel");
              if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");

              $uploadFile = "uploads/tabungan-".createSlug($userId)."-".date("YmdHis").".".$fileExt;
              if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
                DB::beginTransaction();

                $buId = DB::table("coll_batch_upload")->insertGetId(array(
                  'BU_TGL' => date("Y-m-d"),
                  'BU_FILE_PATH' => $uploadFile,
                  'PRSH_ID' => $userData->{"PRSH_ID"},
                  'U_ID' => $userData->{"U_ID"},
                  'BU_TYPE' => 'BU_TABUNGAN'
                ));
                if(!isset($buId) || $buId <= 0) {
                  DB::rollback();
                  return composeReply("ERROR", "Proses penyimpanan data tabungan mengalami kegagalan");
                }

                $objPHPExcel = PHPExcel_IOFactory::load($uploadFile);
                $objWorksheet = $objPHPExcel->getActiveSheet();

                $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

                $titles = $objWorksheet->rangeToArray('A1:' . $highestColumn . "1");
                $body = $objWorksheet->rangeToArray('A2:' . $highestColumn . $highestRow);
                $table = array();
                //return composeReply("ERROR", "HIT HERE : ".$highestRow);
                for ($row=0; $row <= $highestRow - 2; $row++) {
                  $a = array();
                  for ($column=0; $column <= $highestColumnIndex - 1; $column++) {
                    //cek field sama atau tidak dengan data excel
                    if($column == 0 && $titles[0][$column] != "KODE_GROUP") return composeReply("ERROR","Kolom ke-1 HARUS bernama KODE_GROUP");
                    if($column == 1 && $titles[0][$column] != "CAB") return composeReply("ERROR","Kolom ke-3 HARUS bernama CAB");
                    if($column == 2 && $titles[0][$column] != "NO_REKENING") return composeReply("ERROR","Kolom ke-2 HARUS bernama NO_REKENING");
                    //$colomn = 2;
                    //if(isset($colomn)) return composeReply("ERROR", "Kolom NO_REKENING tidak boleh kosong");
                    if($column == 3 && $titles[0][$column] != "NASABAH_ID") return composeReply("ERROR","Kolom ke-4 HARUS bernama ID_NASABAH");
                    if($column == 4 && $titles[0][$column] != "NAMA_NASABAH") return composeReply("ERROR","Kolom ke-5 HARUS bernama NAMA_NASABAH");
                    if($column == 5 && $titles[0][$column] != "ALAMAT") return composeReply("ERROR","Kolom ke-6 HARUS bernama ALAMAT");
                    if($column == 6 && $titles[0][$column] != "TGL_REGISTRASI") return composeReply("ERROR","Kolom ke-7 HARUS bernama TGL_REGISTRASI");
                    if($column == 7 && $titles[0][$column] != "TGL_UPLOAD")  return composeReply("ERROR","Kolom ke-8 HARUS bernama TGL_UPLOAD");
                    if($column == 8 && $titles[0][$column] != "SALDO_AWAL")  return composeReply("ERROR","Kolom ke-9 HARUS bernama SALDO_AWAL");
                    if($column == 9 && $titles[0][$column] != "SETOR_MINIMUM") return composeReply("ERROR","Kolom ke-10 HARUS bernama SETOR_MINIMUM");
                    if($column == 10 && $titles[0][$column] != "SALDO_MINIMUM") return composeReply("ERROR","Kolom ke-11 HARUS bernama SALDO_MINIMUM");

                    $a[$titles[0][$column]] = $body[$row][$column];
                  }
                  $table[$row] = $a;
                  if(isset($table[$row]) && trim($table[$row]["NASABAH_ID"]) !== "") {
                    //return composeReply("ERROR", "HIT : ".$table[$row]["ID_CUSTOMER"]);

                    //new format tanggal
                    $var1 = trim($table[$row]["TGL_REGISTRASI"]);
                    $date1 = str_replace('/', '-', $var1);
                    $tglRegis1 = date("Y-m-d", strtotime($date1));
                    //dd($tglRegis1);


                    // function con2mysql($date) {
                    //   $date = explode("-",$date);
                    //   if ($date[0]<=9) { $date[0]="0".$date[0]; }
                    //   if ($date[1]<=9) { $date[1]="0".$date[1]; }
                    //   $date = array($date[2], $date[1], $date[0]);

                    //  return $n_date=implode("-", $date);
                    //  }

                    $date = trim($table[$row]["TGL_UPLOAD"]);
                    // $dateTime = new DateTime($date);
                    $formatted_date = date("Y-m-d", strtotime(str_replace('/', '-', $date)));
                    //dd($formatted_date);

                    //dd($tglKredit1);
                    $pokokBulan = str_replace(",", "", $table[$row]["SALDO_AWAL"]);
                    $pokokBulan = str_replace(".", "", $pokokBulan);
                    //dd($pokokBulan);

                    $pokokBunga = str_replace(",", "", $table[$row]["SETOR_MINIMUM"]);
                    $pokokBunga = str_replace(".", "", $pokokBunga);

                    $bayarPokok = str_replace(",", "", $table[$row]["SALDO_MINIMUM"]);
                    $bayarPokok = str_replace(".", "", $bayarPokok);

                    //cek dl apakah sdh ada user kolektor
                    $kolektor = DB::table("coll_user")->where("U_KODE_GROUP",trim($table[$row]["KODE_GROUP"]))->first();
                    if(isset($kolektor)) {
                      $cekColl = $kolektor->{"U_ID"};
                    }

                   // dd($cekColl);
                    //cek dl apakah sdh ada data nasabah
                    $nasabah = DB::table("coll_customers")->where("CUST_ID", trim($table[$row]["NASABAH_ID"]))->first();
                    //dd($nasabah);
                    if(count($nasabah) <= 0) {
                      DB::table("coll_customers")->insert(array(
                        'CUST_ID' => trim($table[$row]["NASABAH_ID"]),
                        'CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                        'CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                        'CUST_SALDO' => trim($table[$row]["SALDO_AWAL"])
                      ));
                    } else {
                      //cek apakah ada perubahan data
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
                      // if(trim(strtoupper($nasabah->{"CUST_PONSEL"})) != trim(strtoupper($table[$row]["NO_HP"]))) {
                      //   DB::table("coll_customers")->where("CUST_ID",trim($table[$row]["NASABAH_ID"]))->update(array(
                      //     'CUST_PONSEL' => $table[$row]["NO_HP"]
                      //   ));
                      // }
                    }

                    //ambil data ID collector jika ada didatabase
                    $prshId = $userData->{"PRSH_ID"};
                    //dd($prshId);


                    //dd($collect_id);
                    //dd($inputArray);
                    $budId = DB::table("coll_batch_tabungan")->insertGetId(array(
                      'BU_ID' => $buId,
                      'BT_KODE_GROUP' => trim($table[$row]["KODE_GROUP"]),
                      'BT_COLL_ID' => $cekColl,
                      'BT_CAB' => trim($table[$row]["CAB"]),
                      'BT_NO_REKENING' => trim($table[$row]["NO_REKENING"]),
                      'BT_NASABAH_ID' => trim($table[$row]["NASABAH_ID"]),
                      'BT_NASABAH_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                      'BT_ALAMAT' => trim($table[$row]["ALAMAT"]),
                      'TGL_REGISTRASI' => $tglRegis1,
                      'TGL_UPLOAD' => $formatted_date,
                      'BT_SALDO_AWAL' => $pokokBulan,
                      'BT_SALDO_AKHIR' => '0',
                      'BT_SALDO_MINIMUM' => $pokokBunga,
                      'BT_SETOR_MINIMUM' => $bayarPokok,
                      'BT_SETORAN' => '0',
                      'TGL_SETORAN' => "0000-00-00 00:00:00",
                      'BT_STATUS' => 'BT_JADWAL',
                      'BT_KETERANGAN' => '-',
                      'BT_LAT' => '-',
                      'BT_LONG' => '-',
                      'PRSH_ID' => $prshId
                    ));
                    //dd($budId);
                    if(!isset($budId) || $budId <= 0) {
                      DB::rollback();
                      return composeReply("ERROR", "Proses penyimpanan data jadwal mengalami kegagalan");
                    }
                  }
                }
                DB::commit();
                return composeReply("SUCCESS", "Data Tabungan telah disimpan");
                //return composeReply("SUCCESS", "Hasil : ".$table[0]["ID_COLLECTOR"]." - ".$table[0]["NAMA_COLLECTOR"]." - ".$tglKredit." - ".$tglAngsur." - ".$tglJadwal." - ".$bayarPokok." - ".$bayarJumlah);
              }
            } else {
              return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
            }
        } else {
          if(isset($fileName) && trim($fileName) != "") {
              if(in_array($fileExt,$arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file Excel");
              if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");

              $uploadFile = "uploads/tabungan-".createSlug($userId)."-".date("YmdHis").".".$fileExt;
              if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
                DB::beginTransaction();

                /* Update File Upload */
                DB::table("coll_batch_upload")->where('BU_ID', $dataTgl->BU_ID)->update(array(
                  'BU_FILE_PATH' => $uploadFile,
                ));

                /* Delete data untuk diganti yang baru */
                DB::table('coll_batch_tabungan')->where('BU_ID', $dataTgl->BU_ID)->delete();

                $objPHPExcel = PHPExcel_IOFactory::load($uploadFile);
                $objWorksheet = $objPHPExcel->getActiveSheet();

                $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

                $titles = $objWorksheet->rangeToArray('A1:' . $highestColumn . "1");
                $body = $objWorksheet->rangeToArray('A2:' . $highestColumn . $highestRow);
                $table = array();
                for ($row=0; $row <= $highestRow - 2; $row++) {
                  $a = array();
                  for ($column=0; $column <= $highestColumnIndex - 1; $column++) {
                    //cek field sama atau tidak dengan data excel
                    if($column == 0 && $titles[0][$column] != "KODE_GROUP") return composeReply("ERROR","Kolom ke-1 HARUS bernama KODE_GROUP");
                    if($column == 1 && $titles[0][$column] != "CAB") return composeReply("ERROR","Kolom ke-3 HARUS bernama CAB");
                    if($column == 2 && $titles[0][$column] != "NO_REKENING") return composeReply("ERROR","Kolom ke-2 HARUS bernama NO_REKENING");
                    if($column == 3 && $titles[0][$column] != "NASABAH_ID") return composeReply("ERROR","Kolom ke-4 HARUS bernama ID_NASABAH");
                    if($column == 4 && $titles[0][$column] != "NAMA_NASABAH") return composeReply("ERROR","Kolom ke-5 HARUS bernama NAMA_NASABAH");
                    if($column == 5 && $titles[0][$column] != "ALAMAT") return composeReply("ERROR","Kolom ke-6 HARUS bernama ALAMAT");
                    if($column == 6 && $titles[0][$column] != "TGL_REGISTRASI") return composeReply("ERROR","Kolom ke-7 HARUS bernama TGL_REGISTRASI");
                    if($column == 7 && $titles[0][$column] != "TGL_UPLOAD")  return composeReply("ERROR","Kolom ke-8 HARUS bernama TGL_UPLOAD");
                    if($column == 8 && $titles[0][$column] != "SALDO_AWAL")  return composeReply("ERROR","Kolom ke-9 HARUS bernama SALDO_AWAL");
                    if($column == 9 && $titles[0][$column] != "SETOR_MINIMUM") return composeReply("ERROR","Kolom ke-10 HARUS bernama SETOR_MINIMUM");
                    if($column == 10 && $titles[0][$column] != "SALDO_MINIMUM") return composeReply("ERROR","Kolom ke-11 HARUS bernama SALDO_MINIMUM");

                    $a[$titles[0][$column]] = $body[$row][$column];
                  }
                  $table[$row] = $a;
                  if(isset($table[$row]) && trim($table[$row]["NASABAH_ID"]) !== "") {

                    //new format tanggal
                    $var1 = trim($table[$row]["TGL_REGISTRASI"]);
                    $date1 = str_replace('/', '-', $var1);
                    $tglRegis1 = date("Y-m-d", strtotime($date1));

                    $date = trim($table[$row]["TGL_UPLOAD"]);
                    $formatted_date = date("Y-m-d", strtotime(str_replace('/', '-', $date)));

                    $pokokBulan = str_replace(",", "", $table[$row]["SALDO_AWAL"]);
                    $pokokBulan = str_replace(".", "", $pokokBulan);

                    $pokokBunga = str_replace(",", "", $table[$row]["SETOR_MINIMUM"]);
                    $pokokBunga = str_replace(".", "", $pokokBunga);

                    $bayarPokok = str_replace(",", "", $table[$row]["SALDO_MINIMUM"]);
                    $bayarPokok = str_replace(".", "", $bayarPokok);

                    //cek dl apakah sdh ada user kolektor
                    $kolektor = DB::table("coll_user")->where("U_KODE_GROUP",trim($table[$row]["KODE_GROUP"]))->first();
                    if(isset($kolektor)) {
                      $cekColl = $kolektor->{"U_ID"};
                    }

                    //cek dl apakah sdh ada data nasabah
                    $nasabah = DB::table("coll_customers")->where("CUST_ID", trim($table[$row]["NASABAH_ID"]))->first();

                    if(count($nasabah) <= 0) {
                      DB::table("coll_customers")->insert(array(
                        'CUST_ID' => trim($table[$row]["NASABAH_ID"]),
                        'CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                        'CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                        'CUST_SALDO' => trim($table[$row]["SALDO_AWAL"])
                      ));
                    } else {
                      //cek apakah ada perubahan data
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

                    //ambil data ID collector jika ada didatabase
                    $prshId = $userData->{"PRSH_ID"};

                    $budId = DB::table("coll_batch_tabungan")->insertGetId(array(
                      'BU_ID' => $dataTgl->BU_ID,
                      'BT_KODE_GROUP' => trim($table[$row]["KODE_GROUP"]),
                      'BT_COLL_ID' => $cekColl,
                      'BT_CAB' => trim($table[$row]["CAB"]),
                      'BT_NO_REKENING' => trim($table[$row]["NO_REKENING"]),
                      'BT_NASABAH_ID' => trim($table[$row]["NASABAH_ID"]),
                      'BT_NASABAH_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                      'BT_ALAMAT' => trim($table[$row]["ALAMAT"]),
                      'TGL_REGISTRASI' => $tglRegis1,
                      'TGL_UPLOAD' => $formatted_date,
                      'BT_SALDO_AWAL' => $pokokBulan,
                      'BT_SALDO_AKHIR' => '0',
                      'BT_SALDO_MINIMUM' => $pokokBunga,
                      'BT_SETOR_MINIMUM' => $bayarPokok,
                      'BT_SETORAN' => '0',
                      'TGL_SETORAN' => "0000-00-00 00:00:00",
                      'BT_STATUS' => 'BT_JADWAL',
                      'BT_KETERANGAN' => '-',
                      'BT_LAT' => '-',
                      'BT_LONG' => '-',
                      'PRSH_ID' => $prshId
                    ));

                    if(!isset($budId) || $budId <= 0) {
                      DB::rollback();
                      return composeReply("ERROR", "Proses penyimpanan data jadwal mengalami kegagalan");
                    }
                  }
                }
                DB::commit();
                return composeReply("SUCCESS", "Data Tabungan telah disimpan dan data telah diperbarui dengan upload terbaru");
              }
            } else {
              return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
            }
          //  return composeReply("ERROR", "Proses upload hanya bisa satu kali dalam sehari");
        }
      } else {
        return composeReply("ERROR","Harap sertakan file untuk diunggah");
      }
    }
    else {
      Session::flush();
      return composeReply("ERROR","Silahkan login terlebih dahulu");
    }
  }

  public function deleteTabungan() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(count($userData) <= 0) return composeReply("ERROR", "User tidak dikenal");

      if(null === Input::get("id") || trim(Input::get("id")) === "")  return composeReply("ERROR", "Parameter tidak lengkap");

      $bu = DB::table("coll_batch_upload")->where("BU_ID", Input::get("id"))->first();
      if(count($bu) <= 0) return composeReply("ERROR", "Data batch upload tidak dikenal");
      File::delete(asset_url()."/".$bu->{"BU_FILE_PATH"});

      $bud = DB::table("coll_batch_tabungan")
        ->where("BU_ID", Input::get("id"))
        ->where("BT_PATH_IMAGE", "!=", "-")
        ->get();
      foreach ($bud as $aData) {
        File::delete(asset_url()."/".$bud->{"BT_PATH_IMAGE"});
      }

      DB::table("coll_batch_upload")->where("BU_ID", Input::get("id"))->delete();
      DB::table("coll_batch_tabungan")->where("BU_ID", Input::get("id"))->delete();

      return composeReply("SUCCESS", "Data telah dihapus");
    }
    else {
      Session::flush();
      return composeReply("ERROR","Silahkan login terlebih dahulu");
    }
  }

  public function deleteJadwal() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(count($userData) <= 0) return composeReply("ERROR", "User tidak dikenal");

      if(null === Input::get("id") || trim(Input::get("id")) === "")  return composeReply("ERROR", "Parameter tidak lengkap");

      $bu = DB::table("coll_batch_upload")->where("BU_ID", Input::get("id"))->first();
      if(count($bu) <= 0) return composeReply("ERROR", "Data batch upload tidak dikenal");
      File::delete(asset_url()."/".$bu->{"BU_FILE_PATH"});

      $bud = DB::table("coll_batch_upload_data")
        ->where("BU_ID", Input::get("id"))
        ->where("BUD_IMG_PATH", "!=", "-")
        ->get();
      foreach ($bud as $aData) {
        if (isset($bud->{"BUD_IMG_PATH"})) {
          File::delete(asset_url()."/".$bud->{"BUD_IMG_PATH"});
        }
      }

      DB::table("coll_jadwal")->where("BU_ID", Input::get("id"))->delete();
      DB::table("coll_batch_upload_data")->where("BU_ID", Input::get("id"))->delete();
      DB::table("coll_batch_upload")->where("BU_ID", Input::get("id"))->delete();

      return composeReply("SUCCESS", "Data telah dihapus");
    }
    else {
      Session::flush();
      return composeReply("ERROR","Silahkan login terlebih dahulu");
    }
  }

  public function detailTabungan($budId) {
    // if(null === $buId1 || !isset($buId1)) {
    //   return Redirect::to("collection/tabungan");
    // }
    //dd($budId1);

    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();
      //dd($budId);

      $bud = DB::select("SELECT A.*, B.U_ID, B.U_NAMA, C.R_INFO FROM coll_batch_tabungan AS A INNER JOIN coll_user AS B ON A.BT_COLL_ID = B.U_ID INNER JOIN coll_referensi AS C ON A.BT_STATUS = C.R_ID WHERE A.BU_ID = ?", array($budId));
      //dd($bud);

      return View::make("dashboard.collection.detail-tabungan")
        ->with("ctlUserData", $userData)
        ->with("ctlBUD", $bud)
        ->with("ctlBuId", $budId)
        ->with("ctlNavMenu", "mCollTabungan");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function listDetailJadwal($buId) {
    if(null === $buId || !isset($buId)) {
      return Redirect::to("collection/jadwal-penagihan");
    }

    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      $bud = DB::select("SELECT A.*, B.U_ID, B.U_NAMA, C.R_INFO FROM coll_batch_upload_data AS A INNER JOIN coll_user AS B ON A.BUD_COLL_U_ID = B.U_ID INNER JOIN coll_referensi AS C ON A.BUD_STATUS = C.R_ID WHERE A.BU_ID = ?", array($buId));

      return View::make("dashboard.collection.jadwal-detail-list")
        ->with("ctlUserData", $userData)
        ->with("ctlBUD", $bud)
        ->with("ctlBuId", $buId)
        ->with("ctlNavMenu", "mCollJadwal");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  //monitoring admin
  public function displayMonitoringAdmin(){
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }
      $idUser = Session::get("SESSION_USER_ID");
      $userData = DB::table("coll_user")->where("U_ID", $idUser)->first();

     // $jadwalAdmin = DB::select("SELECT * FROM coll_batch_upload ORDER BY BU_ID DESC");
      $jadwalAdmin = DB::select("SELECT * FROM coll_batch_upload WHERE BU_TYPE = 'BU_JADWAL' ORDER BY BU_ID DESC");
      return View::make("dashboard.collection.monitor-display-admin")
        ->with("ctlUserData", $userData)
        ->with("ctlJadwal", $jadwalAdmin)
        ->with("ctlNavMenu", "mCollMonitoring");

    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function getPositionAdmin(){
    $reply = "";
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y-m-d");
        $periode_dmY = date("d-m-Y");
      }
      else {
        $periode = Input::get("periode");
        $arrTgl = explode("-", $periode);
        $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
      }

      if(null === Input::get("jadwal") || trim(Input::get("jadwal")) === "") {
        $latestBatchUpload = DB::select("SELECT BU_ID FROM coll_batch_upload WHERE BU_TGL <= ? ORDER BY BU_ID DESC LIMIT 0,1", array($periode));
        $buId = $latestBatchUpload[0]->{"BU_ID"};
      }
      else {
        $buId = Input::get("jadwal");
      }


      //baris brkt utk menampilkan SEMUA check in markers pd BU_ID yg latest
      $collRecords = DB::select("SELECT A.*, B.BU_TGL, C.U_NAMA FROM coll_batch_upload_data AS A INNER JOIN coll_batch_upload AS B ON A.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON A.BUD_COLL_U_ID = C.U_ID WHERE B.BU_ID = ? ORDER BY A.BU_ID,A.BUD_COLL_U_ID, A.BUD_STATUS_WAKTU", array($buId));

      $reply = "";
      $lastBatchUpload = "";
      $lastCollector = "";
      $lastWaktu = "";
      if(count($collRecords) > 0) {
        foreach ($collRecords as $aData) {
          //check in start
          //if($aData->{"BUD_STATUS"} != "ST_JADWAL") { //hanya tampilkan yg sdh ada action
          if($aData->{"BUD_LOKASI_LAT"} != "0" && $aData->{"BUD_LOKASI_LNG"} != "0") {
            if($lastBatchUpload == $aData->{"BU_ID"} && $lastCollector == $aData->{"BUD_COLL_U_ID"}) {
              //just skip
            }
            else {
              $checkInStart = DB::select("SELECT A.*,B.U_NAMA FROM coll_check_in_start AS A INNER JOIN coll_user AS B ON A.CIS_COLL_U_ID = B.U_ID WHERE A.CIS_COLL_U_ID = ? AND A.BU_ID = ? AND A.CIS_LOKASI_LAT != '0' AND A.CIS_LOKASI_LNG != '0' ORDER BY A.CIS_ID ASC LIMIT 0,1", array($aData->{"BUD_COLL_U_ID"}, $buId));

              if(count($checkInStart) > 0) {
                if($checkInStart[0]->{"CIS_LOKASI_LAT"} != "0" && $checkInStart[0]->{"CIS_LOKASI_LNG"} != "0") {
                  $reply .= "<marker ";
                  $reply .= 'CUST_NAMA="CHECK-IN START" ';
                  $reply .= 'COLL_ID="'.parseToXML($aData->{"BUD_COLL_U_ID"}).'" ';
                  $reply .= 'COLL_NAMA="'.parseToXML($aData->{"U_NAMA"}) . '" ';
                  $reply .= 'COLL_NAMA="'.parseToXML($aData->{"PRSH_ID"}) . '" ';
                  $reply .= 'COLL_STATUS="Check in start" ';
                  $reply .= 'COLL_STATUS_INFO="Check in start" ';
                  $reply .= 'COLL_POSISI_LAT="'.parseToXML($checkInStart[0]->{"CIS_LOKASI_LAT"}).'" ';
                  $reply .= 'COLL_POSISI_LNG="'.parseToXML($checkInStart[0]->{"CIS_LOKASI_LNG"}).'" ';
                  $reply .= 'COLL_STATUS_WAKTU="'.parseToXML(tglIndo($checkInStart[0]->{"CIS_WAKTU"},"SHORT")).'" ' ;
                  $reply .= 'BUD_ID="'.parseToXML($aData->{"BUD_ID"}).'" ' ;
                  $reply .= 'BU_ID="'.parseToXML($aData->{"BU_ID"}).'" ' ;
                  $reply .= 'TYPE="check-in_start" ';
                  $reply .= '/>';
                  $reply .= "\n";

                  $lastWaktu = $checkInStart[0]->{"CIS_WAKTU"};
                }
              }

              $lastBatchUpload = $aData->{"BU_ID"};
              $lastCollector = $aData->{"BUD_COLL_U_ID"};
            }

            if(isset($lastWaktu) && trim($lastWaktu) !== "") {
              $arrDiff = dayDifference2($lastWaktu, $aData->{"BUD_STATUS_WAKTU"}, false);
              $selisihWaktu = /*$lastWaktu." s.d ".$aData->{"BUD_STATUS_WAKTU"}." : ".*/$arrDiff["DAY"]." hari, ".$arrDiff["MONTH"]." bulan, ".$arrDiff["HOUR"]." jam, ".$arrDiff["MINUTE"]." menit";
            }
            $lastWaktu = $aData->{"BUD_STATUS_WAKTU"};

            $reply .= "<marker ";
            $reply .= 'CUST_NAMA="'.parseToXML($aData->{"BUD_CUST_NAMA"}) . '" ';
            $reply .= 'COLL_ID="'.parseToXML($aData->{"BUD_COLL_U_ID"}).'" ';
            $reply .= 'COLL_NAMA="'.parseToXML($aData->{"U_NAMA"}) . '" ';
            $reply .= 'COLL_STATUS="'.parseToXML($aData->{"BUD_STATUS"}) . '" ';
            $reply .= 'COLL_STATUS_INFO="'.parseToXML(getReferenceInfo("STATUS_COLLECTION",$aData->{"BUD_STATUS"})) . '" ';
            $reply .= 'COLL_POSISI_LAT="'.parseToXML($aData->{"BUD_LOKASI_LAT"}).'" ';
            $reply .= 'COLL_POSISI_LNG="'.parseToXML($aData->{"BUD_LOKASI_LNG"}).'" ';
            $reply .= 'COLL_STATUS_WAKTU="'.parseToXML(tglIndo($aData->{"BUD_STATUS_WAKTU"},"SHORT")).'" ' ;
            $reply .= 'BUD_ID="'.parseToXML($aData->{"BUD_ID"}).'" ' ;
            $reply .= 'BU_ID="'.parseToXML($aData->{"BU_ID"}).'" ' ;
            if(isset($selisihWaktu)) {
              $reply .= 'SELISIH="'.parseToXML($selisihWaktu).'" ';
            }
            $reply .= 'TYPE="collector" ';
            $reply .= '/>';
            $reply .= "\n";
          }
        }
      }

      if($reply != "")  $reply = "<markers>\n".$reply."</markers>";

      return Response::make($reply, '200')->header('Content-Type', 'text/xml');
    }
    else {
      Session::flush();
      return Response::make($reply, '200')->header('Content-Type', 'text/xml');
    }
  }

  public function displayMonitoring() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      //$jadwal = DB::select("SELECT * FROM coll_batch_upload WHERE PRSH_ID = ? AND MONTH(BU_TGL) = ? AND YEAR(BU_TGL) = ? ORDER BY BU_TGL DESC", array($userData->{"PRSH_ID"}, date("m"), date("Y")));
      $jadwal = DB::select("SELECT * FROM coll_batch_upload WHERE PRSH_ID = ? AND BU_TYPE = 'BU_JADWAL' ORDER BY BU_ID DESC", array($userData->{"PRSH_ID"}));

      return View::make("dashboard.collection.monitor-display")
        ->with("ctlUserData", $userData)
        ->with("ctlJadwal", $jadwal)
        ->with("ctlNavMenu", "mCollMonitoring");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function getPosition() {
    $reply = "";
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y-m-d");
        $periode_dmY = date("d-m-Y");
      }
      else {
        $periode = Input::get("periode");
        $arrTgl = explode("-", $periode);
        $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
      }

      if(null === Input::get("jadwal") || trim(Input::get("jadwal")) === "") {
        $latestBatchUpload = DB::select("SELECT BU_ID FROM coll_batch_upload WHERE PRSH_ID = ? AND BU_TGL <= ? ORDER BY BU_ID DESC LIMIT 0,1", array($userData->{"PRSH_ID"}, $periode));
        $buId = $latestBatchUpload[0]->{"BU_ID"};
      }
      else {
        $buId = Input::get("jadwal");
      }


      //baris brkt utk menampilkan SEMUA check in markers pd BU_ID yg latest
      $collRecords = DB::select("SELECT A.*, B.BU_TGL, C.U_NAMA FROM coll_batch_upload_data AS A INNER JOIN coll_batch_upload AS B ON A.BU_ID = B.BU_ID INNER JOIN coll_user AS C ON A.BUD_COLL_U_ID = C.U_ID WHERE B.BU_ID = ? ORDER BY A.BU_ID,A.BUD_COLL_U_ID, A.BUD_STATUS_WAKTU", array($buId));

      $reply = "";
      $lastBatchUpload = "";
      $lastCollector = "";
      $lastWaktu = "";
      if(count($collRecords) > 0) {
        foreach ($collRecords as $aData) {
          //check in start
          //if($aData->{"BUD_STATUS"} != "ST_JADWAL") { //hanya tampilkan yg sdh ada action
          if($aData->{"BUD_LOKASI_LAT"} != "0" && $aData->{"BUD_LOKASI_LNG"} != "0") {
            if($lastBatchUpload == $aData->{"BU_ID"} && $lastCollector == $aData->{"BUD_COLL_U_ID"}) {
              //just skip
            }
            else {
              $checkInStart = DB::select("SELECT A.*,B.U_NAMA FROM coll_check_in_start AS A INNER JOIN coll_user AS B ON A.CIS_COLL_U_ID = B.U_ID WHERE A.CIS_COLL_U_ID = ? AND A.BU_ID = ? AND A.CIS_LOKASI_LAT != '0' AND A.CIS_LOKASI_LNG != '0' ORDER BY A.CIS_ID ASC LIMIT 0,1", array($aData->{"BUD_COLL_U_ID"}, $buId));

              if(count($checkInStart) > 0) {
                if($checkInStart[0]->{"CIS_LOKASI_LAT"} != "0" && $checkInStart[0]->{"CIS_LOKASI_LNG"} != "0") {
                  $reply .= "<marker ";
                  $reply .= 'CUST_NAMA="CHECK-IN START" ';
                  $reply .= 'COLL_ID="'.parseToXML($aData->{"BUD_COLL_U_ID"}).'" ';
                  $reply .= 'COLL_NAMA="'.parseToXML($aData->{"U_NAMA"}) . '" ';
                  $reply .= 'COLL_STATUS="Check in start" ';
                  $reply .= 'COLL_STATUS_INFO="Check in start" ';
                  $reply .= 'COLL_POSISI_LAT="'.parseToXML($checkInStart[0]->{"CIS_LOKASI_LAT"}).'" ';
                  $reply .= 'COLL_POSISI_LNG="'.parseToXML($checkInStart[0]->{"CIS_LOKASI_LNG"}).'" ';
                  $reply .= 'COLL_STATUS_WAKTU="'.parseToXML(tglIndo($checkInStart[0]->{"CIS_WAKTU"},"SHORT")).'" ' ;
                  $reply .= 'BUD_ID="'.parseToXML($aData->{"BUD_ID"}).'" ' ;
                  $reply .= 'BU_ID="'.parseToXML($aData->{"BU_ID"}).'" ' ;
                  $reply .= 'TYPE="check-in_start" ';
                  $reply .= '/>';
                  $reply .= "\n";

                  $lastWaktu = $checkInStart[0]->{"CIS_WAKTU"};
                }
              }

              $lastBatchUpload = $aData->{"BU_ID"};
              $lastCollector = $aData->{"BUD_COLL_U_ID"};
            }

            if(isset($lastWaktu) && trim($lastWaktu) !== "") {
              $arrDiff = dayDifference2($lastWaktu, $aData->{"BUD_STATUS_WAKTU"}, false);
              $selisihWaktu = /*$lastWaktu." s.d ".$aData->{"BUD_STATUS_WAKTU"}." : ".*/$arrDiff["DAY"]." hari, ".$arrDiff["MONTH"]." bulan, ".$arrDiff["HOUR"]." jam, ".$arrDiff["MINUTE"]." menit";
            }
            $lastWaktu = $aData->{"BUD_STATUS_WAKTU"};

            $reply .= "<marker ";
            $reply .= 'CUST_NAMA="'.parseToXML($aData->{"BUD_CUST_NAMA"}) . '" ';
            $reply .= 'COLL_ID="'.parseToXML($aData->{"BUD_COLL_U_ID"}).'" ';
            $reply .= 'COLL_NAMA="'.parseToXML($aData->{"U_NAMA"}) . '" ';
            $reply .= 'COLL_STATUS="'.parseToXML($aData->{"BUD_STATUS"}) . '" ';
            $reply .= 'COLL_STATUS_INFO="'.parseToXML(getReferenceInfo("STATUS_COLLECTION",$aData->{"BUD_STATUS"})) . '" ';
            $reply .= 'COLL_POSISI_LAT="'.parseToXML($aData->{"BUD_LOKASI_LAT"}).'" ';
            $reply .= 'COLL_POSISI_LNG="'.parseToXML($aData->{"BUD_LOKASI_LNG"}).'" ';
            $reply .= 'COLL_STATUS_WAKTU="'.parseToXML(tglIndo($aData->{"BUD_STATUS_WAKTU"},"SHORT")).'" ' ;
            $reply .= 'BUD_ID="'.parseToXML($aData->{"BUD_ID"}).'" ' ;
            $reply .= 'BU_ID="'.parseToXML($aData->{"BU_ID"}).'" ' ;
            if(isset($selisihWaktu)) {
              $reply .= 'SELISIH="'.parseToXML($selisihWaktu).'" ';
            }
            $reply .= 'TYPE="collector" ';
            $reply .= '/>';
            $reply .= "\n";
          }
        }
      }

      if($reply != "")  $reply = "<markers>\n".$reply."</markers>";

      return Response::make($reply, '200')->header('Content-Type', 'text/xml');
    }
    else {
      Session::flush();
      return Response::make($reply, '200')->header('Content-Type', 'text/xml');
    }
  }

  public function formlistCollector() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }


      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      $userAll = DB::table("coll_user")->get();

      $jml = (count($userAll)) + 1;
      if($jml < 10) $jml = "11754664738267800".$jml;
      if($jml > 10 && $jml < 100) $jml = "1175466473826780".$jml;

      $collectors = DB::table("coll_user")
        ->where("U_GROUP_ROLE", "GR_COLLECTOR")
        ->where("PRSH_ID", $userData->{"PRSH_ID"})
        ->get();

      return View::make("dashboard.collection.collector-formlist")
        ->with("ctlUserData", $userData)
        ->with("ctlCollectors", $collectors)
        ->with("ctrlUrutan", $jml)
        ->with("ctlNavMenu", "mCollData");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function submitCollector() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $prshId = Session::get('SESSION_COMPANY_ID', '');
      $rule = 'GR_COLLECTOR';
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      //$userData1 = DB::table('coll_user')->join('coll_perusahaan', 'coll_user.PRSH_ID', '=', 'coll_perusahaan.PRSH_ID')
                    // ->where('PRSH_ID', $prshId)->where('U_GROUPA_ROLE', $rule)->firt();
     //$collMax = $userData1["PRSH_MAX_COLLECT"];

      if(isset($_FILES['collFile'])){
        $fileName = $_FILES['collFile']['name'];
        $fileSize = $_FILES['collFile']['size'];
        $fileTmp = $_FILES['collFile']['tmp_name'];
        $fileType = $_FILES['collFile']['type'];
        $a = explode(".", $_FILES["collFile"]["name"]);
        $fileExt = strtolower(end($a));

        $arrFileExt = array("xls","xlsx","XLS","XLSX");
        if(isset($fileName) && trim($fileName) != "") {
          if(in_array($fileExt,$arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file Excel");
          if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");

          $uploadFile = "uploads/collector-".createSlug($userId)."-".date("YmdHis").".".$fileExt;
          if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
            DB::beginTransaction();

            $objPHPExcel = PHPExcel_IOFactory::load($uploadFile);
            $objWorksheet = $objPHPExcel->getActiveSheet();

            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

            $titles = $objWorksheet->rangeToArray('A1:' . $highestColumn . "1");
            $body = $objWorksheet->rangeToArray('A2:' . $highestColumn . $highestRow);
            $table = array();

            for ($row=0; $row <= $highestRow - 2; $row++) {
              $a = array();
              for ($column=0; $column <= $highestColumnIndex - 1; $column++) {
                if($column == 0 && $titles[0][$column] != "USERID")   return composeReply("ERROR","Kolom ke-1 HARUS bernama USERID");
                if($column == 1 && $titles[0][$column] != "USERNAME")           return composeReply("ERROR","Kolom ke-2 HARUS bernama USERNAME");
                if($column == 2 && $titles[0][$column] != "NAMA")           return composeReply("ERROR","Kolom ke-3 HARUS bernama NAMA");
                if($column == 3 && $titles[0][$column] != "KODE_GROUP")           return composeReply("ERROR","Kolom ke-4 HARUS bernama KODE_GROUP");

                //if($colomn == 2 && $titles[0][$colomn] != "KODE_GROUP") return composeReply("ERROR", "Kolom ke-3 HARUS bernama KODE GROUP");

                $a[$titles[0][$column]] = $body[$row][$column];
              }
              $table[$row] = $a;
              if(isset($table[$row]) && trim($table[$row]["USERNAME"]) !== "") {
                //cek dl apakah sdh ada user kolektor
                $kolektor = DB::select("SELECT * FROM coll_user WHERE PRSH_ID = '$prshId' AND U_GROUP_ROLE = 'GR_COLLECTOR'");

                //cek userbigid collector dala satu perusahaan
                $idUser = trim($table[$row]["USERID"]);
                $role = 'GR_COLLECTOR';
                $cekUserId = DB::select("SELECT * FROM coll_user WHERE PRSH_ID = ? AND USERBIGID = ? AND U_GROUP_ROLE = ?", array($prshId, $idUser, $role));

                //dd($cekUserId);
                //$idPrsh = $kolektor->{"PRSH_ID"};
                $prsh = DB::table("coll_perusahaan")->where("PRSH_ID", $prshId)->first();
                $prshMax = $prsh->{"PRSH_MAX_COLLECT"};

                if(count($kolektor) < $prshMax) {
                  if(count($cekUserId) <= 0) {
                  $randomTelp = "089".randomDigits(10);
                    $randomEmail = strtolower(trim($table[$row]["USERNAME"]))."@".strtolower($userData->{"PRSH_ID"}).".com";
                    //dd($randomTelp);
                    DB::table("coll_user")->insert(array(
                      'U_ID' => trim($table[$row]["USERNAME"]),
                      'USERBIGID' =>  trim($table[$row]["USERID"]),
                      'U_PASSWORD' => getSetting("DEFAULT_PASSWORD"),
                      'U_PASSWORD_HASH' => md5(trim($table[$row]["USERNAME"]).getSetting("DEFAULT_PASSWORD")),
                      'U_NAMA' => $table[$row]["NAMA"],
                      'U_GROUP_ROLE' => 'GR_COLLECTOR',
                      'U_TELPON' => $randomTelp,
                      'U_EMAIL' => $randomEmail,
                      'U_KODE_GROUP' => trim($table[$row]["KODE_GROUP"]),
                      'U_STATUS' => 'USER_ACTIVE',
                      'U_REGISTRASI_TGL' => date("Y-m-d H:i:s"),
                      'U_NOTA_ID' => '0',
                      'PRSH_ID' => $userData->{"PRSH_ID"}
                    ));
                  } else {
                    return composeReply("ERROR", "User BigId sudah digunakan dalam satu perusahaan");
                  }
                } else {
                  return composeReply("ERROR", "Data melebihi batas Maxsimal Collector yang ditentukan");
                }
              }
            }
            DB::commit();
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

  public function listDetailCollector() {
    if(null === Input::get("id") || trim(Input::get("id")) === "") {
      return Redirect::to("collection/collector");
    }

    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      $collectorData = DB::table("coll_user")->where("U_ID", Input::get("id"))->first();
      if(count($collectorData) <= 0)  return Redirect::to("collection/collector");

      $collRecords = DB::select("SELECT A.*, B.BU_TGL FROM coll_batch_upload_data AS A INNER JOIN coll_batch_upload AS B ON A.BU_ID = B.BU_ID WHERE A.BUD_COLL_U_ID = ? AND MONTH(BU_TGL) = ? AND YEAR(BU_TGL) = ?", array(Input::get("id"), $month, $year));

      return View::make("dashboard.collection.collector-detail-list")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlCollectorData", $collectorData)
        ->with("ctlCollRecords", $collRecords)
        ->with("ctlNavMenu", "mCollData");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  //admin report
  public function adminReport() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      $collector = DB::table("coll_user")
        ->where("U_GROUP_ROLE", "GR_COLLECTOR")
        ->where("PRSH_ID", $userData->{"PRSH_ID"})
        ->get();
      $getPrsh = DB::table("coll_perusahaan")->get();

      return View::make("dashboard.collection.laporanAdmin-list")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlCollector", $collector)
        ->with("ctrlPrsh", $getPrsh)
        ->with("ctlNavMenu", "mCollLaporan");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function adminTabunganView()
  {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }
      function bulan($bulan)
          {
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

      $rptType = Input::get("tipe");
      if(!isset($rptType) || trim($rptType) === "") $rptType = "RPT_COLLECTING";

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      $prshData = Input::get("collector");
      $tgl_awal1 = Input::get("tglAwal1");
      $tAwal1 = date("Y-m-d", strtotime($tgl_awal1));

      $tgl_akhir1 = Input::get("tglAkhir1");
      $tAkhir1 = date("Y-m-d", strtotime($tgl_akhir1));

      //dd($tAwal1);

      if($rptType == "RPT_COLLECTING") {
        $bln = date("m");
        $thn = date("Y");
        if(null !== Input::get("bln") && trim(Input::get("bln")) !== "")  $bln = Input::get("bln");
        if(null !== Input::get("thn") && trim(Input::get("thn")) !== "")  $thn = Input::get("thn");

        if(null !== Input::get("periode") && trim(Input::get("periode")) !== "") {
          $arrPeriode = explode("-", Input::get("periode"));
          $bln = $arrPeriode[1];
          $thn = $arrPeriode[0];
        }

        //formalitas
        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".$daysOfMonth;
        $collector = "ALL";
      }

      if($rptType == "RPT_COLLECTING_QUERY") {
        $bln = date("m"); //formalitas
        $bln = bulan($bln);
        $thn = date("Y"); //formalitas

        $collector = "ALL";
        if(null !== Input::get("collector") && trim(Input::get("collector")) !== "")  $collector = Input::get("collector");

        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".getLastDate(date("m"), date("Y"));

        if(null !== Input::get("awal") && trim(Input::get("awal")) !== "")    $awal = Input::get("awal");
        if(null !== Input::get("akhir") && trim(Input::get("akhir")) !== "")  $akhir = Input::get("akhir");
      }
      if($collector == "ALL") {
        $tabungan = DB::table("coll_batch_tabungan")->join("coll_perusahaan", "coll_batch_tabungan.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->whereBetween("coll_batch_tabungan.TGL_UPLOAD", array($tAwal1, $tAkhir1))->get();
      } else {
        $tabungan = DB::table("coll_batch_tabungan")->join("coll_perusahaan", "coll_batch_tabungan.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->whereBetween("coll_batch_tabungan.TGL_UPLOAD", array($tAwal1, $tAkhir1))->where("coll_perusahaan.PRSH_ID", $prshData)->get();
      }
      // dd($tabungan);
      return View::make('dashboard.collection.laporanAdmin-tabunganlistview')
            ->with("ctlUserData", $userData)
            ->with('tabungan', $tabungan)
            ->with("ctlNavMenu", "mCollLaporan");
    } else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function adminTabunganDownload() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }
      function bulan($bulan)
          {
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

      $rptType = Input::get("tipe");
      if(!isset($rptType) || trim($rptType) === "") $rptType = "RPT_COLLECTING";

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      $prshData = Input::get("collector");
      $tgl_awal1 = Input::get("tglAwal1");
      $tAwal1 = date("Y-m-d", strtotime($tgl_awal1));

      $tgl_akhir1 = Input::get("tglAkhir1");
      $tAkhir1 = date("Y-m-d", strtotime($tgl_akhir1));

      //dd($tAwal1);

      if($rptType == "RPT_COLLECTING") {
        $bln = date("m");
        $thn = date("Y");
        if(null !== Input::get("bln") && trim(Input::get("bln")) !== "")  $bln = Input::get("bln");
        if(null !== Input::get("thn") && trim(Input::get("thn")) !== "")  $thn = Input::get("thn");

        if(null !== Input::get("periode") && trim(Input::get("periode")) !== "") {
          $arrPeriode = explode("-", Input::get("periode"));
          $bln = $arrPeriode[1];
          $thn = $arrPeriode[0];
        }

        //formalitas
        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".$daysOfMonth;
        $collector = "ALL";
      }

      if($rptType == "RPT_COLLECTING_QUERY") {
        $bln = date("m"); //formalitas
        $bln = bulan($bln);
        $thn = date("Y"); //formalitas

        $collector = "ALL";
        if(null !== Input::get("collector") && trim(Input::get("collector")) !== "")  $collector = Input::get("collector");

        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".getLastDate(date("m"), date("Y"));

        if(null !== Input::get("awal") && trim(Input::get("awal")) !== "")    $awal = Input::get("awal");
        if(null !== Input::get("akhir") && trim(Input::get("akhir")) !== "")  $akhir = Input::get("akhir");
      }

      //convert to excel
      Excel::create('Pintech Mobile App Report', function($excel) use($rptType, $userData, $bln, $thn, $awal, $akhir, $collector, $prshData, $tAwal1, $tAkhir1) {
        $rptName = getReferenceInfo("REPORT_TYPE", $rptType);
        // Set the title
        $excel->setTitle('Pintech Mobile App System Report '.$thn."_".$bln);
        $excel->setCreator('Pintech Mobile App System')->setCompany($userData->{"PRSH_ID"});
        $excel->setDescription('Laporan hasil collecting');

        $excel->sheet('Sheet 1', function ($sheet) use($rptType, $userData, $bln, $thn, $awal, $akhir, $collector, $prshData, $tAwal1, $tAkhir1) {
          $sheet->setOrientation('landscape');
          //$sheet->fromArray($data, NULL, 'A4');

          //$sheet->setCellValue('A1', "Tanggal Laporan");
          $sheet->setCellValue('M1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
          $sheet->getStyle('M1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);


          $sheet->setCellValue('A1', "PINTECH MOBILE APP REPORT TABUNGAN PER BULAN " .$bln. " " .$thn);
          $sheet->mergeCells('A1:AA1');
          $sheet->getRowDimension('1')->setRowHeight(30);
          $sheet->getColumnDimension('A')->setWidth(50);

          $sheet->setCellValue('A2', "TANGGAL");
          $sheet->getRowDimension('2')->setRowHeight(30);
          $sheet->getColumnDimension('A')->setWidth(30);
          $sheet->setCellValue('B2', "KODE GROUP");
          $sheet->getColumnDimension('B')->setWidth(35);
          $sheet->setCellValue('C2', "NAMA COLLECTOR");
          $sheet->getColumnDimension('C')->setWidth(30);
          $sheet->setCellValue('D2', "KODE CAB");
          $sheet->getColumnDimension('D')->setWidth(30);
          $sheet->setCellValue('E2', "NO REKENING");
          $sheet->getColumnDimension('E')->setWidth(30);
          $sheet->setCellValue('F2', "NASABAH ID");
          $sheet->getColumnDimension('F')->setWidth(35);
          $sheet->setCellValue('G2', "NAMA NASABAH");
          $sheet->getColumnDimension('G')->setWidth(35);
          $sheet->setCellValue('H2', "ALAMAT");
          $sheet->getColumnDimension('H')->setWidth(70);
          $sheet->setCellValue('I2', "TGL REGISTRASI");
          $sheet->getColumnDimension('I')->setWidth(70);
          $sheet->setCellValue('J2', "TGL UPLOAD");
          $sheet->getColumnDimension('J')->setWidth(20);
          $sheet->setCellValue('K2', "SALDO AWAL");
          $sheet->getColumnDimension('K')->setWidth(30);
          $sheet->setCellValue('L2', "SETOR MINIMUM");
          $sheet->getColumnDimension('L')->setWidth(30);
          $sheet->setCellValue('M2', "SALDO MINIMUM");
          $sheet->getColumnDimension('M')->setWidth(30);
          $sheet->setCellValue('N2', "STATUS");
          $sheet->getColumnDimension('N')->setWidth(25);
          $sheet->setCellValue('O2', "KETERANGAN");
          $sheet->getColumnDimension('O')->setWidth(25);
          $sheet->setCellValue('P2', "SETORAN");
          $sheet->getColumnDimension('P')->setWidth(35);
          $sheet->setCellValue('Q2', "TGL SETORAN");
          $sheet->getColumnDimension('Q')->setWidth(35);
          $sheet->setCellValue('R2', "JAM SETORAN");
          $sheet->getColumnDimension('R')->setWidth(20);
          $sheet->setCellValue('S2', "PETUGAS LAPANGAN");
          $sheet->getColumnDimension('S')->setWidth(20);

          $sheet->getStyle('A2:S2')->getFont()->setBold(true);

          $grandTotalBayar = 0;

          $xlsRow = 3;
          if($rptType == "RPT_COLLECTING") {
            $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
          }

          if($rptType == "RPT_COLLECTING_QUERY") {
            $arrDiff = dayDifference2($awal,$akhir,TRUE);
            $daysOfMonth = $arrDiff["DAY"] + 1;
          }

          for($i=1; $i<=$daysOfMonth; $i++) {
            if($rptType == "RPT_COLLECTING") {
              $aDate = $thn."-".$bln."-".$i;
              if(intval($aDate) < 10) $aDate = $thn."-".$bln."-0".$i;
            }

            if($rptType == "RPT_COLLECTING_QUERY") {
              $aDate = addDaysWithDate($awal,($i-1),'Y-m-d');
            }

            //dd($prshData);

            if($collector == "ALL") {
              $tabungan1 = DB::table("coll_batch_tabungan")->join("coll_perusahaan", "coll_batch_tabungan.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->whereBetween("coll_batch_tabungan.TGL_UPLOAD", array($tAwal1, $tAkhir1))->get();
            }
            else {
              $tabungan1 = DB::table("coll_batch_tabungan")->join("coll_perusahaan", "coll_batch_tabungan.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->whereBetween("coll_batch_tabungan.TGL_UPLOAD", array($tAwal1, $tAkhir1))->where("coll_perusahaan.PRSH_ID", $prshData)->get();
            }

            //dd($tabungan1);

            //
            $aDate1 = $tabungan1[0]->{"TGL_UPLOAD"};
            //dd($aDate1);


            $sheet->setCellValue('A'.$xlsRow, tglIndo($aDate1,"SHORT"));
            $sheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('A'.$xlsRow)->getFont()->setBold(true);

            $lastCollector = "";
            $jmlPokokPerCollector = 0;
            $jmlBungaPerCollector = 0;
            $jmlDendaPerCollector = 0;
            $jmlTagihanPerCollector = 0;
            $jmlBayarPerCollector = 0;
            $jmlTotalBayar = 0;

            $jmlPokokPerTgl = 0;
            $jmlBungaPerTgl = 0;
            $jmlDendaPerTgl = 0;
            $jmlTagihanPerTgl = 0;
            $jmlBayarPerTgl = 0;
            foreach ($tabungan1 as $aData) {

              //mulai start edit line setelah ini
              $sheet->setCellValue('B'.$xlsRow, $aData->{"BT_KODE_GROUP"});
              $sheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('C'.$xlsRow, $aData->{"BT_COLL_ID"});
              //$sheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('D'.$xlsRow, $aData->{"BT_CAB"});
              $sheet->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('E'.$xlsRow, $aData->{"BT_NO_REKENING"});
              $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('F'.$xlsRow, $aData->{"BT_NASABAH_ID"});
              $sheet->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('G'.$xlsRow, $aData->{"BT_NASABAH_NAMA"});
              $sheet->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('H'.$xlsRow, $aData->{"BT_ALAMAT"});
             // $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('I'.$xlsRow, $aData->{"TGL_REGISTRASI"});
              $sheet->setCellValue('J'.$xlsRow, $aData->{"TGL_UPLOAD"});
              $sheet->setCellValue('K'.$xlsRow, $aData->{"BT_SALDO_AWAL"});
              $sheet->setCellValue('L'.$xlsRow, $aData->{"BT_SETOR_MINIMUM"});
              $sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('M'.$xlsRow, $aData->{"BT_SALDO_MINIMUM"});
              $sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              if($aData->{"BT_STATUS"} == "BT_MENABUNG") {
                $sheet->setCellValue('N'.$xlsRow, 'Menabung');
              } elseif($aData->{"BT_STATUS"} == "BT_TIDAK_MENABUNG") {
                $sheet->setCellValue('N'.$xlsRow, 'Tidak Menabung');
              } else {
                $sheet->setCellValue('N'.$xlsRow, 'Dalam Penjadwalan');
              }
              $sheet->setCellValue('O'.$xlsRow, $aData->{"BT_KETERANGAN"});
              //$sheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('P'.$xlsRow, $aData->{"BT_SETORAN"});
              $sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Q'.$xlsRow, $aData->{"TGL_SETORAN"});
              $sheet->getStyle('Q')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('R'.$xlsRow, $aData->{"TGL_SETORAN"});
              //end new
              $sheet->setCellValue('S'.$xlsRow, $aData->{"BT_PETUGAS"});

              $xlsRow++;
            }

            $xlsRow++;
          }
          $sheet->mergeCells('A'.$xlsRow.':S'.$xlsRow);

          $sheet->getStyle("A".$xlsRow.":B".$xlsRow)->applyFromArray(array(
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
          ));

          $sheet->setCellValue('A'.$xlsRow, " T  O  T  A  L ");
          $sheet->getStyle('A'.$xlsRow.':AF'.$xlsRow)->getFont()->setBold(true);
          $sheet->setCellValue('AG'.$xlsRow, $grandTotalBayar);

          // Set style for header row using alternative method

           $sheet->getStyle('A1:S1')->applyFromArray(
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

          $sheet->getStyle('A2:S2')->applyFromArray(
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

          $sheet->getStyle('S2')->applyFromArray(
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

    } else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function adminReportView()
  {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }
      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();
      $prshData = Input::get("collector");
      $tgl_awal = Input::get("tglAwal");
      $tAwal = date("Y-m-d", strtotime($tgl_awal));
      $tgl_akhir = Input::get("tglAkhir");
      $tAkhir = date("Y-m-d", strtotime($tgl_akhir));
      if($prshData == "ALL") {
        $jadwal = DB::table("coll_batch_upload_data")->join("coll_perusahaan", "coll_batch_upload_data.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->join("coll_jadwal", "coll_batch_upload_data.BUD_ID", "=", "coll_jadwal.BUD_ID")->whereBetween("coll_jadwal.J_TGL", array($tAwal, $tAkhir ))->get();
      }
      else {
        $jadwal = DB::table("coll_batch_upload_data")->join("coll_perusahaan", "coll_batch_upload_data.PRSH_ID", "=", "coll_perusahaan.PRSH_ID")->join("coll_jadwal", "coll_batch_upload_data.BUD_ID", "=", "coll_jadwal.BUD_ID")->whereBetween("coll_jadwal.J_TGL", array($tAwal, $tAkhir ))->where("coll_batch_upload_data.PRSH_ID", $prshData)->get();
      }
      return View::make('dashboard.collection.laporanAdmin-listview')
            ->with("ctlUserData", $userData)
            ->with('jadwal', $jadwal)
            ->with("ctlNavMenu", "mCollLaporan");
    } else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function adminReportDownload() {
     if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      function bulan($bulan)
          {
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

      $rptType = Input::get("tipe");
      if(!isset($rptType) || trim($rptType) === "") $rptType = "RPT_COLLECTING";

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      $prshData = Input::get("collector");
      $tgl_awal = Input::get("tglAwal");
      $tAwal = date("Y-m-d", strtotime($tgl_awal));

      $tgl_akhir = Input::get("tglAkhir");
      $tAkhir = date("Y-m-d", strtotime($tgl_akhir));

      //$jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS,C.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_user AS C ON A.PRSH_ID = C.PRSH_ID WHERE (A.J_TGL BETWEEN '$tAwal' AND '$tAkhir') AND A.PRSH_ID = '$prshData' ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID");
            //}
      //echo "query ". $jadwal;

      // print_r($jadwal1);

      //****

      if($rptType == "RPT_COLLECTING") {
        $bln = date("m");
        $thn = date("Y");
        if(null !== Input::get("bln") && trim(Input::get("bln")) !== "")  $bln = Input::get("bln");
        if(null !== Input::get("thn") && trim(Input::get("thn")) !== "")  $thn = Input::get("thn");

        if(null !== Input::get("periode") && trim(Input::get("periode")) !== "") {
          $arrPeriode = explode("-", Input::get("periode"));
          $bln = $arrPeriode[1];
          $thn = $arrPeriode[0];
        }

        //formalitas
        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".$daysOfMonth;
        $collector = "ALL";
      }

      if($rptType == "RPT_COLLECTING_QUERY") {
        $bln = date("m"); //formalitas
        $bln = bulan($bln);
        $thn = date("Y"); //formalitas

        $collector = "ALL";
        if(null !== Input::get("collector") && trim(Input::get("collector")) !== "")  $collector = Input::get("collector");

        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".getLastDate(date("m"), date("Y"));

        if(null !== Input::get("awal") && trim(Input::get("awal")) !== "")    $awal = Input::get("awal");
        if(null !== Input::get("akhir") && trim(Input::get("akhir")) !== "")  $akhir = Input::get("akhir");
      }

      Excel::create('Pintech Mobile App Report', function($excel) use($rptType, $userData, $bln, $thn, $awal, $akhir, $collector, $prshData, $tAwal, $tAkhir) {
        $rptName = getReferenceInfo("REPORT_TYPE", $rptType);
        // Set the title
        $excel->setTitle('Pintech Mobile App System Report '.$thn."_".$bln);
        $excel->setCreator('Pintech Mobile App System')->setCompany($userData->{"PRSH_ID"});
        $excel->setDescription('Laporan hasil collecting');

        $excel->sheet('Sheet 1', function ($sheet) use($rptType, $userData, $bln, $thn, $awal, $akhir, $collector, $prshData, $tAwal, $tAkhir) {
          $sheet->setOrientation('landscape');
          //$sheet->fromArray($data, NULL, 'A4');

          //$sheet->setCellValue('A1', "Tanggal Laporan");
          $sheet->setCellValue('M1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
          $sheet->getStyle('M1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);


          $sheet->setCellValue('A1', "PINTECH MOBILE APP REPORT PER BULAN " .$bln. " " .$thn);
          $sheet->mergeCells('A1:AA1');
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
          $sheet->setCellValue('K2', "AGUNAN");
          $sheet->getColumnDimension('K')->setWidth(30);
          $sheet->setCellValue('L2', "JML PINJAMAN");
          $sheet->getColumnDimension('L')->setWidth(30);
          $sheet->setCellValue('M2', "SALDO NOMINATIF");
          $sheet->getColumnDimension('M')->setWidth(30);
          $sheet->setCellValue('N2', "FP");
          $sheet->getColumnDimension('N')->setWidth(25);
          $sheet->setCellValue('O2', "FB");
          $sheet->getColumnDimension('O')->setWidth(25);
          $sheet->setCellValue('P2', "POKOK/BLN");
          $sheet->getColumnDimension('P')->setWidth(35);
          $sheet->setCellValue('Q2', "BUNGA/BLN");
          $sheet->getColumnDimension('Q')->setWidth(35);
          $sheet->setCellValue('R2', "KOLEKTIBILITAS");
          $sheet->getColumnDimension('R')->setWidth(20);
          $sheet->setCellValue('S2', "ANGSURAN KE");
          $sheet->getColumnDimension('S')->setWidth(38);
          $sheet->setCellValue('T2', "JANGKA WAKTU");
          $sheet->getColumnDimension('T')->setWidth(35);
          $sheet->setCellValue('U2', "TGL REALISASI");
          $sheet->getColumnDimension('U')->setWidth(35);
          $sheet->setCellValue('V2', "TGL UPLOAD");
          $sheet->getColumnDimension('V')->setWidth(35);
          $sheet->setCellValue('W2', "TGL JADWAL");
          $sheet->getColumnDimension('W')->setWidth(35);
          $sheet->setCellValue('X2', "TUNGG POKOK");
          $sheet->getColumnDimension('X')->setWidth(40);
          $sheet->setCellValue('Y2', "TUNGG BUNGA");
          $sheet->getColumnDimension('Y')->setWidth(40);
          $sheet->setCellValue('Z2', "TUNGG DENDA");
          $sheet->getColumnDimension('Z')->setWidth(40);
          $sheet->setCellValue('AA2', "TAGIHAN");
          $sheet->getColumnDimension('AA')->setWidth(40);
          $sheet->setCellValue('AB2', "STATUS");
          $sheet->getColumnDimension('AB')->setWidth(40);
          $sheet->setCellValue('AC2', "KETERANGAN");
          $sheet->getColumnDimension('AC')->setWidth(35);
          $sheet->setCellValue('AD2', "BAYAR POKOK");
          $sheet->getColumnDimension('AD')->setWidth(35);
          $sheet->setCellValue('AE2', "BAYAR BUNGA");
          $sheet->getColumnDimension('AE')->setWidth(35);
          $sheet->setCellValue('AF2', "BAYAR DENDA");
          $sheet->getColumnDimension('AF')->setWidth(35);
          $sheet->setCellValue('AG2', "TOTAL BAYAR");
          $sheet->getColumnDimension('AG')->setWidth(35);
          $sheet->setCellValue('AH2', "TANGGAL BAYAR");
          $sheet->getColumnDimension('AH')->setWidth(35);
          $sheet->setCellValue('AI2', "JAM BAYAR");
          $sheet->getColumnDimension('AI')->setWidth(35);

          $sheet->getStyle('A2:AI2')->getFont()->setBold(true);

          $grandTotalBayar = 0;

          $xlsRow = 3;
          if($rptType == "RPT_COLLECTING") {
            $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
          }

          if($rptType == "RPT_COLLECTING_QUERY") {
            $arrDiff = dayDifference2($awal,$akhir,TRUE);
            $daysOfMonth = $arrDiff["DAY"] + 1;
          }

          for($i=1; $i<=$daysOfMonth; $i++) {
            if($rptType == "RPT_COLLECTING") {
              $aDate = $thn."-".$bln."-".$i;
              if(intval($aDate) < 10) $aDate = $thn."-".$bln."-0".$i;
            }

            if($rptType == "RPT_COLLECTING_QUERY") {
              $aDate = addDaysWithDate($awal,($i-1),'Y-m-d');
            }

            //dd($tAwal);

            if($collector == "ALL") {
              $jadwal1 = DB::table("coll_jadwal")->join("coll_batch_upload_data", "coll_jadwal.BUD_ID", "=", "coll_batch_upload_data.BUD_ID")->whereBetween("coll_jadwal.J_TGL", array($tAwal, $tAkhir ))->get();
            }
            else {
              $jadwal1 = DB::table("coll_jadwal")->join("coll_batch_upload_data", "coll_jadwal.BUD_ID", "=", "coll_batch_upload_data.BUD_ID")->whereBetween("coll_jadwal.J_TGL", array($tAwal, $tAkhir ))->where("coll_jadwal.PRSH_ID", $prshData)->get();
            }

            //
            $aDate1 = $jadwal1[0]->{"J_TGL"};
            // dd($jadwal1);


            $sheet->setCellValue('A'.$xlsRow, tglIndo($aDate1,"SHORT"));
            $sheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('A'.$xlsRow)->getFont()->setBold(true);

            $lastCollector = "";
            $jmlPokokPerCollector = 0;
            $jmlBungaPerCollector = 0;
            $jmlDendaPerCollector = 0;
            $jmlTagihanPerCollector = 0;
            $jmlBayarPerCollector = 0;
            $jmlTotalBayar = 0;

            $jmlPokokPerTgl = 0;
            $jmlBungaPerTgl = 0;
            $jmlDendaPerTgl = 0;
            $jmlTagihanPerTgl = 0;
            $jmlBayarPerTgl = 0;
            foreach ($jadwal1 as $aData) {
              if($lastCollector == "")  $lastCollector = $aData->{"J_COLL_U_ID"};
              if($aData->{"J_COLL_U_ID"}   != $lastCollector) {
                $sheet->mergeCells('A'.$xlsRow.':J'.$xlsRow);
                $sheet->setCellValue('W'.$xlsRow, "T O T A L");
                $sheet->getStyle('W'.$xlsRow.':Z'.$xlsRow)->getFont()->setBold(true);
                $sheet->setCellValue('X'.$xlsRow, $jmlPokokPerCollector);
                //$sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Y'.$xlsRow, $jmlBungaPerCollector);
                //$sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Z'.$xlsRow, $jmlDendaPerCollector);
                //$sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AA'.$xlsRow, $jmlTagihanPerCollector);
                //$sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AB'.$xlsRow, $jmlBayarPerCollector);
                //$sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

                $jmlPokokPerCollector = 0;
                $jmlBungaPerCollector = 0;
                $jmlDendaPerCollector = 0;
                $jmlTagihanPerCollector = 0;
                $jmlBayarPerCollector = 0;
                $jmlTotalBayar = 0;
                $lastCollector = $aData->{"J_COLL_U_ID"};
                $xlsRow++;
              }

              $jmlPokokPerCollector += $aData->{"BUD_PINJ_POKOK"};
              $jmlBungaPerCollector += $aData->{"BUD_PINJ_BUNGA"};
              $jmlDendaPerCollector += $aData->{"BUD_PINJ_DENDA"};
              $jmlTagihanPerCollector += $aData->{"BUD_PINJ_JUMLAH"};
              $jmlBayarPerCollector += $aData->{"J_PINJ_JUMLAH_BAYAR"};

              $jmlPokokPerTgl += $aData->{"BUD_PINJ_POKOK"};
              $jmlBungaPerTgl += $aData->{"BUD_PINJ_BUNGA"};
              $jmlDendaPerTgl += $aData->{"BUD_PINJ_DENDA"};
              $jmlTagihanPerTgl += $aData->{"BUD_PINJ_JUMLAH"};
              $jmlBayarPerTgl += $aData->{"J_PINJ_JUMLAH_BAYAR"};

              $grandTotalBayar += $aData->{"J_PINJ_JUMLAH_BAYAR"};

              //mulai start edit line setelah ini
              $sheet->setCellValue('B'.$xlsRow, $aData->{"J_ID"});
              $sheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('C'.$xlsRow, $aData->{"J_COLL_U_ID"});
              //$sheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('D'.$xlsRow, $aData->{"BUD_KODE_GROUP"});
              $sheet->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('E'.$xlsRow, $aData->{"BUD_CAB"});
              $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('F'.$xlsRow, $aData->{"BUD_PINJ_ID"});
              $sheet->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('G'.$xlsRow, $aData->{"BUD_CUST_ID"});
              $sheet->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('H'.$xlsRow, $aData->{"BUD_CUST_NAMA"});
             // $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('I'.$xlsRow, $aData->{"BUD_CUST_ALAMAT"});
              $sheet->setCellValue('J'.$xlsRow, $aData->{"BUD_CUST_PONSEL"});
              $sheet->setCellValue('K'.$xlsRow, $aData->{"BUD_AGUNAN"});
              $sheet->setCellValue('L'.$xlsRow, $aData->{"BUD_JML_PINJAMAN"});
              $sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('M'.$xlsRow, $aData->{"BUD_SALDO_NOMINATIF"});
              $sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('N'.$xlsRow, $aData->{"BUD_FP"});
              $sheet->setCellValue('O'.$xlsRow, $aData->{"BUD_FB"});
              //$sheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('P'.$xlsRow, $aData->{"BUD_BLN_POKOK"});
              $sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Q'.$xlsRow, $aData->{"BUD_BLN_BUNGA"});
              $sheet->getStyle('Q')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('R'.$xlsRow, $aData->{"BUD_KOLEKTIBILITAS"});
              //end new
              $sheet->setCellValue('S'.$xlsRow, $aData->{"BUD_PINJ_PERIODE"});
              $sheet->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('T'.$xlsRow, $aData->{"BUD_PINJ_MASA_KREDIT"});
              $sheet->getStyle('T')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('U'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_KREDIT"},"SHORT"));
             // $sheet->getStyle('U')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('V'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_JADWAL"},"SHORT"));
             // $sheet->getStyle('V')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('W'.$xlsRow, $aData->{"BUD_TGL_DEPAN_JADWAL"});
              //$sheet->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('X'.$xlsRow, $aData->{"BUD_PINJ_POKOK"});
              $sheet->getStyle('X')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Y'.$xlsRow, $aData->{"BUD_PINJ_BUNGA"});
              $sheet->getStyle('Y')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Z'.$xlsRow, $aData->{"BUD_PINJ_DENDA"});
              $sheet->getStyle('Z')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('AA'.$xlsRow, $aData->{"BUD_PINJ_JUMLAH"});
              $sheet->getStyle('AA')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


              if($aData->{"J_STATUS"} == "ST_BAYAR" || $aData->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
                $sheet->setCellValue('AB'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"},"SHORT"));
                $sheet->getStyle('AB')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              }
              else {
                $sheet->setCellValue('AB'.$xlsRow, "-");
              }

              $sheet->setCellValue('AB'.$xlsRow, getReferenceInfo("STATUS_COLLECTION",$aData->{"J_STATUS"}));
              //$sheet->getStyle('R')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('AC'.$xlsRow, $aData->{"BUD_KETERANGAN"});

              $sheet->setCellValue('AD'.$xlsRow, $aData->{"BUD_EDIT_POKOK"});
              $sheet->getStyle('AD')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AE'.$xlsRow, $aData->{"BUD_EDIT_BUNGA"});
              $sheet->getStyle('AE')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AF'.$xlsRow, $aData->{"BUD_EDIT_DENDA"});
              $sheet->getStyle('AF')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AG'.$xlsRow, $aData->{"J_PINJ_JUMLAH_BAYAR"});
              $sheet->getStyle('AG')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AH'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"}, "LONG"));

              $sheet->setCellValue('AI'.$xlsRow, date("H:i:s", strtotime($aData->{"BUD_PINJ_TGL_BAYAR"})));

              $xlsRow++;
            }
            //last row per collector
            $sheet->mergeCells('A'.$xlsRow.':J'.$xlsRow);
            $sheet->setCellValue('W'.$xlsRow, "T O T A L");
            $sheet->getStyle('X'.$xlsRow.':AA'.$xlsRow)->getFont()->setBold(true);
            $sheet->setCellValue('X'.$xlsRow, $jmlPokokPerCollector);
            //$sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Y'.$xlsRow, $jmlBungaPerCollector);
            //$sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Z'.$xlsRow, $jmlDendaPerCollector);
            //$sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AA'.$xlsRow, $jmlTagihanPerCollector);
            //$sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AG'.$xlsRow, $jmlBayarPerCollector);
            //$sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            $jmlPokokPerCollector = 0;
            $jmlBungaPerCollector = 0;
            $jmlDendaPerCollector = 0;
            $jmlTagihanPerCollector = 0;
            $jmlBayarPerCollector = 0;
            $lastCollector = "";
            $xlsRow++;
            $sheet->mergeCells('A'.$xlsRow.':I'.$xlsRow);
            $sheet->setCellValue('V'.$xlsRow, "TOTAL PER ".strtoupper(tglIndo($aDate,"SHORT")));
            $sheet->getStyle('V'.$xlsRow.':W'.$xlsRow)->getFont()->setBold(true);
            $sheet->mergeCells('V'.$xlsRow.':W'.$xlsRow);
            $sheet->setCellValue('X'.$xlsRow, $jmlPokokPerTgl);
            //$sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Y'.$xlsRow, $jmlBungaPerTgl);
            //$sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Z'.$xlsRow, $jmlDendaPerTgl);
            //$sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('W'.$xlsRow, $jmlTagihanPerTgl);
            //$sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AA'.$xlsRow, $jmlBayarPerTgl);
            //$sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AG'.$xlsRow, $grandTotalBayar);

            $xlsRow++;
          }
          $sheet->mergeCells('A'.$xlsRow.':AF'.$xlsRow);

          $sheet->getStyle("A".$xlsRow.":B".$xlsRow)->applyFromArray(array(
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
          ));

          $sheet->setCellValue('A'.$xlsRow, " T  O  T  A  L ");
          $sheet->getStyle('A'.$xlsRow.':AF'.$xlsRow)->getFont()->setBold(true);
          $sheet->setCellValue('AG'.$xlsRow, $grandTotalBayar);

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
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }
  //end function

  public function listLaporan() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
        $periode = date("Y")."-".date("m");
      }
      else {
        $periode = Input::get("periode");
      }

      $arrPeriode = explode("-", $periode);
      $month = $arrPeriode[1];
      $year = $arrPeriode[0];

      $collector = DB::table("coll_user")
        ->where("U_GROUP_ROLE", "GR_COLLECTOR")
        ->where("PRSH_ID", $userData->{"PRSH_ID"})
        ->get();

      return View::make("dashboard.collection.laporan-list")
        ->with("ctlUserData", $userData)
        ->with("ctlFilterMonth",$month)
        ->with("ctlFilterYear", $year)
        ->with("ctlCollector", $collector)
        ->with("ctlNavMenu", "mCollLaporan");
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  public function listLaporanView()
  {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }
      function bulan($bulan)
      {
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
      $rptType = Input::get("tipe");
      if(!isset($rptType) || trim($rptType) === "") $rptType = "RPT_COLLECTING";

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      //****

      if($rptType == "RPT_COLLECTING") {
        $bln = date("m");
        $thn = date("Y");
        if(null !== Input::get("bln") && trim(Input::get("bln")) !== "")  $bln = Input::get("bln");
        if(null !== Input::get("thn") && trim(Input::get("thn")) !== "")  $thn = Input::get("thn");

        if(null !== Input::get("periode") && trim(Input::get("periode")) !== "") {
          $arrPeriode = explode("-", Input::get("periode"));
          $bln = $arrPeriode[1];
          $thn = $arrPeriode[0];
        }

        //formalitas
        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
        $awal = $thn."-".$bln."-01";
        $akhir = $thn."-".$bln."-".$daysOfMonth;
        $collector = "ALL";
      }

      if($rptType == "RPT_COLLECTING_QUERY") {
        $bln = date("m"); //formalitas
        $bln = bulan($bln);
        $thn = date("Y"); //formalitas

        $collector = "ALL";
        if(null !== Input::get("collector") && trim(Input::get("collector")) !== "")  $collector = Input::get("collector");

        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".getLastDate(date("m"), date("Y"));

        if(null !== Input::get("awal") && trim(Input::get("awal")) !== "")    $awal = Input::get("awal");
        if(null !== Input::get("akhir") && trim(Input::get("akhir")) !== "")  $akhir = Input::get("akhir");
      }

      if($collector == "ALL") {
        $jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS,C.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_user AS C ON A.J_COLL_U_ID = C.U_ID WHERE (A.J_TGL BETWEEN ? AND ?) AND A.PRSH_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($awal, $akhir, $userData->{"PRSH_ID"}));
      }
      else {
        $jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS,C.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_user AS C ON A.J_COLL_U_ID = C.U_ID WHERE (A.J_TGL BETWEEN ? AND ?) AND A.PRSH_ID = ? AND A.J_COLL_U_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($awal, $akhir, $userData->{"PRSH_ID"}, $collector));
      }
      // var_dump($jadwal);
      return View::make('dashboard.collection.laporan-listview')
            ->with("ctlUserData", $userData)
            ->with('jadwal', $jadwal)
            ->with("ctlNavMenu", "mCollLaporan");
    } else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }

  }

  public function downloadLaporan() {
    if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
      if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
        Session::flush();
        return Redirect::to('login')->with('ctlError','Please login to access system');
      }

      function bulan($bulan)
          {
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

      $rptType = Input::get("tipe");
      if(!isset($rptType) || trim($rptType) === "") $rptType = "RPT_COLLECTING";

      $userId = Session::get('SESSION_USER_ID', '');
      $userData = DB::table('coll_user')->where('U_ID',$userId)->first();

      //****

      if($rptType == "RPT_COLLECTING") {
        $bln = date("m");
        $thn = date("Y");
        if(null !== Input::get("bln") && trim(Input::get("bln")) !== "")  $bln = Input::get("bln");
        if(null !== Input::get("thn") && trim(Input::get("thn")) !== "")  $thn = Input::get("thn");

        if(null !== Input::get("periode") && trim(Input::get("periode")) !== "") {
          $arrPeriode = explode("-", Input::get("periode"));
          $bln = $arrPeriode[1];
          $thn = $arrPeriode[0];
        }

        //formalitas
        $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".$daysOfMonth;
        $collector = "ALL";
      }

      if($rptType == "RPT_COLLECTING_QUERY") {
        $bln = date("m"); //formalitas
        $bln = bulan($bln);
        $thn = date("Y"); //formalitas

        $collector = "ALL";
        if(null !== Input::get("collector") && trim(Input::get("collector")) !== "")  $collector = Input::get("collector");

        $awal = date("Y")."-".date("m")."-01";
        $akhir = date("Y")."-".date("m")."-".getLastDate(date("m"), date("Y"));

        if(null !== Input::get("awal") && trim(Input::get("awal")) !== "")    $awal = Input::get("awal");
        if(null !== Input::get("akhir") && trim(Input::get("akhir")) !== "")  $akhir = Input::get("akhir");
      }

      Excel::create('Pintech Mobile App Report', function($excel) use($rptType, $userData, $bln, $thn, $awal, $akhir, $collector) {
        $rptName = getReferenceInfo("REPORT_TYPE", $rptType);
        // Set the title
        $excel->setTitle('Pintech Mobile App System Report '.$thn."_".$bln);
        $excel->setCreator('Pintech Mobile App System')->setCompany($userData->{"PRSH_ID"});
        $excel->setDescription('Laporan hasil collecting');

        $excel->sheet('Sheet 1', function ($sheet) use($rptType, $userData, $bln, $thn, $awal, $akhir, $collector) {
          $sheet->setOrientation('landscape');
          //$sheet->fromArray($data, NULL, 'A4');

          //$sheet->setCellValue('A1', "Tanggal Laporan");
          $sheet->setCellValue('M1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
          $sheet->getStyle('M1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);


          $sheet->setCellValue('A1', "PINTECH MOBILE APP REPORT PER BULAN " .$bln. " " .$thn);
          $sheet->mergeCells('A1:AA1');
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
          $sheet->setCellValue('K2', "AGUNAN");
          $sheet->getColumnDimension('K')->setWidth(30);
          $sheet->setCellValue('L2', "JML PINJAMAN");
          $sheet->getColumnDimension('L')->setWidth(30);
          $sheet->setCellValue('M2', "SALDO NOMINATIF");
          $sheet->getColumnDimension('M')->setWidth(30);
          $sheet->setCellValue('N2', "FP");
          $sheet->getColumnDimension('N')->setWidth(25);
          $sheet->setCellValue('O2', "FB");
          $sheet->getColumnDimension('O')->setWidth(25);
          $sheet->setCellValue('P2', "POKOK/BLN");
          $sheet->getColumnDimension('P')->setWidth(35);
          $sheet->setCellValue('Q2', "BUNGA/BLN");
          $sheet->getColumnDimension('Q')->setWidth(35);
          $sheet->setCellValue('R2', "KOLEKTIBILITAS");
          $sheet->getColumnDimension('R')->setWidth(20);
          $sheet->setCellValue('S2', "ANGSURAN KE");
          $sheet->getColumnDimension('S')->setWidth(38);
          $sheet->setCellValue('T2', "JANGKA WAKTU");
          $sheet->getColumnDimension('T')->setWidth(35);
          $sheet->setCellValue('U2', "TGL REALISASI");
          $sheet->getColumnDimension('U')->setWidth(35);
          $sheet->setCellValue('V2', "TGL UPLOAD");
          $sheet->getColumnDimension('V')->setWidth(35);
          $sheet->setCellValue('W2', "TGL JADWAL");
          $sheet->getColumnDimension('W')->setWidth(35);
          $sheet->setCellValue('X2', "TUNGG POKOK");
          $sheet->getColumnDimension('X')->setWidth(40);
          $sheet->setCellValue('Y2', "TUNGG BUNGA");
          $sheet->getColumnDimension('Y')->setWidth(40);
          $sheet->setCellValue('Z2', "TUNGG DENDA");
          $sheet->getColumnDimension('Z')->setWidth(40);
          $sheet->setCellValue('AA2', "TAGIHAN");
          $sheet->getColumnDimension('AA')->setWidth(40);
          $sheet->setCellValue('AB2', "STATUS");
          $sheet->getColumnDimension('AB')->setWidth(40);
          $sheet->setCellValue('AC2', "KETERANGAN");
          $sheet->getColumnDimension('AC')->setWidth(35);
          $sheet->setCellValue('AD2', "BAYAR POKOK");
          $sheet->getColumnDimension('AD')->setWidth(35);
          $sheet->setCellValue('AE2', "BAYAR BUNGA");
          $sheet->getColumnDimension('AE')->setWidth(35);
          $sheet->setCellValue('AF2', "BAYAR DENDA");
          $sheet->getColumnDimension('AF')->setWidth(35);
          $sheet->setCellValue('AG2', "TOTAL BAYAR");
          $sheet->getColumnDimension('AG')->setWidth(35);
          $sheet->setCellValue('AH2', "TANGGAL BAYAR");
          $sheet->getColumnDimension('AH')->setWidth(35);
          $sheet->setCellValue('AI2', "JAM BAYAR");
          $sheet->getColumnDimension('AI')->setWidth(35);

          $sheet->getStyle('A2:AI2')->getFont()->setBold(true);

          $grandTotalBayar = 0;

          $xlsRow = 3;
          if($rptType == "RPT_COLLECTING") {
            $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
          }

          if($rptType == "RPT_COLLECTING_QUERY") {
            $arrDiff = dayDifference2($awal,$akhir,TRUE);
            $daysOfMonth = $arrDiff["DAY"] + 1;
          }

          for($i=1; $i<=$daysOfMonth; $i++) {
            if($rptType == "RPT_COLLECTING") {
              $aDate = $thn."-".$bln."-".$i;
              if(intval($aDate) < 10) $aDate = $thn."-".$bln."-0".$i;
            }

            if($rptType == "RPT_COLLECTING_QUERY") {
              $aDate = addDaysWithDate($awal,($i-1),'Y-m-d');
            }

            if($collector == "ALL") {
              $jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS,C.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_user AS C ON A.J_COLL_U_ID = C.U_ID WHERE A.J_TGL = ? AND A.PRSH_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($aDate, $userData->{"PRSH_ID"}));
            }
            else {
              $jadwal = DB::select("SELECT A.*,B.*,DATE(B.BUD_STATUS_WAKTU) AS TGL_STATUS,C.* FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_user AS C ON A.J_COLL_U_ID = C.U_ID WHERE A.J_TGL = ? AND A.PRSH_ID = ? AND A.J_COLL_U_ID = ? ORDER BY A.J_TGL, A.J_COLL_U_ID, A.BU_ID, A.BUD_ID", array($aDate, $userData->{"PRSH_ID"}, $collector));
            }

            $sheet->setCellValue('A'.$xlsRow, tglIndo($aDate,"SHORT"));
            $sheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('A'.$xlsRow)->getFont()->setBold(true);

            $lastCollector = "";
            $jmlPokokPerCollector = 0;
            $jmlBungaPerCollector = 0;
            $jmlDendaPerCollector = 0;
            $jmlTagihanPerCollector = 0;
            $jmlBayarPerCollector = 0;
            $jmlTotalBayar = 0;

            $jmlPokokPerTgl = 0;
            $jmlBungaPerTgl = 0;
            $jmlDendaPerTgl = 0;
            $jmlTagihanPerTgl = 0;
            $jmlBayarPerTgl = 0;
            foreach ($jadwal as $aData) {
              if($lastCollector == "")  $lastCollector = $aData->{"J_COLL_U_ID"};
              if($aData->{"J_COLL_U_ID"}   != $lastCollector) {
                $sheet->mergeCells('A'.$xlsRow.':J'.$xlsRow);
                $sheet->setCellValue('W'.$xlsRow, "T O T A L");
                $sheet->getStyle('W'.$xlsRow.':Z'.$xlsRow)->getFont()->setBold(true);
                $sheet->setCellValue('X'.$xlsRow, $jmlPokokPerCollector);
                //$sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Y'.$xlsRow, $jmlBungaPerCollector);
                //$sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('Z'.$xlsRow, $jmlDendaPerCollector);
                //$sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AA'.$xlsRow, $jmlTagihanPerCollector);
                //$sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('AB'.$xlsRow, $jmlBayarPerCollector);
                //$sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

                $jmlPokokPerCollector = 0;
                $jmlBungaPerCollector = 0;
                $jmlDendaPerCollector = 0;
                $jmlTagihanPerCollector = 0;
                $jmlBayarPerCollector = 0;
                $jmlTotalBayar = 0;
                $lastCollector = $aData->{"J_COLL_U_ID"};
                $xlsRow++;
              }

              $jmlPokokPerCollector += $aData->{"BUD_PINJ_POKOK"};
              $jmlBungaPerCollector += $aData->{"BUD_PINJ_BUNGA"};
              $jmlDendaPerCollector += $aData->{"BUD_PINJ_DENDA"};
              $jmlTagihanPerCollector += $aData->{"BUD_PINJ_JUMLAH"};
              $jmlBayarPerCollector += $aData->{"J_PINJ_JUMLAH_BAYAR"};

              $jmlPokokPerTgl += $aData->{"BUD_PINJ_POKOK"};
              $jmlBungaPerTgl += $aData->{"BUD_PINJ_BUNGA"};
              $jmlDendaPerTgl += $aData->{"BUD_PINJ_DENDA"};
              $jmlTagihanPerTgl += $aData->{"BUD_PINJ_JUMLAH"};
              $jmlBayarPerTgl += $aData->{"J_PINJ_JUMLAH_BAYAR"};

              $grandTotalBayar += $aData->{"J_PINJ_JUMLAH_BAYAR"};

              //mulai start edit line setelah ini
              $sheet->setCellValue('B'.$xlsRow, $aData->{"USERBIGID"});
              $sheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('C'.$xlsRow, $aData->{"U_NAMA"});
              //$sheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('D'.$xlsRow, $aData->{"BUD_KODE_GROUP"});
              $sheet->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('E'.$xlsRow, $aData->{"BUD_CAB"});
              $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('F'.$xlsRow, $aData->{"BUD_PINJ_ID"});
              $sheet->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('G'.$xlsRow, $aData->{"BUD_CUST_ID"});
              $sheet->getStyle('G')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('H'.$xlsRow, $aData->{"BUD_CUST_NAMA"});
             // $sheet->getStyle('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('I'.$xlsRow, $aData->{"BUD_CUST_ALAMAT"});
              $sheet->setCellValue('J'.$xlsRow, $aData->{"BUD_CUST_PONSEL"});
              $sheet->setCellValue('K'.$xlsRow, $aData->{"BUD_AGUNAN"});
              $sheet->setCellValue('L'.$xlsRow, $aData->{"BUD_JML_PINJAMAN"});
              $sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('M'.$xlsRow, $aData->{"BUD_SALDO_NOMINATIF"});
              $sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('N'.$xlsRow, $aData->{"BUD_FP"});
              $sheet->setCellValue('O'.$xlsRow, $aData->{"BUD_FB"});
              //$sheet->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('P'.$xlsRow, $aData->{"BUD_BLN_POKOK"});
              $sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Q'.$xlsRow, $aData->{"BUD_BLN_BUNGA"});
              $sheet->getStyle('Q')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('R'.$xlsRow, $aData->{"BUD_KOLEKTIBILITAS"});
              //end new
              $sheet->setCellValue('S'.$xlsRow, $aData->{"BUD_PINJ_PERIODE"});
              $sheet->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('T'.$xlsRow, $aData->{"BUD_PINJ_MASA_KREDIT"});
              $sheet->getStyle('T')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('U'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_KREDIT"},"SHORT"));
             // $sheet->getStyle('U')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('V'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_JADWAL"},"SHORT"));
             // $sheet->getStyle('V')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('W'.$xlsRow, $aData->{"BUD_TGL_DEPAN_JADWAL"});
              //$sheet->getStyle('S')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('X'.$xlsRow, $aData->{"BUD_PINJ_POKOK"});
              $sheet->getStyle('X')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Y'.$xlsRow, $aData->{"BUD_PINJ_BUNGA"});
              $sheet->getStyle('Y')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('Z'.$xlsRow, $aData->{"BUD_PINJ_DENDA"});
              $sheet->getStyle('Z')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('AA'.$xlsRow, $aData->{"BUD_PINJ_JUMLAH"});
              $sheet->getStyle('AA')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


              if($aData->{"J_STATUS"} == "ST_BAYAR" || $aData->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
                $sheet->setCellValue('AB'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"},"SHORT"));
                $sheet->getStyle('AB')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              }
              else {
                $sheet->setCellValue('AB'.$xlsRow, "-");
              }

              $sheet->setCellValue('AB'.$xlsRow, getReferenceInfo("STATUS_COLLECTION",$aData->{"J_STATUS"}));
              //$sheet->getStyle('R')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
              $sheet->setCellValue('AC'.$xlsRow, $aData->{"BUD_KETERANGAN"});

              $sheet->setCellValue('AD'.$xlsRow, $aData->{"BUD_EDIT_POKOK"});
              $sheet->getStyle('AD')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AE'.$xlsRow, $aData->{"BUD_EDIT_BUNGA"});
              $sheet->getStyle('AE')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AF'.$xlsRow, $aData->{"BUD_EDIT_DENDA"});
              $sheet->getStyle('AF')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AG'.$xlsRow, $aData->{"J_PINJ_JUMLAH_BAYAR"});
              $sheet->getStyle('AG')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

              $sheet->setCellValue('AH'.$xlsRow, tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"}, "LONG"));

              $sheet->setCellValue('AI'.$xlsRow, date("H:i:s", strtotime($aData->{"BUD_PINJ_TGL_BAYAR"})));

              $xlsRow++;
            }
            //last row per collector
            $sheet->mergeCells('A'.$xlsRow.':J'.$xlsRow);
            $sheet->setCellValue('W'.$xlsRow, "T O T A L");
            $sheet->getStyle('X'.$xlsRow.':AA'.$xlsRow)->getFont()->setBold(true);
            $sheet->setCellValue('X'.$xlsRow, $jmlPokokPerCollector);
            //$sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Y'.$xlsRow, $jmlBungaPerCollector);
            //$sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Z'.$xlsRow, $jmlDendaPerCollector);
            //$sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AA'.$xlsRow, $jmlTagihanPerCollector);
            //$sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AG'.$xlsRow, $jmlBayarPerCollector);
            //$sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            $jmlPokokPerCollector = 0;
            $jmlBungaPerCollector = 0;
            $jmlDendaPerCollector = 0;
            $jmlTagihanPerCollector = 0;
            $jmlBayarPerCollector = 0;
            $lastCollector = "";
            $xlsRow++;
            $sheet->mergeCells('A'.$xlsRow.':I'.$xlsRow);
            $sheet->setCellValue('V'.$xlsRow, "TOTAL PER ".strtoupper(tglIndo($aDate,"SHORT")));
            $sheet->getStyle('V'.$xlsRow.':W'.$xlsRow)->getFont()->setBold(true);
            $sheet->mergeCells('V'.$xlsRow.':W'.$xlsRow);
            $sheet->setCellValue('X'.$xlsRow, $jmlPokokPerTgl);
            //$sheet->getStyle('L')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Y'.$xlsRow, $jmlBungaPerTgl);
            //$sheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('Z'.$xlsRow, $jmlDendaPerTgl);
            //$sheet->getStyle('N')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('W'.$xlsRow, $jmlTagihanPerTgl);
            //$sheet->getStyle('O')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AA'.$xlsRow, $jmlBayarPerTgl);
            //$sheet->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('AG'.$xlsRow, $grandTotalBayar);

            $xlsRow++;
          }
          $sheet->mergeCells('A'.$xlsRow.':AF'.$xlsRow);

          $sheet->getStyle("A".$xlsRow.":B".$xlsRow)->applyFromArray(array(
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
          ));

          $sheet->setCellValue('A'.$xlsRow, " T  O  T  A  L ");
          $sheet->getStyle('A'.$xlsRow.':AF'.$xlsRow)->getFont()->setBold(true);
          $sheet->setCellValue('AG'.$xlsRow, $grandTotalBayar);

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
    }
    else {
      Session::flush();
      return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
    }
  }

  //== API TABUNGAN ===
  public function apiGetTabungan() {
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR", "User ID tidak dikenali");

    if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
      $periode = date("Y-m-d");
      $periode_dmY = date("d-m-Y");
    }
    else {
      $periode = Input::get("periode");
      $arrTgl = explode("-", $periode);
      $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
    }

    if(null === Input::get("status") || trim(Input::get("status")) === "") {
      $collTabRecord = DB::select("SELECT A.*, C.PRSH_ID, C.PRSH_NAMA  FROM coll_batch_tabungan AS A INNER JOIN coll_perusahaan AS C ON A.PRSH_ID = C.PRSH_ID WHERE A.TGL_UPLOAD = ?", array($periode));
    } else {
      $collTabRecord = DB::select("SELECT A.*, C.PRSH_ID, C.PRSH_NAMA  FROM coll_batch_tabungan AS A INNER JOIN coll_perusahaan AS C ON A.PRSH_ID = C.PRSH_ID WHERE A.TGL_UPLOAD = ? AND A.BT_STATUS = ?", array($periode, Input::get("status")));
    }

    foreach($collTabRecord as $tabData) {
      $tabData->{"BT_STATUS_INFO"} = getReferenceInfo("STATUS_TABUNGAN", $tabData->{"BT_STATUS"});
      $tabData->{"BT_TGL_REGISTRASI_FORMATED"} = tglIndo($tabData->{"TGL_REGISTRASI"},"SHORT");
      $tabData->{"BT_TGL_UPLOAD_FORMATED"} = tglIndo($tabData->{"TGL_UPLOAD"},"SHORT");
      $tabData->{"BT_SALDO_AWAL_FORMATED"} = number_format($tabData->{"BT_SALDO_AWAL"});
      $tabData->{"BT_SALDO_MINIM_FORMATED"} = number_format($tabData->{"BT_SALDO_MINIMUM"});
      $tabData->{"BT_SETOR_MINIMUM_FORMATED"} = number_format($tabData->{"BT_SETOR_MINIMUM"});
    }

    return composeReply2("SUCCESS", "Data List Tabungan", $collTabRecord);

  }

  public function apiGetHistory(){
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR", "User ID tidak dikenali");

    $tabHistory = DB::select("SELECT A.*, C.PRSH_ID, C.PRSH_NAMA  FROM coll_batch_tabungan AS A INNER JOIN coll_perusahaan AS C ON A.PRSH_ID = C.PRSH_ID WHERE A.BT_PETUGAS = ?", array(Input::get("userId")));
    //formated  alias data json
    foreach($tabHistory as $tabData) {
      $tabData->{"BT_STATUS_INFO"} = getReferenceInfo("STATUS_TABUNGAN", $tabData->{"BT_STATUS"});
      $tabData->{"BT_TGL_REGISTRASI_FORMATED"} = tglIndo($tabData->{"TGL_REGISTRASI"},"SHORT");
      $tabData->{"BT_TGL_UPLOAD_FORMATED"} = tglIndo($tabData->{"TGL_UPLOAD"},"SHORT");
      $tabData->{"BT_SALDO_AWAL_FORMATED"} = number_format($tabData->{"BT_SALDO_AWAL"});
      $tabData->{"BT_SALDO_MINIM_FORMATED"} = number_format($tabData->{"BT_SALDO_MINIMUM"});
      $tabData->{"BT_SETOR_MINIMUM_FORMATED"} = number_format($tabData->{"BT_SETOR_MINIMUM"});
      $tabData->{"BT_SALDO_AKHIR_FORMATED"} = number_format($tabData->{"BT_SALDO_AKHIR"});
    }

    return composeReply2("SUCCESS", "Data Last History Tabungan", $tabHistory);

  }

  public function apiUpdateTabungan(){
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token");

    if(null === Input::get("status") || trim(Input::get("status")) === "")  return composeReply2("ERROR", "Status pembayaran harus diisi");

    if(Input::get("status") === "BT_JADWAL")  return composeReply2("ERROR", "Data tidak diproses karena status penagihan masih dalam penjadwalan");

    if(null === Input::get("budId") || trim(Input::get("budId")) === "")    return composeReply2("ERROR", "Invalid collection Tabungan ID");
    $budData = DB::table("coll_batch_tabungan")->where("BT_ID", Input::get("budId"))->first();

    $saldo_minim = $budData->{"BT_SALDO_MINIMUM"};
    $setor_minim = $budData->{"BT_SETOR_MINIMUM"};
    $custId = $budData->{"BT_NASABAH_ID"};

    if(count($budData) <= 0)  return composeReply2("ERROR", "Invalid  collection Tabungan ID");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR","User ID tidak dikenal");

    $jmlSetor = "0";
    $tglSetor = "0000-00-00 00:00:00";
    $statusNabung = Input::get("status");
    ///dd($statusNabung);

    if($statusNabung === "BT_MENABUNG") {
      if(null === Input::get("bayar") || trim(Input::get("bayar")) === "")  return composeReply2("ERROR", "Nominal pembayaran harus diisi");
      $jmlNabung = Input::get("bayar");
      $tglNabung = date("Y-m-d H:i:s");
      if(!is_numeric($jmlNabung))    return composeReply2("ERROR", "Nominal pembayaran harus berupa bilangan");
      if(floatval($jmlNabung) <= $setor_minim) return composeReply2("ERROR", "Jumlah Bayar anda kurang dari setor minimum");

        //update data jml saldo di data nasabah
        $dataNasabah = DB::table("coll_customers")->where("CUST_ID", $custId)->first();
        $saldoNasabah = $dataNasabah->{"CUST_SALDO"};
        //kurangi saldo
        $krSaldo = $saldoNasabah + $jmlNabung;
        $updateNasabah = DB::table("coll_customers")->where("CUST_ID", $custId)->update(array(
          'CUST_SALDO' => $krSaldo
        ));

        //update data tabungan
        //cek jika membayar
        if(Input::get("status") === "BT_MENABUNG") {
          $dataTabungan = DB::table("coll_batch_tabungan")->where("BT_ID", Input::get("budId"))->update(array(
            'BT_SETORAN' => trim(Input::get("bayar")),
            'BT_SALDO_AKHIR' => $krSaldo,
            'TGL_SETORAN' => $tglNabung,
            'BT_STATUS' => Input::get("status"),
            'BT_KETERANGAN' => Input::get("keterangan"),
            'BT_PETUGAS' => Input::get("userId"),
            'BT_NO_NOTA' => Input::get("no_nota")
          ));
        }

        $noNota = DB::table("coll_user")->where("U_ID", Input::get("userId"))->update(array(
            'U_NOTA_ID' => Input::get("no_nota")
          ));

    } else {
      $dataTabungan = DB::table("coll_batch_tabungan")->where("BT_ID", Input::get("budId"))->update(array(
          'BT_SETORAN' => trim(Input::get("bayar")),
          'BT_STATUS' => Input::get("status"),
          'BT_KETERANGAN' => Input::get("keterangan"),
          'BT_PETUGAS' => Input::get("userId")
        ));
    }

   if($dataTabungan > 0) {
      return composeReply2("SUCCESS", "Data berhasil disimpan");
    }
    else {
      return composeReply2("ERROR", "Tidak terjadi perubahan data atau data gagal disimpan");
    }

  }
  // == END API TABUNGAN ==
  //api get jadwal full
   public function apiListJadwalFull() {
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR", "User ID tidak dikenal");
    $prsh_id = $userData->{"PRSH_ID"};
    $userId = Input::get("userId");

    if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
      $periode = date("Y-m-d");
      $periode_dmY = date("d-m-Y");
    }
    else {
      $periode = Input::get("periode");
      $arrTgl = explode("-", $periode);
      $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
    }

    if(null === Input::get("status") || trim(Input::get("status")) === "") {
      $collRecords = DB::select("SELECT A.*,B.*, C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID WHERE A.J_TGL = ? AND A.PRSH_ID = ? AND A.J_COLL_U_ID != ?", array($periode, $prsh_id, $userId));
    }
    else {
      $collRecords = DB::select("SELECT A.*,B.*,C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID WHERE A.J_TGL = ?  AND A.J_STATUS = ? AND A.PRSH_ID = ? AND A.J_COLL_U_ID != ?", array($periode, Input::get("status"), $prsh_id, $userId));
    }

    foreach ($collRecords as $aData) {
      $aData->{"BUD_STATUS_INFO"} = getReferenceInfo("STATUS_COLLECTION", $aData->{"BUD_STATUS"});
      $aData->{"BUD_PINJ_TGL_KREDIT_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_KREDIT"},"SHORT");
      $aData->{"BUD_PINJ_TGL_ANGS_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_ANGS"},"SHORT");
      $aData->{"BUD_PINJ_TGL_JADWAL_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_JADWAL"},"SHORT");
      $aData->{"BUD_PINJ_TGL_BAYAR_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"},"SHORT");
      $aData->{"BUD_PINJ_POKOK_FORMATTED"} = number_format($aData->{"BUD_PINJ_POKOK"});
      $aData->{"BUD_PINJ_BUNGA_FORMATTED"} = number_format($aData->{"BUD_PINJ_BUNGA"});
      $aData->{"BUD_PINJ_DENDA_FORMATTED"} = number_format($aData->{"BUD_PINJ_DENDA"});
      $aData->{"BUD_PINJ_JUMLAH_FORMATTED"} = number_format($aData->{"BUD_PINJ_JUMLAH"});
      $aData->{"BUD_PINJ_JUMLAH_BAYAR_FORMATTED"} = number_format($aData->{"BUD_PINJ_JUMLAH_BAYAR"});
    }

    return composeReply2("SUCCESS", "Data jadwal ", $collRecords);
  }

  public function apiListJadwal() {
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR", "User ID tidak dikenal");

    if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
      $periode = date("Y-m-d");
      $periode_dmY = date("d-m-Y");
    }
    else {
      $periode = Input::get("periode");
      $arrTgl = explode("-", $periode);
      $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
    }

    if(null === Input::get("status") || trim(Input::get("status")) === "") {

      $collRecords = DB::select("SELECT A.*,B.*, C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID WHERE A.J_TGL = ?  AND A.J_COLL_U_ID = ?", array($periode, $userData->{"U_ID"}));
    }
    else {
      $collRecords = DB::select("SELECT A.*,B.*,C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID WHERE A.J_TGL = ?  AND A.J_COLL_U_ID = ? AND A.J_STATUS = ?", array($periode, $userData->{"U_ID"}, Input::get("status")));
    }

    foreach ($collRecords as $aData) {
      $aData->{"BUD_STATUS_INFO"} = getReferenceInfo("STATUS_COLLECTION", $aData->{"BUD_STATUS"});
      $aData->{"BUD_PINJ_TGL_KREDIT_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_KREDIT"},"SHORT");
      $aData->{"BUD_PINJ_TGL_ANGS_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_ANGS"},"SHORT");
      $aData->{"BUD_PINJ_TGL_JADWAL_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_JADWAL"},"SHORT");
      $aData->{"BUD_PINJ_TGL_BAYAR_FORMATTED"} = tglIndo($aData->{"BUD_PINJ_TGL_BAYAR"},"SHORT");
      $aData->{"BUD_PINJ_POKOK_FORMATTED"} = number_format($aData->{"BUD_PINJ_POKOK"});
      $aData->{"BUD_PINJ_BUNGA_FORMATTED"} = number_format($aData->{"BUD_PINJ_BUNGA"});
      $aData->{"BUD_PINJ_DENDA_FORMATTED"} = number_format($aData->{"BUD_PINJ_DENDA"});
      $aData->{"BUD_PINJ_JUMLAH_FORMATTED"} = number_format($aData->{"BUD_PINJ_JUMLAH"});
      $aData->{"BUD_PINJ_JUMLAH_BAYAR_FORMATTED"} = number_format($aData->{"BUD_PINJ_JUMLAH_BAYAR"});
    }

    return composeReply2("SUCCESS", "Data jadwal ", $collRecords);
  }

  public function apiUpdateJadwal() {
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token");

    if(null === Input::get("status") || trim(Input::get("status")) === "")  return composeReply2("ERROR", "Status pembayaran harus diisi");

    if(Input::get("status") === "ST_JADWAL")  return composeReply2("ERROR", "Data tidak diproses karena status penagihan masih dalam penjadwalan");
    if (!empty(Input::get('periode'))) {
      if (Input::get('periode') != date('Y-m-d')) {
        return composeReply2('ERROR', 'Maaf data tidak dalam penjadwalan');
      }
    }

    if(null === Input::get("budId") || trim(Input::get("budId")) === "")    return composeReply2("ERROR", "Invalid collection ID");
    $budData = DB::table("coll_batch_upload_data")->where("BUD_ID", Input::get("budId"))->first();
    if(count($budData) <= 0)  return composeReply2("ERROR", "Invalid collection ID");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR","User ID tidak dikenal");

    $jmlBayar = "0";
    $tglBayar = "0000-00-00 00:00:00";
    $statusPenagihan = Input::get("status");
    if($statusPenagihan === "ST_BAYAR" || $statusPenagihan === "ST_BAYAR_PARSIAL") {
      if(null === Input::get("bayar") || trim(Input::get("bayar")) === "")  return composeReply2("ERROR", "Nominal pembayaran harus diisi");
      $jmlBayar = Input::get("bayar");
      $tglBayar = date("Y-m-d H:i:s");

      if(!is_numeric($jmlBayar))    return composeReply2("ERROR", "Nominal pembayaran harus berupa bilangan");
      if(floatval($jmlBayar) <= 0)  return composeReply2("ERROR", "Masukkan nominal pembayaran test yang benar");
      if(floatval($jmlBayar) < floatval($budData->{"BUD_PINJ_JUMLAH"})) {
        //return composeReply2("ERROR", "Nominal pembayaran HARUS sebesar Rp.".number_format($budData->{"BUD_PINJ_JUMLAH"}));
        $statusPenagihan = "ST_BAYAR_PARSIAL";
      }
    }
    else { //PERLU DIPASTIKAN BGMN JIKA STATUS TIDAK BAYAR TP ADA NOMINAL PEMBAYARANNYA
      if(null !== Input::get("bayar") && trim(Input::get("bayar")) !== "")  {
        $jmlBayar = Input::get("bayar");
        $tglBayar = date("Y-m-d H:i:s");

        if(!is_numeric($jmlBayar))    return composeReply2("ERROR", "Nominal pembayaran harus berupa bilangan");
       // if(floatval($jmlBayar) <= 0)  return composeReply2("ERROR", "Masukkan nominal pembayaran test2 yang benar");
        if(floatval($jmlBayar) < floatval($budData->{"BUD_PINJ_JUMLAH"})) {
          //return composeReply2("ERROR", "Nominal pembayaran HARUS sebesar Rp.".number_format($budData->{"BUD_PINJ_JUMLAH"}));
          $statusPenagihan = "ST_BAYAR_PARSIAL";
        }
        if(floatval($jmlBayar) >= floatval($budData->{"BUD_PINJ_JUMLAH"})) {
          $statusPenagihan = "ST_BAYAR";
        }
        if(floatval($jmlBayar) == 0 && Input::get("status") == 'ST_TIDAK_BAYAR') {
          $statusPenagihan = "ST_TIDAK_BAYAR";
        }
        if(floatval($jmlBayar) == 0 && Input::get("status") == 'ST_TIDAK_DITEMUKAN') {
          $statusPenagihan = "ST_TIDAK_DITEMUKAN";
        }
      }
    }

    //input edit pokok
    //if(null !== Input::get("editPokok") && trim(Input::get("editPokok")) !== "") {
      $editPokok = Input::get("editPokok");

      if(!is_numeric($editPokok))    return composeReply2("ERROR", "Nominal Edit Pokok harus berupa bilangan");
     // if(floatval($editPokok) <= 0)  return composeReply2("ERROR", "Masukkan Edit Pokok yang benar");

   // }

    //input edit bunga
    //if(null !== Input::get("editBunga") && trim(Input::get("editBunga")) !== "") {
      $editBunga = Input::get("editBunga");

      if(!is_numeric($editBunga))    return composeReply2("ERROR", "Nominal Edit BUnga harus berupa bilangan");
//      if(floatval($editBunga) <= 0)  return composeReply2("ERROR", "Masukkan Edit Bunga pembayaran yang benar");

  //  }

    //input edit denda
    //if(null !== Input::get("editDenda") && trim(Input::get("editDenda")) !== "") {
      $editDenda = Input::get("editDenda");

      if(!is_numeric($editDenda))    return composeReply2("ERROR", "Nominal edit Denda harus berupa bilangan");
      //if(floatval($editDenda) <= 0)  return composeReply2("ERROR", "Masukkan nominal edit yang benar");

    //}

    if(isset($_FILES['uploadFile'])){
      $fileName = $_FILES['uploadFile']['name'];
      $fileSize = $_FILES['uploadFile']['size'];
      $fileTmp = $_FILES['uploadFile']['tmp_name'];
      $fileType = $_FILES['uploadFile']['type'];
      $a = explode(".", $_FILES["uploadFile"]["name"]);
      $fileExt = strtolower(end($a));

      $arrFileExt = array("jpg","jpeg","png","JPG","JPEG","PNG");
      if(isset($fileName) && trim($fileName) != "") {
        if(in_array($fileExt,$arrFileExt)=== false)   return composeReply2("ERROR","Harap pilih file JPG atau PNG");
        if($fileSize > 2048000)                       return composeReply2("ERROR","Harap pilih file JPG atau PNG dengan ukuran max. 2 MB");

        $uploadFile = "uploads/report-".$userData->{"U_ID"}."-".substr(md5(date("YmdHis")),0,10).".".$fileExt;
        if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
          $opr = DB::table("coll_batch_upload_data")->where("BUD_ID", Input::get("budId"))->update(array(
            'BUD_EDIT_POKOK' => $editPokok,
            'BUD_EDIT_BUNGA' => $editBunga,
            'BUD_EDIT_DENDA' => $editDenda,
            'BUD_STATUS' => $statusPenagihan,
            'BUD_STATUS_WAKTU' => date("Y-m-d H:i:s"),
            'BUD_KETERANGAN' => Input::get("keterangan"),
            'BUD_PINJ_JUMLAH_BAYAR' => $jmlBayar,
            'BUD_PINJ_TGL_BAYAR' => $tglBayar,
            'BUD_LOKASI_LAT' => Input::get("latitude"),
            'BUD_LOKASI_LNG' => Input::get("longitude"),
            'BUD_IMG_PATH' => $uploadFile,
            'BUD_NO_NOTA' => Input::get("no_nota")
          ));

          $dateNow = date("Y-m-d");
          $opr = DB::table("coll_jadwal")
            ->where("BUD_ID", Input::get("budId"))
            ->where("J_TGL", "=", $dateNow)
            ->update(array(
              'J_STATUS' => $statusPenagihan,
              'J_PINJ_JUMLAH_BAYAR' => $jmlBayar
            ));

          if($statusPenagihan == "ST_BAYAR") {
            $nextStatus = "-";
          }
          else {
            $nextStatus = "ST_JADWAL";
          }

          $opr2 = DB::table("coll_jadwal")
            ->where("BUD_ID", Input::get("budId"))
            ->where("J_TGL",">", Input::get('periode'))
            ->update(array(
              'J_STATUS' => $nextStatus
            ));

          $noNota = DB::table("coll_user")->where("U_ID", Input::get("userId"))->update(array(
            'U_NOTA_ID' => Input::get("no_nota")
          ));

          $jadwal = DB::select("SELECT DISTINCT(BU_ID) AS BU_ID FROM coll_jadwal WHERE J_TGL = ? AND J_COLL_U_ID = ?", array(Input::get("periode"), Input::get("userId")));

            if(count($jadwal) > 0) {
              foreach ($jadwal as $aData) {
                $cisId = DB::table("coll_check_in_start")->insertGetId(array(
                  'CIS_WAKTU' => date("Y-m-d H:i:s"),
                  'CIS_COLL_U_ID' => Input::get("userId"),
                  'BU_ID' => $aData->{"BU_ID"},
                  'CIS_LOKASI_LAT' => Input::get("latitude"),
                  'CIS_LOKASI_LNG' => Input::get("longitude")
                ));
              }

              return composeReply2("SUCCESS", "Data check in start tersimpan");
            }
            else {
              return composeReply2("ERROR", "Tidak ada data jadwal per ".tglIndo(Input::get("periode"), "SHORT"));
            }

          if($opr > 0) {
            return composeReply2("SUCCESS", "Data berhasil disimpan", array(
              'UPDATED_STATUS_INFO' => getReferenceInfo("STATUS_COLLECTION", Input::get("status")),
              'UPDATED_STATUS' => Input::get("status")
            ));
          }
          else {
            return composeReply2("ERROR", "Tidak terjadi perubahan data atau data gagal disimpan");
          }

        }
        else {
          return composeReply2("ERROR","Proses upload gagal. Silahkan diulang kembali.");
        }
      }
      else {
        return composeReply2("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
      }
    }
    else {
      return composeReply2("ERROR","Harap sertakan file untuk diunggah");
    }
  }

  public function apiUpdateJadwalX() {
      if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token");

    if(null === Input::get("status") || trim(Input::get("status")) === "")  return composeReply2("ERROR", "Status pembayaran harus diisi");

    if(Input::get("status") === "ST_JADWAL")  return composeReply2("ERROR", "Data tidak diproses karena status penagihan masih dalam penjadwalan");

    if (!empty(Input::get('periode'))) {
      if (Input::get('periode') != date('Y-m-d')) {
        return composeReply2('ERROR', 'Maaf data tidak dalam penjadwalan');
      }
    }

    if(null === Input::get("budId") || trim(Input::get("budId")) === "")    return composeReply2("ERROR", "Invalid collection ID");
    $budData = DB::table("coll_batch_upload_data")->where("BUD_ID", Input::get("budId"))->first();
    if(count($budData) <= 0)  return composeReply2("ERROR", "Invalid collection ID");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    if(count($userData) <= 0) return composeReply2("ERROR","User ID tidak dikenal");

    $jmlBayar = "0";
    $tglBayar = "0000-00-00 00:00:00";
    $statusPenagihan = Input::get("status");
    if($statusPenagihan === "ST_BAYAR_NON_TARGET" || $statusPenagihan === "ST_BAYAR_PARSIAL_NON_TARGET") {
      if(null === Input::get("bayar") || trim(Input::get("bayar")) === "")  return composeReply2("ERROR", "Nominal pembayaran harus diisi");
      $jmlBayar = Input::get("bayar");
      $tglBayar = date("Y-m-d H:i:s");

      if(!is_numeric($jmlBayar))    return composeReply2("ERROR", "Nominal pembayaran harus berupa bilangan");
      if(floatval($jmlBayar) <= 0)  return composeReply2("ERROR", "Masukkan nominal pembayaran test yang benar");
      if(floatval($jmlBayar) < floatval($budData->{"BUD_PINJ_JUMLAH"})) {
        //return composeReply2("ERROR", "Nominal pembayaran HARUS sebesar Rp.".number_format($budData->{"BUD_PINJ_JUMLAH"}));
        $statusPenagihan = "ST_BAYAR_PARSIAL_NON_TARGET";
      }
    }
    else { //PERLU DIPASTIKAN BGMN JIKA STATUS TIDAK BAYAR TP ADA NOMINAL PEMBAYARANNYA
      if(null !== Input::get("bayar") && trim(Input::get("bayar")) !== "")  {
        $jmlBayar = Input::get("bayar");
        $tglBayar = date("Y-m-d H:i:s");

        if(!is_numeric($jmlBayar))    return composeReply2("ERROR", "Nominal pembayaran harus berupa bilangan");
       // if(floatval($jmlBayar) <= 0)  return composeReply2("ERROR", "Masukkan nominal pembayaran test2 yang benar");
        if(floatval($jmlBayar) < floatval($budData->{"BUD_PINJ_JUMLAH"})) {
          //return composeReply2("ERROR", "Nominal pembayaran HARUS sebesar Rp.".number_format($budData->{"BUD_PINJ_JUMLAH"}));
          $statusPenagihan = "ST_BAYAR_PARSIAL_NON_TARGET";
        }
        if(floatval($jmlBayar) >= floatval($budData->{"BUD_PINJ_JUMLAH"})) {
          $statusPenagihan = "ST_BAYAR_NON_TARGET";
        }
        if(floatval($jmlBayar) == 0 && Input::get("status") == 'ST_TIDAK_BAYAR_NON_TARGET') {
          $statusPenagihan = "ST_TIDAK_BAYAR_NON_TARGET";
        }
        if(floatval($jmlBayar) == 0 && Input::get("status") == 'ST_TIDAK_DITEMUKAN_NON_TARGET') {
          $statusPenagihan = "ST_TIDAK_DITEMUKAN_NON_TARGET";
        }
      }
    }

    //input edit pokok
    //if(null !== Input::get("editPokok") && trim(Input::get("editPokok")) !== "") {
      $editPokok = Input::get("editPokok");

      if(!is_numeric($editPokok))    return composeReply2("ERROR", "Nominal Edit Pokok harus berupa bilangan");
     // if(floatval($editPokok) <= 0)  return composeReply2("ERROR", "Masukkan Edit Pokok yang benar");

   // }

    //input edit bunga
    //if(null !== Input::get("editBunga") && trim(Input::get("editBunga")) !== "") {
      $editBunga = Input::get("editBunga");

      if(!is_numeric($editBunga))    return composeReply2("ERROR", "Nominal Edit BUnga harus berupa bilangan");
//      if(floatval($editBunga) <= 0)  return composeReply2("ERROR", "Masukkan Edit Bunga pembayaran yang benar");

  //  }

    //input edit denda
    //if(null !== Input::get("editDenda") && trim(Input::get("editDenda")) !== "") {
      $editDenda = Input::get("editDenda");

      if(!is_numeric($editDenda))    return composeReply2("ERROR", "Nominal edit Denda harus berupa bilangan");
      //if(floatval($editDenda) <= 0)  return composeReply2("ERROR", "Masukkan nominal edit yang benar");

    //}

    if(isset($_FILES['uploadFile'])){
      $fileName = $_FILES['uploadFile']['name'];
      $fileSize = $_FILES['uploadFile']['size'];
      $fileTmp = $_FILES['uploadFile']['tmp_name'];
      $fileType = $_FILES['uploadFile']['type'];
      $a = explode(".", $_FILES["uploadFile"]["name"]);
      $fileExt = strtolower(end($a));

      $arrFileExt = array("jpg","jpeg","png","JPG","JPEG","PNG");
      if(isset($fileName) && trim($fileName) != "") {
        if(in_array($fileExt,$arrFileExt)=== false)   return composeReply2("ERROR","Harap pilih file JPG atau PNG");
        if($fileSize > 2048000)                       return composeReply2("ERROR","Harap pilih file JPG atau PNG dengan ukuran max. 2 MB");

        $uploadFile = "uploads/report-".$userData->{"U_ID"}."-".substr(md5(date("YmdHis")),0,10).".".$fileExt;
        if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
          $opr = DB::table("coll_batch_upload_data")->where("BUD_ID", Input::get("budId"))->update(array(
            'BUD_COLL_U_ID' => Input::get("userId"),
            'BUD_EDIT_POKOK' => $editPokok,
            'BUD_EDIT_BUNGA' => $editBunga,
            'BUD_EDIT_DENDA' => $editDenda,
            'BUD_STATUS' => $statusPenagihan,
            'BUD_STATUS_WAKTU' => date("Y-m-d H:i:s"),
            'BUD_KETERANGAN' => Input::get("keterangan"),
            'BUD_PINJ_JUMLAH_BAYAR' => $jmlBayar,
            'BUD_PINJ_TGL_BAYAR' => $tglBayar,
            'BUD_LOKASI_LAT' => Input::get("latitude"),
            'BUD_LOKASI_LNG' => Input::get("longitude"),
            'BUD_IMG_PATH' => $uploadFile,
            'BUD_NO_NOTA' => Input::get("no_nota")
          ));

          $dateNow = date("Y-m-d");
          $opr = DB::table("coll_jadwal")
            ->where("BUD_ID", Input::get("budId"))
            ->where("J_TGL", "=", $dateNow)
            ->update(array(
              'J_STATUS' => $statusPenagihan,
              'J_PINJ_JUMLAH_BAYAR' => $jmlBayar
            ));

          if($statusPenagihan == "ST_BAYAR_NON_TARGET") {
            $nextStatus = "-";
          }
          else {
            $nextStatus = "ST_JADWAL";
          }

          $opr2 = DB::table("coll_jadwal")
            ->where("BUD_ID", Input::get("budId"))
            ->where("J_TGL",">", Input::get('periode'))
            ->update(array(
              'J_STATUS' => $nextStatus
            ));

          $noNota = DB::table("coll_user")->where("U_ID", Input::get("userId"))->update(array(
            'U_NOTA_ID' => Input::get("no_nota")
          ));

          $jadwal = DB::select("SELECT DISTINCT(BU_ID) AS BU_ID FROM coll_jadwal WHERE J_TGL = ? AND J_COLL_U_ID = ?", array(Input::get("periode"), Input::get("userId")));

            if($jadwal) {
              foreach ($jadwal as $aData) {
                $cisId = DB::table("coll_check_in_start")->insertGetId(array(
                  'CIS_WAKTU' => date("Y-m-d H:i:s"),
                  'CIS_COLL_U_ID' => Input::get("userId"),
                  'BU_ID' => $aData->{"BU_ID"},
                  'CIS_LOKASI_LAT' => Input::get("latitude"),
                  'CIS_LOKASI_LNG' => Input::get("longitude")
                ));
              }

              return composeReply2("SUCCESS", "Data check in start tersimpan");
            }
            else {
              return composeReply2("ERROR", "Tidak ada data jadwal per ".tglIndo(Input::get("periode"), "SHORT"));
            }

          if($opr > 0) {
            return composeReply2("SUCCESS", "Data berhasil disimpan", array(
              'UPDATED_STATUS_INFO' => getReferenceInfo("STATUS_COLLECTION", Input::get("status")),
              'UPDATED_STATUS' => Input::get("status")
            ));
          }
          else {
            return composeReply2("ERROR", "Tidak terjadi perubahan data atau data gagal disimpan");
          }

        }
        else {
          return composeReply2("ERROR","Proses upload gagal. Silahkan diulang kembali.");
        }
      }
      else {
        return composeReply2("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
      }
    }
    else {
      return composeReply2("ERROR","Harap sertakan file untuk diunggah");
    }
  }

  public function apiGetSummary() {
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");

    $userData = DB::table("coll_user")->where("U_ID", Input::get("userId"))->first();
    $prsh_id = $userData->{"PRSH_ID"};

    $jmlStatusJadwal = 0;
    $jmlStatusBayar = 0;
    $jmlStatusBayarParsial = 0;
    $jmlStatusTdkBayar = 0;
    $jmlStatusTdkBertemu = 0;
    $jmlNonTarget = 0;
    $nominalTarget = 0;
    $nominalBayar = 0;
    $total_bayar = 0;
    $total_bayar_parsial = 0;
    $jml_bayar_non_target = 0;
    $jml_bayar_parsial_non_target = 0;
    $jml_tidak_bayar_non_target = 0;
    $jml_tidak_bertemu_non_target = 0;
    $total_bayar_non_target = 0;
    $total_bayar_parsial_non_target = 0;

    if(null === Input::get("periode") || trim(Input::get("periode")) === "") {
      $periode = date("Y-m-d");
      $periode_dmY = date("d-m-Y");
    }
    else {
      $periode = Input::get("periode");
      $arrTgl = explode("-", $periode);
      $periode_dmY = $arrTgl[2]."-".$arrTgl[1]."-".$arrTgl[0];
    }

    //$latestBatchUpload = DB::select("SELECT BU_ID FROM coll_batch_upload WHERE PRSH_ID = ? AND BU_TGL <= ? ORDER BY BU_ID DESC LIMIT 0,1", array($userData->{"PRSH_ID"}, date("Y-m-d")));

    //$collRecords = DB::select("SELECT X.* FROM (SELECT A.*,CURDATE() AS TGL_PERIODE, DATE_ADD(A.BUD_PINJ_TGL_JADWAL, INTERVAL A.BUD_PINJ_PERIODE DAY) AS TGL_BATAS FROM coll_batch_upload_data AS A WHERE A.BU_ID = ?) AS X WHERE X.TGL_PERIODE BETWEEN X.BUD_PINJ_TGL_JADWAL AND X.TGL_BATAS AND X.BUD_COLL_U_ID = ? ORDER BY X.BUD_COLL_U_ID", array($latestBatchUpload[0]->{"BU_ID"}, $userData->{"U_ID"}));

    $collRecords = DB::select("SELECT * FROM coll_jadwal WHERE J_TGL = ?  AND J_COLL_U_ID = ?", array($periode, $userData->{"U_ID"}));
    $collRecordsAll = DB::select("SELECT * FROM coll_jadwal WHERE J_TGL = ? AND J_STATUS = 'ST_JADWAL' AND PRSH_ID = ? AND J_COLL_U_ID != ?", array($periode, $prsh_id, Input::get("userId")));
    $collRecordsNonTarget = DB::select("SELECT * FROM coll_jadwal WHERE J_TGL = ? AND PRSH_ID = ?", array($periode, $prsh_id));

    foreach($collRecordsAll as $fullData) {
      if($fullData->{"J_STATUS"} == "ST_JADWAL") $jmlNonTarget++;
    }

    foreach ($collRecordsNonTarget as $data) {
      if($data->{"J_STATUS"} == "ST_BAYAR_NON_TARGET") {
        $jml_bayar_non_target++;
        $total_bayar_non_target += $data->J_PINJ_JUMLAH_BAYAR;
      }
      if($data->{"J_STATUS"} == "ST_BAYAR_PARSIAL_NON_TARGET") {
        $jml_bayar_parsial_non_target++;
        $total_bayar_parsial_non_target += $data->J_PINJ_JUMLAH_BAYAR;
      }
      if($data->{"J_STATUS"} == "ST_TIDAK_BAYAR_NON_TARGET") {
        $jml_tidak_bayar_non_target++;
      }
      if($data->{"J_STATUS"} == "ST_TIDAK_DITEMUKAN_NON_TARGET") {
        $jml_tidak_ditemukan_non_target++;
      }
    }

    foreach ($collRecords as $aData) {
      //if($aData->{"BUD_STATUS"} == "ST_JADWAL") $jmlStatusJadwal++;
      //if($aData->{"BUD_STATUS"} == "ST_BAYAR")  $jmlStatusBayar++;
      //if($aData->{"BUD_STATUS"} == "ST_TIDAK_BAYAR")  $jmlStatusTdkBayar++;
      //if($aData->{"BUD_STATUS"} == "ST_TIDAK_DITEMUKAN")  $jmlStatusTdkBertemu++;
      //if($aData->{"BUD_STATUS"} == "ST_BAYAR_PARSIAL")  $jmlStatusBayarParsial++;

      if($aData->{"J_STATUS"} == "ST_JADWAL") {
        $jmlStatusJadwal++;
      }
      if($aData->{"J_STATUS"} == "ST_BAYAR") {
        $jmlStatusBayar++;
        $total_bayar += $aData->J_PINJ_JUMLAH_BAYAR;
      }
      if($aData->{"J_STATUS"} == "ST_TIDAK_BAYAR") {
        $jmlStatusTdkBayar++;
      }
      if($aData->{"J_STATUS"} == "ST_TIDAK_DITEMUKAN") {
        $jmlStatusTdkBertemu++;
      }
      if($aData->{"J_STATUS"} == "ST_BAYAR_PARSIAL") {
        $jmlStatusBayarParsial++;
        $total_bayar_parsial += $aData->J_PINJ_JUMLAH_BAYAR;
      }

      //$nominalTar += floatval($aData->{"BUD_PINJ_JUMLAH"});
      //$nominalBayar += floatval($aData->{"BUD_PINJ_JUMLAH_BAYAR"});

      if($aData->{"J_STATUS"} != "-" && $aData->{"J_STATUS"} != "ST_BAYAR")  $nominalTarget += floatval($aData->{"J_PINJ_JUMLAH"});
      $nominalBayar += floatval($aData->{"J_PINJ_JUMLAH_BAYAR"});
    }

    return composeReply2("SUCCESS", "Summary data", array(
      /*'BATCH_UPLOAD_ID' => $latestBatchUpload[0]->{"BU_ID"},*/
      'SUMMARY_JADWAL' => $jmlStatusJadwal." orang",
      'JUMLAH_JADWAL' => $jmlStatusJadwal,
      'SUMMARY_BAYAR' => $jmlStatusBayar." orang",
      'JUMLAH_BAYAR' => $jmlStatusBayar,
      'TOTAL_BAYAR' => $total_bayar,
      'SUMMARY_BAYAR_PARSIAL' => $jmlStatusBayarParsial." orang",
      'JUMLAH_BAYAR_PARSIAL' => $jmlStatusBayarParsial,
      'TOTAL_BAYAR_PARSIAL' => $total_bayar_parsial,
      'SUMMARY_TIDAK_BAYAR' => $jmlStatusTdkBayar." orang",
      'JUMLAH_TIDAK_BAYAR' => $jmlStatusTdkBayar,
      'SUMMARY_TIDAK_BERTEMU' => $jmlStatusTdkBertemu. " orang",
      'JUMLAH_TIDAK_BERTEMU' => $jmlStatusTdkBertemu,
      'JUMLAH_NON_TARGET' => $jmlNonTarget. " orang",
      'SUMMARY_NON_TARGET' => $jmlNonTarget,
      'SUMMARY_BAYAR_NON_TARGET' => $jml_bayar_non_target . " orang",
      'JUMLAH_BAYAR_NON_TARGET' => $jml_bayar_non_target,
      'TOTAL_BAYAR_NON_TARGET' => $total_bayar_non_target,
      'SUMMARY_BAYAR_PARSIAL_NON_TARGET' => $jml_bayar_parsial_non_target . " orang",
      'JUMLAH_BAYAR_PARSIAL_NON_TARGET' => $jml_bayar_parsial_non_target,
      'TOTAL_BAYAR_PARSIAL_NON_TARGET' => $total_bayar_parsial_non_target,
      'SUMMARY_TIDAK_BAYAR_NON_TARGET' => $jml_tidak_bayar_non_target . " orang",
      'JUMLAH_TIDAK_BAYAR_NON_TARGET' => $jml_tidak_bayar_non_target,
      'SUMMARY_TIDAK_BERTEMU_NON_TARGET' => $jml_tidak_bertemu_non_target . " orang",
      'JUMLAH_TIDAK_BERTEMU_NON_TARGET' => $jml_tidak_bertemu_non_target,
      'SUMMARY_TARGET_BAYAR' => $nominalTarget,
      'SUMMARY_REALISASI_BAYAR' => $nominalBayar
    ));
  }

  public function getReference($refCategory) {
    if(null === $refCategory || !isset($refCategory)) return composeReply2("ERROR", "Invalid category");

    $refData = DB::table("coll_referensi")->where("R_KATEGORI", $refCategory)
    ->orderBy('R_URUTAN', 'asc')
    ->get();

    return composeReply2("SUCCESS", "Referensi", $refData);
  }

  public function apiRegisterStartCheckIn() {
    if(null === Input::get("userId") || trim(Input::get("userId")) === "")          return composeReply2("ERROR", "Invalid user ID");
    if(null === Input::get("loginToken") || trim(Input::get("loginToken")) === "")  return composeReply2("ERROR", "Invalid login token");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken')))               return composeReply2("ERROR", "Invalid login token");

    if(null === Input::get("lat") || trim(Input::get("lat")) === "" || trim(Input::get("lat")) === "0")  return composeReply2("ERROR", "Latitude tidak terdeteksi");
    if(null === Input::get("lng") || trim(Input::get("lng")) === "" || trim(Input::get("lng")) === "0")  return composeReply2("ERROR", "Longitude tidak terdeteksi");

    $jadwal = DB::select("SELECT DISTINCT(BU_ID) AS BU_ID FROM coll_jadwal WHERE J_TGL = ? AND J_COLL_U_ID = ?", array(date("Y-m-d"), Input::get("userId")));

    if(count($jadwal) > 0) {
      foreach ($jadwal as $aData) {
        $cisId = DB::table("coll_check_in_start")->insertGetId(array(
          'CIS_WAKTU' => date("Y-m-d H:i:s"),
          'CIS_COLL_U_ID' => Input::get("userId"),
          'BU_ID' => $aData->{"BU_ID"},
          'CIS_LOKASI_LAT' => Input::get("lat"),
          'CIS_LOKASI_LNG' => Input::get("lng")
        ));
      }

      return composeReply2("SUCCESS", "Data check in start tersimpan");
    }
    else {
      return composeReply2("ERROR", "Tidak ada data jadwal per ".tglIndo(date("Y-m-d"), "SHORT"));
    }
  }

}
?>
