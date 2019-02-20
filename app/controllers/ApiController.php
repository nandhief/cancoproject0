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

    $schedules = DB::select("SELECT A.*,B.*,C.PRSH_ID, C.PRSH_NAMA, C.PRSH_JENIS_TIPE
        FROM coll_jadwal AS A
        INNER JOIN coll_batch_upload_data AS B ON A.BUD_ID = B.BUD_ID
        INNER JOIN coll_perusahaan AS C ON B.PRSH_ID = C.PRSH_ID
        WHERE A.J_TGL = ?  AND A.J_COLL_U_ID = ? AND A.J_STATUS = ? LIMIT 10 OFFSET ?", array(
            $periode, $user->U_ID, Input::get("status"), $start
        ));
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
                            WHERE A.J_TGL = ?  AND A.J_STATUS = ? AND A.PRSH_ID = ? AND B.BUD_COLL_U_ID = ?)
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

  public function search()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
    $customer = [];
    if (!empty(Input::get('q'))) {
        $search = Input::get('q');
        if (!empty($search)) {
            $customer = DB::select('SELECT cc.CUST_ID, cc.CUST_NAMA, cc.CUST_ALAMAT, cc.CUST_PONSEL, ct.REK NO_REK, ct.CAB, cp.PRSH_NAMA, cp.PRSH_ALAMAT
                        FROM coll_customers cc
                        INNER JOIN coll_tabungan ct ON ct.CUST_ID = cc.CUST_ID
                        INNER JOIN coll_perusahaan cp ON ct.PRSH_ID = cp.PRSH_ID
                        WHERE (cc.CUST_NAMA LIKE ? OR ct.REK = ?) AND ct.PRSH_ID = ?', [
                            '%'.$search.'%', $search, $user->PRSH_ID
                        ]);
        }
    }
    return composeReply2('SUCCESS', 'Cari Nasabah', $customer);
  }

  public function tabungan()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login", "ACTION_LOGIN");
    $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
    $rekening = Input::get('no_rek');
    $tabungan = DB::table('coll_tabungan')->where('REK', $rekening)->where('PRSH_ID', $user->PRSH_ID)->first();
    $get_nota = DB::table('coll_tabungan_history')->orderBy('NO_NOTA', 'desc')->first();
    $nota = empty($get_nota) ? 1 : ((int) $get_nota->NO_NOTA) + 1;
    if ($tabungan) {
        if ($tabungan->TUTUP) return composeReply2("ERROR", "Maaf nasabah " . $tabungan->CUST_NAMA . ' sudah ditutup', "ACTION_POST_TABUNGAN");
        if ($tabungan->SETOR_MINIMUM > (int) Input::get('setoran')) return composeReply2("ERROR", "Setor minimal tabungan " . $tabungan->CUST_NAMA . ' adalah ' . $tabungan->SETOR_MINIMUM, "ACTION_POST_TABUNGAN");
        $tab_history = DB::table('coll_tabungan_history')->insertGetId([
            'T_ID' => $tabungan->ID,
            'PRSH_ID' => $user->PRSH_ID,
            'KODE_GROUP' => $user->U_KODE_TABUNGAN,
            'COLL_ID' => $user->U_ID,
            'NO_REK' => $tabungan->REK,
            'NASABAH_ID' => $tabungan->CUST_ID,
            'NASABAH_NAMA' => $tabungan->CUST_NAMA,
            'NASABAH_ALAMAT' => $tabungan->CUST_ALAMAT,
            'SETORAN' => Input::get('setoran'),
            'KETERANGAN' => Input::get('keterangan'),
            'NO_NOTA' => $nota,
            'LAT' => Input::get("latitude"),
            'LONG' => Input::get("longitude"),
        ]);
        $data = DB::table('coll_tabungan_history')->where('TH_ID', $tab_history)->first();
        return composeReply2('SUCCESS', 'Detail Nasabah Menabung', $data);
    }
  }

  public function history()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
    $filter = empty(Input::get('periode')) ? date('Y-m-d') : Input::get('periode');
    $page = empty(Input::get('page')) ? 1 : (int) Input::get('page');
    $start = $page > 1 ? ($page * 10) - 10 : 0;
    $tab_history = DB::select('SELECT *
        FROM coll_tabungan_history cth
        INNER JOIN coll_tabungan ct ON ct.ID = cth.T_ID
        INNER JOIN coll_customers cc ON cc.CUST_ID = cth.NASABAH_ID
        WHERE cth.PRSH_ID = ? AND cth.COLL_ID = ? AND DATE(cth.TGL_SETORAN) = ?
        LIMIT 10 OFFSET ?', [
            $user->PRSH_ID,
            $user->U_ID,
            $filter,
            $start,
        ]);
    foreach ($tab_history as $key => $value) {
        $value->{'type'} = 'load';
    }
    return composeReply2('SUCCESS', 'Detail History Tabungan', $tab_history);
  }

  public function summary_tabungan()
  {
    if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
    if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
    $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
    $filter = empty(Input::get('periode')) ? date('Y-m-d') : Input::get('periode');
    $jumlah = 0;
    $total = 0;
    $summary = DB::select('SELECT COUNT(*) JUMLAH, SUM(SETORAN) TOTAL
        FROM coll_tabungan_history
        WHERE COLL_ID = ? AND DATE(TGL_SETORAN) = ? AND PRSH_ID = ?
        GROUP BY COLL_ID', [
            $user->U_ID,
            $filter,
            $user->PRSH_ID
        ]);
    if (empty($summary)) {
        $data = [
            'JUMLAH' => $jumlah,
            'TOTAL' => $total,
        ];
    } else {
        foreach ($summary as $key => $value) {
            $data = [
                'JUMLAH' => (int) $value->JUMLAH,
                'TOTAL' => (int) $value->TOTAL,
            ];
        }
    }
    return composeReply2('SUCCESS', 'Summary History Tabungan', $data);
  }
}
