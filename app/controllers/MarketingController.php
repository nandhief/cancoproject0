<?php

class MarketingController extends \BaseController
{

	public function index()
	{
		if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
			}
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $pengajuan = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'pengajuan')->get();
            $survey = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'survey')->get();
            $berkas = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'berkas')->get();
            $selesai = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'selesai')->get();
            return View::make('dashboard.marketing.index')
                    ->with("ctlUserData", $user)
                    ->with('pengajuan', $pengajuan)
                    ->with('survey', $survey)
                    ->with('berkas', $berkas)
                    ->with('selesai', $selesai)
                    ->with("ctlNavMenu", "mCollMarketing");
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
                return composeReply2("ERROR", "Please login to access system", "ACTION_LOGIN");
			}
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $update = DB::table('coll_marketing')->where('ID', Input::get('id'))->update([
                'PROSES' => Input::get('proses'),
            ]);
            if ($update) {
                $nasabah = DB::table('coll_marketing')->where('ID', Input::get('id'))->first();
                if ($nasabah->STATUS == 'berkas' && $nasabah->PROSES == true) {
                     DB::table('coll_marketing')->where('ID', Input::get('id'))->update([
                        'STATUS' => 'selesai',
                    ]);
                }
                if ($nasabah->STATUS == 'berkas' && $nasabah->PROSES == false) {
                     DB::table('coll_marketing')->where('ID', Input::get('id'))->update([
                        'STATUS' => 'selesai',
                    ]);
                }
                return composeReply2('SUCCESS', 'Update PROSES Berhasil');
            }
        } else {
			Session::flush();
			return composeReply2("ERROR", "Please login to access system", "ACTION_LOGIN");
		}
    }

    public function api_index()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        switch (Input::get('status')) {
            case 'pengajuan':
                if (Input::get('proses')) {
                    $pengajuan = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'pengajuan')->where('PROSES', true)->get();
                } else {
                    $pengajuan = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'pengajuan')->get();
                }
                return composeReply2('SUCCESS', 'Daftar Proses Pengajuan Nasabah', $pengajuan);
                break;
            case 'survey':
                if (Input::get('proses')) {
                    $survey = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'survey')->where('PROSES', true)->get();
                } else {
                    $survey = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'survey')->get();
                }
                return composeReply2('SUCCESS', 'Daftar Proses Survey Pengajuan Nasabah', $survey);
                break;
            case 'berkas':
                if (Input::get('proses')) {
                    $berkas = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'berkas')->where('PROSES', true)->get();
                } else {
                    $berkas = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'berkas')->get();
                }
                return composeReply2('SUCCESS', 'Daftar Proses Survey Pengajuan Nasabah', $berkas);
                break;
            case 'selesai':
                if (Input::get('proses')) {
                    $selesai = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'selesai')->where('PROSES', true)->get();
                } else {
                    $selesai = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'selesai')->get();
                }
                return composeReply2('SUCCESS', 'Daftar Proses Survey Pengajuan Nasabah', $selesai);
                break;
            default:
                $pengajuan = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'pengajuan')->get();
                $survey = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'survey')->get();
                $berkas = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'berkas')->get();
                $selesai = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'selesai')->get();
                return composeReply2('SUCCESS', 'Daftar Proses Pengajuan Nasabah', compact('pengajuan', 'survey', 'berkas', 'selesai'));
                break;
        }
    }

    public function get_index($type = null)
    {
        switch ($type) {
            case 'pengajuan':
                # code...
                break;

            default:
                $pengajuan = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'pengajuan')->where('PROSES', true)->get();
                $survey = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'survey')->where('PROSES', true)->get();
                $berkas = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'berkas')->where('PROSES', true)->get();
                $selesai = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'selesai')->where('PROSES', true)->get();
                break;
        }
    }

    public function api_store()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        if(empty(Input::get("no_ktp"))) return composeReply2("ERROR", "No KTP tidak boleh kosong", "FIELD_REQUIRED");
        if(empty(Input::get("status"))) return composeReply2("ERROR", "Status tidak boleh kosong", "FIELD_REQUIRED");
        if(empty(Input::get("latitude"))) return composeReply2("ERROR", "Latitude tidak boleh kosong", "FIELD_REQUIRED");
        if(empty(Input::get("longitude"))) return composeReply2("ERROR", "Longitude tidak boleh kosong", "FIELD_REQUIRED");
        if(!isset($_FILES['path_foto'])) return composeReply2("ERROR", "Foto tidak boleh kosong", "FIELD_REQUIRED");
        $filename = date('YmdHis_') . $user->U_ID . '_' . $_FILES['path_foto']['name'];
        $file = $_FILES['path_foto']['tmp_name'];
        $fileext = explode('.', $filename);
        $ext = strtolower(end($fileext));
        if (! in_array($ext, ['jpg', 'jpeg', 'png'])) return composeReply2('ERROR', 'File harus gambar', 'FIELD_INVALID');
        $upload_file = "uploads/pengajuan-" . $filename;
        if (move_uploaded_file($file, $upload_file)) {
            switch (Input::get('status')) {
                case 'pengajuan':
                    if(empty(Input::get("nama"))) return composeReply2("ERROR", "Nama tidak boleh kosong", "FIELD_REQUIRED");
                    if(empty(Input::get("alamat"))) return composeReply2("ERROR", "Alamat tidak boleh kosong", "FIELD_REQUIRED");
                    if(empty(Input::get("no_ponsel"))) return composeReply2("ERROR", "No Ponsel tidak boleh kosong", "FIELD_REQUIRED");
                    $exist = DB::select('SELECT * FROM coll_marketing cm
                            INNER JOIN coll_marketing_history cmh ON cmh.MARKETING_ID = cm.ID
                            WHERE cm.KTP = ? AND cm.PRSH_ID = ? ', [
                                Input::get("no_ktp"),
                                $user->PRSH_ID,
                            ]);
                    if ($exist) {
                        switch ($exist[count($exist) -1]->STATUS) {
                            case 'pengajuan':
                                if (is_null($exist[count($exist) -1]->PROSES)) {
                                    return composeReply2("ERROR", "Mohon Maaf Anda sudah melakukan pengajuan");
                                }
                                break;
                            case 'survey':
                                if (is_null($exist[count($exist) -1]->PROSES)) {
                                    return composeReply2("ERROR", "Mohon Maaf Anda sudah melakukan pengajuan dan dalam proses survey");
                                }
                                break;
                            case 'berkas':
                                if (is_null($exist[count($exist) -1]->PROSES)) {
                                    return composeReply2("ERROR", "Mohon Maaf Anda sudah melakukan pengajuan dan dalam proses validasi berkas");
                                }
                                break;
                        }
                    }
                    $message = 'Data Pengajuan ';
                    $marketing_id = DB::table('coll_marketing')->insertGetId([
                        'NAMA' => Input::get('nama'),
                        'ALAMAT' => Input::get('alamat'),
                        'KTP' => Input::get('no_ktp'),
                        'PONSEL' => Input::get('no_ponsel'),
                        'PATH_FOTO' => $upload_file,
                        'LAT' => Input::get('latitude'),
                        'LONG' => Input::get('longitude'),
                        'PRSH_ID' => $user->PRSH_ID,
                        'KETERANGAN' => Input::get('keterangan'),
                    ]);
                    DB::table('coll_marketing_history')->insert([
                        'MARKETING_ID' => $marketing_id,
                        'PATH_FOTO' => $upload_file,
                        'LAT' => Input::get('latitude'),
                        'LONG' => Input::get('longitude'),
                        'INFO' => Input::get('status'),
                        'KETERANGAN' => Input::get('keterangan'),
                        'USER_ID' => $user->U_ID,
                    ]);
                    break;
                case 'survey':
                    $message = 'Data Survey ';
                    $query = DB::table('coll_marketing')->where('KTP', Input::get('no_ktp'))
                        ->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'pengajuan')->where('PROSES', true);
                    if ($query->first()) {
                        $marketing_id = $query->first()->ID;
                        $query->update([
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'STATUS' => Input::get('status'),
                            'PROSES' => null,
                            'KETERANGAN' => Input::get('keterangan'),
                        ]);
                        DB::table('coll_marketing_history')->insert([
                            'MARKETING_ID' => $marketing_id,
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'INFO' => Input::get('status'),
                            'KETERANGAN' => Input::get('keterangan'),
                            'USER_ID' => $user->U_ID,
                        ]);
                    } else {
                        return composeReply2('ERROR', 'Data tidak ada untuk survey', null);
                    }
                    break;
                case 'berkas':
                    $message = 'Data Berkas ';
                    $query = DB::table('coll_marketing')->where('KTP', Input::get('no_ktp'))
                        ->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 'survey')->where('PROSES', true);
                    if ($query->first()) {
                        $marketing_id = $query->first()->ID;
                        $query->update([
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'STATUS' => Input::get('status'),
                            'PROSES' => null,
                            'KETERANGAN' => Input::get('keterangan'),
                        ]);
                        DB::table('coll_marketing_history')->insert([
                            'MARKETING_ID' => $marketing_id,
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'INFO' => Input::get('status'),
                            'KETERANGAN' => Input::get('keterangan'),
                            'USER_ID' => $user->U_ID,
                        ]);
                    } else {
                        return composeReply2('ERROR', 'Data tidak ada', null);
                    }
                    break;
            }

            $marketing = DB::table('coll_marketing')->where('id', $marketing_id)->first();
        };
        return composeReply2("SUCCESS", $message, $marketing);
    }

    public function api_search()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $customer = [];
        if (!empty(Input::get('q')) && !empty(Input::get('status'))) {
            switch (Input::get('status')) {
                case 'survey':
                    $status = 'pengajuan';
                    break;
                case 'berkas':
                    $status = 'survey';
                    break;
            }
            $search = Input::get('q');
            $customer = DB::table('coll_marketing')->whereRaw('(NAMA LIKE ? OR KTP LIKE ?) AND PRSH_ID = ? AND STATUS = ?  AND PROSES = 1', [
                '%'.$search.'%',
                $search,
                $user->PRSH_ID,
                $status,
            ])->get();
        }
        return composeReply2('SUCCESS', 'Cari Nasabah ' . ucwords(Input::get('status')), $customer);
    }

    public function api_search_status($type = null)
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $customer = [];
        if (!empty(Input::get('q')) && !empty($type)) {
            switch ($type) {
                case 'survey':
                    $status = 'pengajuan';
                    break;
                case 'berkas':
                    $status = 'survey';
                    break;
            }
            $search = Input::get('q');
            $customer = DB::table('coll_marketing')->whereRaw('(NAMA LIKE ? OR KTP LIKE ?) AND PRSH_ID = ? AND STATUS = ?  AND PROSES = 1', [
                '%'.$search.'%',
                $search,
                $user->PRSH_ID,
                $status,
            ])->get();
        }
        return composeReply2('SUCCESS', 'Cari Nasabah ' . ucwords($type), $customer);
    }
}
