<?php
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('DB_NAME', 'collect');


$var = '06/29/2018';
$date = str_replace('/', '-', $var);
echo date('Y-m-d', strtotime($date))." | ";


$var1 = '6/29/2018';
$date1 =  implode("-", array_reverse(explode("/", $var1)));
echo $date1." | ";

$parts = explode('/', '29/06/2018');
$date2  = "$parts[2]-$parts[0]-$parts[1]";
echo $date2." | ";

function con2mysql($date) {
  $date = explode("-",$date);
  if ($date[0]<=9) { $date[0]="0".$date[0]; }
  if ($date[1]<=9) { $date[1]="0".$date[1]; }
  $date = array($date[2], $date[1], $date[0]);
 
 return $n_date=implode("-", $date);
 }

$date ='29/06/2018';
$dateTime = new DateTime($date);
$formatted_date=date_format ( $dateTime, 'Y-m-d' );
echo $formatted_date;