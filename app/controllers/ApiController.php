<?php

class ApiController extends BaseController
{

  public function updatePassword()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");

    $userData = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
    if($userData) {
      DB::table("coll_user")->where("U_ID", Input::get("userId"))->update([
        'U_PASSWORD' => Input::get('password'),
        'U_PASSWORD_HASH' => md5($userData->{"U_ID"}.Input::get('password')),
        'U_GANTIPASS' => true,
      ]);
      return composeReply2("SUCCESS", "Update Password data telah disimpan");
    }
    return composeReply2("ERROR", "User tidak terdaftar");
  }

  public function list()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();

    $periode = empty(Input::get('periode')) ? date('Y-m-d') : Input::get('periode');

    $page = empty(Input::get('page')) ? 1 : (int) Input::get('page');
    $start = $page > 1 ? ($page * 10) - 10 : 0;

    $schedules = DB::select("SELECT A.*,B.*,C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE FROM coll_jadwal AS A INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID WHERE A.J_TGL = ?  AND A.J_COLL_U_ID = ? AND A.J_STATUS = ? LIMIT 10 OFFSET ?", array($periode, $user->U_ID, Input::get("status"), $start));
    foreach ($schedules as $data) {
      $data->{"BUD_STATUS_INFO"} = getReferenceInfo("STATUS_COLLECTION", $data->{"BUD_STATUS"});
      $data->{"BUD_PINJ_TGL_KREDIT_FORMATTED"} = tglIndo($data->{"BUD_PINJ_TGL_KREDIT"},"SHORT");
      $data->{"BUD_PINJ_TGL_ANGS_FORMATTED"} = tglIndo($data->{"BUD_PINJ_TGL_ANGS"},"SHORT");
      $data->{"BUD_PINJ_TGL_JADWAL_FORMATTED"} = tglIndo($data->{"BUD_PINJ_TGL_JADWAL"},"SHORT");
      $data->{"BUD_PINJ_TGL_BAYAR_FORMATTED"} = tglIndo($data->{"BUD_PINJ_TGL_BAYAR"},"SHORT");
      $data->{"BUD_PINJ_POKOK_FORMATTED"} = number_format($data->{"BUD_PINJ_POKOK"});
      $data->{"BUD_PINJ_BUNGA_FORMATTED"} = number_format($data->{"BUD_PINJ_BUNGA"});
      $data->{"BUD_PINJ_DENDA_FORMATTED"} = number_format($data->{"BUD_PINJ_DENDA"});
      $data->{"BUD_PINJ_JUMLAH_FORMATTED"} = number_format($data->{"BUD_PINJ_JUMLAH"});
      $data->{"BUD_PINJ_JUMLAH_BAYAR_FORMATTED"} = number_format($data->{"BUD_PINJ_JUMLAH_BAYAR"});
    }
    return composeReply2("SUCCESS", "Data jadwal ", $schedules);
  }

  public function list_bayar()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();

    $periode = empty(Input::get('periode')) ? date('Y-m-d') : Input::get('periode');
    $status1 = empty(Input::get("status1")) ? '' : Input::get("status1");
    $status2 = empty(Input::get("status2")) ? '' : Input::get("status2");

    $page = empty(Input::get('page')) ? 1 : (int) Input::get('page');
    $start = $page > 1 ? ($page * 10) - 10 : 0;

    $bayar = DB::select("(SELECT A.*,B.*, C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE
                            FROM coll_jadwal AS A
                            INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID
                            INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID
                            WHERE A.J_TGL = ?  AND A.J_COLL_U_ID = ? AND A.J_STATUS = ?)
                        UNION
                        (SELECT A.*,B.*,C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE
                            FROM coll_jadwal AS A
                            INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID
                            INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID
                            WHERE A.J_TGL = ?  AND A.J_STATUS = ? AND A.PRSH_ID = ? AND A.J_COLL_U_ID != ?)
                        LIMIT 10 OFFSET ?", [
                          $periode, $user->U_ID, $status1, $periode, $status2, $user->PRSH_ID, $user->U_ID, $start
                        ]);
    foreach ($bayar as $aData) {
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
      $aData->{'type'} = 'load';
    }
    return composeReply2("SUCCESS", "Data jadwal ", $bayar);
  }

  public function jadwal()
  {
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
          dump('BARU');
          exit;
        } else {
            if(isset($fileName) && trim($fileName) != "") {
                if(in_array($fileExt, $arrFileExt)=== false)   return composeReply("ERROR","Harap pilih file Excel");
                if($fileSize > 2048000)                       return composeReply("ERROR","Harap pilih file Excel dengan ukuran max. 2 MB");

                $uploadFile = "uploads/jadwal-".createSlug($userId)."-".date("YmdHis").".".$fileExt;
                if(move_uploaded_file($fileTmp,$uploadFile) == TRUE) {
                    DB::beginTransaction();

                    /* Update File Upload */
                    DB::table("coll_batch_upload")->where('BU_ID', $dataTgl->BU_ID)->where('U_ID', $userId)->update(array(
                      'BU_FILE_PATH' => $uploadFile,
                    ));

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

                            $tglKredit = date("Y-m-d", strtotime(str_replace('/', '-', trim($table[$row]["TGL_REALISASI"]))));

                            $tglAngsur = date("Y-m-d", strtotime(str_replace('/', '-', trim($table[$row]["TGL_UPLOAD"]))));

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

                            $rekening = str_replace(',', '.', trim($table[$row]["NO_REKENING"]));
                            $id_nasabah = trim($table[$row]["ID_NASABAH"]);

                            $tKgroup = trim($table[$row]["KODE_GROUP"]);
                            $prshId = $userData->{"PRSH_ID"};

                            $nasabah = DB::table("coll_customers")->where("CUST_ID", $id_nasabah)->first();
                            if(empty($nasabah)) {
                                DB::table("coll_customers")->insert([
                                    'CUST_ID' => $id_nasabah,
                                    'CUST_NAMA' => trim($table[$row]["NAMA_NASABAH"]),
                                    'CUST_ALAMAT' => trim($table[$row]["ALAMAT"]),
                                    'CUST_PONSEL' => trim($table[$row]["NO_HP"])
                                ]);
                            } else {
                                // cek apakah ada perubahan data
                                if(trim(strtoupper($nasabah->{"CUST_NAMA"})) != trim(strtoupper($table[$row]["NAMA_NASABAH"]))) {
                                    DB::table("coll_customers")->where("CUST_ID", $id_nasabah)
                                        ->update([
                                            'CUST_NAMA' => $table[$row]["NAMA_NASABAH"]
                                        ]);
                                }
                                if(trim(strtoupper($nasabah->{"CUST_ALAMAT"})) != trim(strtoupper($table[$row]["ALAMAT"]))) {
                                    DB::table("coll_customers")->where("CUST_ID", $id_nasabah)
                                        ->update(array(
                                            'CUST_ALAMAT' => $table[$row]["ALAMAT"]
                                        ));
                                }
                                if(trim(strtoupper($nasabah->{"CUST_PONSEL"})) != trim(strtoupper($table[$row]["NO_HP"]))) {
                                    DB::table("coll_customers")->where("CUST_ID", $id_nasabah)
                                        ->update([
                                            'CUST_PONSEL' => $table[$row]["NO_HP"]
                                        ]);
                                }
                            }

                            //cek dl apakah sdh ada data pinjaman
                            $pinjaman = DB::table("coll_pinjaman")->where("PINJ_ID", $rekening)->first();
                            if(empty($pinjaman)) {
                                DB::table("coll_pinjaman")->insert([
                                    'PINJ_ID' => str_replace(',', '.', trim($table[$row]["NO_REKENING"])),
                                    'CUST_ID' => $id_nasabah,
                                    'PINJ_JUMLAH' => trim($table[$row]["JML_PINJAMAN"]),
                                    'PINJ_MASA_KREDIT' => trim($table[$row]["JANGKA_WAKTU"]),
                                    'PINJ_TGL_KREDIT' => $tglKredit
                                ]);
                            }

                            $collID = DB::table("coll_user")->where("U_KODE_GROUP", "=", $table[$row]["KODE_GROUP"])->first();
                            if (is_null($collID)) {
                                return composeReply("ERROR","KODE_GROUP " .  $tKgroup . ' Tidak Berkaitan dengan collector manapun');
                            }
                            if ($collID->PRSH_ID != $prshId) {
                                $collID = DB::table("coll_user")->whereNull("U_KODE_GROUP")->first();
                            }
                            $collect_id = $collID->{"U_ID"};
                            $coll_nama = $collID->{"U_NAMA"};

                            // check data penagihan hari ini
                            $penagihan = DB::table('coll_batch_upload_data')
                                    ->where('BUD_PINJ_TGL_JADWAL', $tglAngsur)
                                    ->where('PRSH_ID', $userData->{"PRSH_ID"})
                                    ->where('BUD_PINJ_ID', $rekening)->first();
                            if ($penagihan) {
                                switch ($penagihan->BUD_STATUS) {
                                    case 'ST_JADWAL':
                                        DB::table("coll_batch_upload_data")
                                        ->where('BUD_PINJ_TGL_JADWAL', $tglAngsur)
                                        ->where('BUD_PINJ_ID', $rekening)
                                        ->update([
                                            'BUD_KODE_GROUP' => $table[$row]["KODE_GROUP"],
                                            'BUD_COLL_U_ID' => $collect_id,
                                            'BUD_CAB' => trim($table[$row]["CAB"]),
                                            'BUD_PINJ_PERIODE' => trim($table[$row]["ANGSURAN_KE"]),
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
                                            'BUD_PINJ_TGL_KREDIT' => $tglKredit,
                                            'BUD_PINJ_TGL_ANGS' => $tglAngsur,
                                            'BUD_PINJ_TGL_JADWAL' => $tglAngsur,
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
                                            'BUD_LOKASI_LAT' => '0',
                                            'BUD_LOKASI_LNG' => '0',
                                            'PRSH_ID' => $userData->{"PRSH_ID"}
                                        ]);
                                        $budId = DB::table('coll_batch_upload_data')
                                                    ->where('BUD_STATUS', 'ST_JADWAL')
                                                    ->where('PRSH_ID', $userData->{"PRSH_ID"})
                                                    ->where('BUD_PINJ_TGL_JADWAL', $tglAngsur)
                                                    ->where('BUD_PINJ_ID', $rekening)->first();
                                        break;

                                    default:
                                        break;
                                }
                            } else {
                                DB::table("coll_batch_upload_data")->insert([
                                    'BU_ID' => $dataTgl->BU_ID,
                                    'BUD_KODE_GROUP' => $table[$row]["KODE_GROUP"],
                                    'BUD_COLL_U_ID' => $collect_id,
                                    'BUD_CAB' => trim($table[$row]["CAB"]),
                                    'BUD_PINJ_ID' => $rekening,
                                    'BUD_PINJ_PERIODE' => trim($table[$row]["ANGSURAN_KE"]),
                                    'BUD_CUST_ID' => $id_nasabah,
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
                                    'BUD_PINJ_TGL_KREDIT' => $tglKredit,
                                    'BUD_PINJ_TGL_ANGS' => $tglAngsur,
                                    'BUD_PINJ_TGL_JADWAL' => $tglAngsur,
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
                                  ]);
                                $budId = DB::table('coll_batch_upload_data')
                                            ->where('BUD_STATUS', 'ST_JADWAL')
                                            ->where('PRSH_ID', $userData->{"PRSH_ID"})
                                            ->where('BUD_PINJ_TGL_JADWAL', $tglAngsur)
                                            ->where('BUD_PINJ_ID', $rekening)->first();
                            }
                            if (isset($budId)) {
                                $aTgl = date('Y-m-d');
                                $cek = DB::table("coll_jadwal")
                                            ->where("J_TGL", $tglAngsur)
                                            ->where("CUST_ID", $id_nasabah)
                                            ->where("PINJ_ID", $rekening)
                                            ->where("J_PINJ_JUMLAH", $bayarJumlah)
                                            ->first();

                                if(is_null($cek)) {
                                    $jId = DB::table("coll_jadwal")->insertGetId([
                                                'J_TGL' => $tglAngsur,
                                                'BU_ID' => $dataTgl->BU_ID,
                                                'BUD_ID' => $budId->BUD_ID,
                                                'CUST_ID' => $id_nasabah,
                                                'PINJ_ID' => $rekening,
                                                'J_PINJ_JUMLAH' => $bayarJumlah,
                                                'J_PINJ_JUMLAH_BAYAR' => '0',
                                                'J_STATUS' => 'ST_JADWAL',
                                                'PRSH_ID' => $userData->{"PRSH_ID"},
                                                'J_COLL_U_ID' => $collect_id
                                            ]);
                                } else {
                                    DB::table("coll_jadwal")
                                        ->where("J_TGL", $aTgl)
                                        ->where("CUST_ID", $id_nasabah)
                                        ->where("PINJ_ID", $rekening)
                                        ->where("J_PINJ_JUMLAH", $bayarJumlah)
                                        ->update([
                                            'BUD_ID' => $budId->BUD_ID,
                                            'BU_ID' => $dataTgl->BU_ID
                                        ]);
                                }
                            }

                        }
                    }
                    DB::commit();
                    return composeReply("SUCCESS", "Data telah disimpan dan data telah diperbarui dengan upload terbaru");
                }
            } else {
                return composeReply("ERROR","Proses upload gagal (file upload tidak terdeteksi server)");
            }
        }
      } else {
        return composeReply("ERROR","Harap sertakan file untuk diunggah");
      }
    } else {
      Session::flush();
      return composeReply("ERROR","Silahkan login terlebih dahulu");
    }
  }

  public function get() {
    echo 'GET';
  }

  public function post() {
    echo 'POST';
  }

  public function put() {
    echo 'PUT';
  }

  public function delete() {
    echo 'DELETE';
  }
}
