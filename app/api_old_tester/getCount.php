<?php

include('./connection.php');

if ($_GET) {
 $tgl = $_GET['tgl'];
 $id_collector = $_GET['id_collector'];

$query = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_JADWAL' AND BUD_COLL_U_ID = '$id_collector'";
$data = mysqli_query($konek, $query);

$query2 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_BAYAR' AND BUD_COLL_U_ID = '$id_collector'";
$data2 = mysqli_query($konek, $query2);

$query3 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_BAYAR_PARSIAL' AND BUD_COLL_U_ID = '$id_collector'";
$data3 = mysqli_query($konek, $query3);

$query4 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_TIDAK_BAYAR' AND BUD_COLL_U_ID = '$id_collector'";
$data4 = mysqli_query($konek, $query4);

$query5 = "SELECT COUNT(*) AS datajumlah FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='ST_TIDAK_DITEMUKAN' AND BUD_COLL_U_ID = '$id_collector'";
$data5 = mysqli_query($konek, $query5);

$query6 = "SELECT BUD_PINJ_JUMLAH as jumlah , BUD_PINJ_JUMLAH_BAYAR as jumlah_bayar FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_COLL_U_ID = '$id_collector'";
$data6 = mysqli_query($konek, $query6);


$response = 1;
$dataCount = array();
if(mysqli_num_rows($data) >= 0) {

  while($hasil = mysqli_fetch_array($data)) {
      $dataCount["ST_JADWAL"] = $hasil[0];
   }

  while($hasil2 = mysqli_fetch_array($data2)) {
      $dataCount["ST_BAYAR"] = $hasil2[0];
   }

   while($hasil3 = mysqli_fetch_array($data3)) {
      $dataCount["ST_BAYAR_PARSIAL"] = $hasil3[0];
   }

   while($hasil4 = mysqli_fetch_array($data4)) {
      $dataCount["ST_TIDAK_BAYAR"] = $hasil4[0];
   }

   while($hasil5 = mysqli_fetch_array($data5)) {
      $dataCount["ST_TIDAK_DITEMUKAN"] = $hasil5[0];
   }

   $jumlah_bayar=0;
   $jumlah_tagih=0;
   while ($hasil6 = mysqli_fetch_assoc($data6)) {
   	 $jumlah_bayar=$jumlah_bayar+$hasil6['jumlah_bayar'];
   	 $jumlah_tagih=$jumlah_tagih+$hasil6['jumlah'];
   }
   $dataCount["TOTAL_PENAGIHAN"] = $jumlah_tagih;
   $dataCount["TOTAL_PEMBAYARAN"] = $jumlah_bayar;

  //  for ($x = 0; $x <= 10; $x++) {
  //   echo "The number is: $x <br>";
  // }


   echo json_encode($dataCount);
} else {
 $response["data"] = 0;
 $response["msg"] = "Maaf Data Tidak ada";
 echo json_encode($response);
}
}


?>