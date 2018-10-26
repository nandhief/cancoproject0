<?php
header('Content-type: application/json');
//timezone_location_get('Asia/jakarta');
date_default_timezone_set('Asia/Jakarta');
$response = array();

include('./connection.php');

 $now = date('Y-m-d h:i:s');
 $query = "SELECT * FROM coll_user";

 $result = mysqli_query($konek, $query) or die(mysqli_error($konek));

 $row = mysqli_fetch_array($result);
 $row1 = $row["U_LOGIN_TOKEN"];

 //$loginToken = substr(md5($row["U_NAMA"].$now), 0, 30);

 //echo $loginToken. "</br>";
 //echo $now;

 //cek fungsi baru
 if($_POST["userId"]) {
 	$user = $_POST["userId"];
 	$w =  "SELECT * FROM coll_user WHERE U_ID = '$user'";
 	$data = mysqli_query($konek, $w);
 	//$data1 - mysqli_fetch_array($data);
 	//$token = $data["U_LOGIN_TOKEN"];

 	if($data["U_LOGIN_TOKEN"]) {
		$response["error"] = False;
		$response["msg"] = "Token sama persis";
	} else {
		$response["error"] = TRUE;
		$response["msg"] = "Silahkan login kembali";
	}


echo json_encode($response);
 }
 //new
 <?php
              //if(isset($ctlRefGroupRole) && count($ctlRefGroupRole) > 0) {
                //foreach ($ctlRefGroupRole as $aData) {
                  //?>
                  <!-- <option value="<//?php echo $aData->{"R_ID"}; ?>"><//?php echo $aData->{"R_INFO"}; ?></option> -->
                  <?php
                //}
              //}
              ?>
?>