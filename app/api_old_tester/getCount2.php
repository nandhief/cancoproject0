<?php

include('connection.php');

if ($_GET) {
 $tgl = $_GET['tgl'];

 	$query = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_JADWAL'";
$data = mysqli_query($konek, $query);

$query2 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_BAYAR'";
$data2 = mysqli_query($konek, $query2);

$query3 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_BAYAR_PARSIAL'";
$data3 = mysqli_query($konek, $query3);

$query4 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_TIDAK_BAYAR'";
$data4 = mysqli_query($konek, $query4);

$query5 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_TIDAK_DITEMUKAN'";
$data5 = mysqli_query($konek, $query5);


$response = 1;
$dataCount = array();
if(mysqli_num_rows($data) >= 0) {
  while($hasil = mysqli_fetch_array($data)) {
  		$dataCount[] = $hasil;
   }
	// $dataCount[] = mysqli_fetch_array($data);
   
  while($hasil2 = mysqli_fetch_array($data2)) {
  		$dataCount[] = $hasil2;
   }

   while($hasil3 = mysqli_fetch_array($data3)) {
  		$dataCount[] = $hasil3;
   }

   while($hasil4 = mysqli_fetch_array($data4)) {
  		$dataCount[] = $hasil4;
   }

   while($hasil5 = mysqli_fetch_array($data5)) {
  		$dataCount[] = $hasil5;
   }


   echo json_encode($dataCount);
} else {
 $response["data"] = 0;
 $response["msg"] = "Maaf Data Tidak ada";
 echo json_encode($response);
}
}


?>