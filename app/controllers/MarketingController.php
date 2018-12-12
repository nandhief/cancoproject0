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
            $pengajuan = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', null)->get();
            $survey = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 0)->get();
            $berkas = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 1)->get();
            $selesai = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 2)->get();
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

    public function api_index()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $list_data = [];
        $query = DB::table('coll_marketing')->where('PRSH_ID', $user->PRSH_ID);
        switch (Input::get('status')) {
            case 'pengajuan':
                $list_data = $query->where('STATUS', null)->get();
                break;
            case 'survey':
                $list_data = $query->where('STATUS', 0)->get();
                break;
            case 'berkas':
                $list_data = $query->where('STATUS', 1)->get();
                break;
            case 'selesai':
                $list_data = $query->where('STATUS', 2)->get();
                break;
            default:
                $list_data = $query->get();
                break;
        }
        return composeReply2('SUCCESS', 'Daftar Proses Pengajuan Nasabah', $list_data);
    }

    public function api_store()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        if(empty(Input::get("nama"))) return composeReply2("ERROR", "Nama tidak boleh kosong", "FIELD_REQUIRED");
        if(empty(Input::get("alamat"))) return composeReply2("ERROR", "Alamat tidak boleh kosong", "FIELD_REQUIRED");
        if(empty(Input::get("no_ktp"))) return composeReply2("ERROR", "No KTP tidak boleh kosong", "FIELD_REQUIRED");
        if(empty(Input::get("no_ponsel"))) return composeReply2("ERROR", "No Ponsel tidak boleh kosong", "FIELD_REQUIRED");
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
                    $exist = DB::table('coll_marketing')->where('KTP', Input::get('no_ktp'))
                                ->where('PRSH_ID', $user->PRSH_ID)->whereNull('STATUS')->first();
                    if ($exist) return composeReply2("ERROR", "Anda sudah melakukan pengajuan", "DATA_EXIST");
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
                    ]);
                    break;
                case 'survey':
                    $message = 'Data Survey ';
                    $query = DB::table('coll_marketing')->where('KTP', Input::get('no_ktp'))
                        ->where('PRSH_ID', $user->PRSH_ID)->whereNull('STATUS');
                    if ($query->first()) {
                        $marketing_id = $query->first()->ID;
                        $query->update([
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'STATUS' => 0,
                        ]);
                    } else {
                        return composeReply2('ERROR', 'Data tidak ada', null);
                    }
                    break;
                case 'berkas':
                    $message = 'Data Berkas ';
                    $query = DB::table('coll_marketing')->where('KTP', Input::get('no_ktp'))
                        ->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 0);
                    if ($query->first()) {
                        $marketing_id = $query->first()->ID;
                        $query->update([
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'STATUS' => 1,
                        ]);
                    } else {
                        return composeReply2('ERROR', 'Data tidak ada', null);
                    }
                    break;
                case 'selesai':
                    $message = 'Selesai ';
                    $query = DB::table('coll_marketing')->where('KTP', Input::get('no_ktp'))
                        ->where('PRSH_ID', $user->PRSH_ID)->where('STATUS', 1);
                    if ($query->first()) {
                        $marketing_id = $query->first()->ID;
                        $query->update([
                            'PATH_FOTO' => $upload_file,
                            'LAT' => Input::get('latitude'),
                            'LONG' => Input::get('longitude'),
                            'STATUS' => 2,
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
}
