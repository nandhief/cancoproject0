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
