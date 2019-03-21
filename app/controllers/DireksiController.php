<?php

class DireksiController extends \BaseController
{
    public function index()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $data = DB::select('SELECT *
                    FROM coll_batch_upload cbu
                    INNER JOIN coll_perusahaan cp ON cp.PRSH_ID = cbu.PRSH_ID
                    WHERE cbu.BU_TGL <= ? AND cbu.BU_TYPE = \'BU_JADWAL\' AND cp.PRSH_ID = ? ORDER BY cbu.BU_ID DESC', [
                        date('Y-m-d'),
                        $user->PRSH_ID
                    ]);
            return View::make('dashboard.direksi.index')
                        ->with("ctlUserData", $user)
                        ->with('data', $data)
                        ->with("ctlNavMenu", "mCollMonitoring");
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function monitor_jadwal($id)
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $collect = DB::select("SELECT *
            FROM coll_batch_upload_data cbud
            INNER JOIN coll_user cu ON cu.U_ID = cbud.BUD_COLL_U_ID
            WHERE cbud.BU_ID = ? AND BUD_STATUS != 'ST_JADWAL' AND cu.PRSH_ID = ? GROUP BY BUD_COLL_U_ID", [
                $id,
                $user->PRSH_ID
            ]);
            return composeReply2('SUCCESS', 'Data Kolektor', $collect);
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

    public function monitor_route()
    {
        if (Session::has('SESSION_USER_ID') && Session::has('SESSION_LOGIN_TOKEN')) {
			if(!isLoginValid(Session::get('SESSION_USER_ID'), Session::get('SESSION_LOGIN_TOKEN'))) {
				Session::flush();
				return Redirect::to('login')->with('ctlError','Please login to access system');
            }
            $user = DB::table('coll_user')->where('U_ID', Session::get('SESSION_USER_ID', ''))->first();
            $collect = DB::select("SELECT *
            FROM coll_batch_upload_data cbud
            INNER JOIN coll_user cu ON cu.U_ID = cbud.BUD_COLL_U_ID
            WHERE cbud.BU_ID = ? AND BUD_STATUS != 'ST_JADWAL' AND BUD_COLL_U_ID = ? AND cu.PRSH_ID = ? ", [
                Input::get('bu_id'),
                Input::get('collect'),
                $user->PRSH_ID
            ]);
            return composeReply2('SUCCESS', 'Penagihan Kolektor', $collect);
        } else {
			Session::flush();
			return Redirect::to('login')->with('ctlError','Harap login terlebih dahulu');
		}
    }

	public function api_monitor()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $data = DB::select('SELECT *
                FROM coll_batch_upload cbu
                INNER JOIN coll_perusahaan cp ON cp.PRSH_ID = cbu.PRSH_ID
                WHERE cbu.BU_TGL <= ? AND cbu.BU_TYPE = \'BU_JADWAL\' AND cp.PRSH_ID = ? ORDER BY cbu.BU_ID DESC', [
                    date('Y-m-d'),
                    $user->PRSH_ID
                ]);
        return View::make('dashboard.direksi.maps')
                    ->with('data', $data);
    }

    public function api_monitor_list()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $data = DB::select('SELECT *
                FROM coll_batch_upload cbu
                INNER JOIN coll_perusahaan cp ON cp.PRSH_ID = cbu.PRSH_ID
                WHERE cbu.BU_TGL <= ? AND cbu.BU_TYPE = \'BU_JADWAL\' AND cp.PRSH_ID = ? ORDER BY cbu.BU_ID DESC', [
                    date('Y-m-d'),
                    $user->PRSH_ID
                ]);
        return composeReply2('SUCCESS', 'Data Jadwal Penagihan', $data);
    }

    public function api_monitor_jadwal($id)
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $collect = DB::select("SELECT *
        FROM coll_batch_upload_data cbud
        INNER JOIN coll_user cu ON cu.U_ID = cbud.BUD_COLL_U_ID
        WHERE cbud.BU_ID = ? AND BUD_STATUS != 'ST_JADWAL' AND cu.PRSH_ID = ? GROUP BY BUD_COLL_U_ID", [
            $id,
            $user->PRSH_ID
        ]);
        return composeReply2('SUCCESS', 'Data Kolektor', $collect);
    }

    public function api_monitor_route()
    {
        if(empty(Input::get("userId"))) return composeReply2("ERROR", "Invalid user ID", "ACTION_LOGIN");
        if(empty(Input::get("loginToken"))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        if(!isLoginValid(Input::get('userId'), Input::get('loginToken'))) return composeReply2("ERROR", "Invalid login token", "ACTION_LOGIN");
        $user = DB::table('coll_user')->where('U_ID', Input::get("userId"))->first();
        $collect = DB::select("SELECT *
        FROM coll_batch_upload_data cbud
        INNER JOIN coll_user cu ON cu.U_ID = cbud.BUD_COLL_U_ID
        WHERE cbud.BU_ID = ? AND BUD_STATUS != 'ST_JADWAL' AND BUD_COLL_U_ID = ? AND cu.PRSH_ID = ? ", [
            Input::get('bu_id'),
            Input::get('collect'),
            $user->PRSH_ID
        ]);
        return composeReply2('SUCCESS', 'Penagihan Kolektor', $collect);
    }
}
