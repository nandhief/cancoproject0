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
    $status1 = Input::get("status1");
    $status2 = Input::get("status2");

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
