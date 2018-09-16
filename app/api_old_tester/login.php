<?php
header('Content-type: application/json');
date_default_timezone_set('Asia/Jakarta');

include('./connection.php');

if(isset($_POST['email']) && isset($_POST['password'])) {

	//variabel post
	$loginEmail = mysqli_real_escape_string($konek, $_POST['email']);
    //$password = md5($_POST['password']);
    $password = mysqli_real_escape_string($konek, $_POST["password"]);
    //3.1.2 Checking the values are existing in the database or not
    $query = "SELECT a.*, b.* FROM coll_user AS a INNER JOIN coll_perusahaan AS b ON a.PRSH_ID = b.PRSH_ID WHERE (a.U_ID = '$loginEmail' OR a.U_EMAIL = '$loginEmail' OR a.U_TELPON = '$loginEmail') AND a.U_PASSWORD='$password'";

    $result = mysqli_query($konek, $query) or die(mysqli_error($konek));
    $count = mysqli_num_rows($result);

    //cek variabel untuk ditampilkan di json
    if(mysqli_num_rows($result) <= 0) {
    	$response["error"] = TRUE;
    	$response["error_show"] = "Periksa lagi data login anda";
    	echo json_encode($response);
    } else {
    	$row = mysqli_fetch_array($result);
    	
    	//update data 
    	$user_id = $row["U_ID"];
    	$now = date('Y-m-d h:i:s');
    	$loginToken = substr(md5($row["U_NAMA"].$now), 0, 30); 

    	//cek variabel $row untuk menampilkan response data yang sesuai
    	if($row["U_GROUP_ROLE"] == "GR_COLLECTOR") {
    		if($row["U_STATUS"] == "USER_ACTIVE") {
    			$token = "UPDATE coll_user SET U_LOGIN_WAKTU = '$now', U_LOGIN_TOKEN = '$loginToken' WHERE U_ID = '$user_id'";
    			$data = mysqli_query($konek, $token);
    			$response["token_new"] = $data;
    			//tampilkam response 
    			$response["error"] = FALSE;
    			$response["collector_id"] = $row["U_ID"];
    			$response["collector_nama"] = $row["U_NAMA"];
    			$response["collector_rule"] = $row["U_GROUP_ROLE"];
    			$response["collector_telpon"] = $row["U_TELPON"];
    			$response["collector_email"] = $row["U_EMAIL"];
    			$response["collector_login_waktu"] = $row["U_LOGIN_WAKTU"];
    			$response["collector_token"] = $row["U_LOGIN_TOKEN"];
    			$response["collector_prsh_id"] = $row["PRSH_ID"];
                $response["collector_prsh_nama"] = $row["PRSH_NAMA"];
    			echo json_encode($response);
    		} else {
    			$response["error"] = TRUE;
    			$response["err_show"] = "Akun anda sudah tidak aktif,hubungi admin untuk info lebih lanjut";
    			echo json_encode($response);
    		}
    	} else {
    		$response["error"] = TRUE;
    		$response["err_show"] = "Maaf,anda tidak ada hak akses disini";
            echo json_encode($response);
    	}
    }
} else {
	$response['error'] = TRUE;
	$response['err_show'] = "Parameter yang anda masukkan salah";
	echo json_encode($response);
}

?>