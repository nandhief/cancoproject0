<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//$con = mysqli_connect('localhost', 'root', '', 'collect');
include('./connection.php');

if ($_GET) {
	$tgl = $_GET['tgl'];
	$status = $_GET['status'];
	$id_collector = $_GET['id_collector'];

$query_tampil_data = mysqli_query($konek, "SELECT * FROM coll_batch_upload_data WHERE BUD_PINJ_TGL_JADWAL='$tgl' AND BUD_STATUS='$status' AND BUD_COLL_U_ID = '$id_collector'") or die(mysql_error());
				$array_json = array();
				while ($data = mysqli_fetch_assoc($query_tampil_data)) {
				$array_json[]=$data;
		}
		echo json_encode($array_json);	
}