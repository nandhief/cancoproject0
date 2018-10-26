<?php
function asset_url() {
  return URL::to('/');
}

function antiInjection($data) {
  $filter_sql = mysqli_real_escape_string(stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES))));
  return $filter_sql;
}

function composeReply($status,$msg,$payload = null) { //NON-LARAVEL WAY
  header("Content-Type: application/json");
	$reply = json_encode(array(
						"SENDER" => "Collecting Mgmt System",
						"STATUS" => $status,
						"MESSAGE" => $msg,
            "PAYLOAD" => $payload));
	
	return $reply;
}

function composeReply2($status,$msg,$payload = null) { //LARAVEL WAY
  $reply = json_encode(array(
            "SENDER" => "Pintech Mgmt System",
            "STATUS" => $status,
            "MESSAGE" => $msg,
            "PAYLOAD" => $payload));
  
  return Response::make($reply, '200')->header('Content-Type', 'application/json');
}

function formatYMD($dateDMY) {
	if($dateDMY != "") {
		$a = explode("-",$dateDMY);
		return $a[2]."-".$a[1]."-".$a[0];
	}
	else {
		return "0000-00-00";
	}
}

function formatDMY($dateYMD) {
	if($dateYMD != "") {
		$a = explode("-",$dateYMD);
		return $a[2]."-".$a[1]."-".$a[0];
	}
	else {
		return "0000-00-00";
	}
}

function getLastQuery() {
  $queries = DB::getQueryLog();
  $sql = end($queries);
	
  if( ! empty($sql['bindings'])) {
    $pdo = DB::getPdo();
    foreach($sql['bindings'] as $binding) {
      $sql['query'] =
        preg_replace('/\?/', $pdo->quote($binding),
          $sql['query'], 1);
    }
  }
	
  return $sql['query'];
}

function isJSON($string){
  json_decode($string);
  return (json_last_error() == JSON_ERROR_NONE);
}

function cURLPost($url,$params) {
  $postData = '';
  //create name value pairs separated by &
  foreach($params as $k => $v) { 
    $postData .= $k . '='.$v.'&'; 
  }
  rtrim($postData, '&');

  $ch = curl_init();  

  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch,CURLOPT_HEADER, false); 
  curl_setopt($ch, CURLOPT_POST, count($postData));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    

  $output = curl_exec($ch);

  curl_close($ch);
  return $output; 
}

function tglIndo($tgl,$mode) {
  if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00") {
    $t = explode("-",$tgl);
    $bln = array();
    $bln["01"]["LONG"] = "Januari";
    $bln["01"]["SHORT"] = "Jan";
    $bln["1"]["LONG"] = "Januari";
    $bln["1"]["SHORT"] = "Jan";
    $bln["02"]["LONG"] = "Februari";
    $bln["02"]["SHORT"] = "Feb";
    $bln["2"]["LONG"] = "Februari";
    $bln["2"]["SHORT"] = "Feb";
    $bln["03"]["LONG"] = "Maret";
    $bln["03"]["SHORT"] = "Mar";
    $bln["3"]["LONG"] = "Maret";
    $bln["3"]["SHORT"] = "Mar";   
    $bln["04"]["LONG"] = "April";
    $bln["04"]["SHORT"] = "Apr";
    $bln["4"]["LONG"] = "April";
    $bln["4"]["SHORT"] = "Apr";
    $bln["05"]["LONG"] = "Mei";
    $bln["05"]["SHORT"] = "Mei";
    $bln["5"]["LONG"] = "Mei";
    $bln["5"]["SHORT"] = "Mei";
    $bln["06"]["LONG"] = "Juni";
    $bln["06"]["SHORT"] = "Jun";
    $bln["6"]["LONG"] = "Juni";
    $bln["6"]["SHORT"] = "Jun";
    $bln["07"]["LONG"] = "Juli";
    $bln["07"]["SHORT"] = "Jul";
    $bln["7"]["LONG"] = "Juli";
    $bln["7"]["SHORT"] = "Jul";
    $bln["08"]["LONG"] = "Agustus";
    $bln["08"]["SHORT"] = "Ags";
    $bln["8"]["LONG"] = "Agustus";
    $bln["8"]["SHORT"] = "Ags";
    $bln["09"]["LONG"] = "September";
    $bln["09"]["SHORT"] = "Sep";
    $bln["9"]["LONG"] = "September";
    $bln["9"]["SHORT"] = "Sep";
    $bln["10"]["LONG"] = "Oktober";
    $bln["10"]["SHORT"] = "Okt";
    $bln["11"]["LONG"] = "November";
    $bln["11"]["SHORT"] = "Nov";
    $bln["12"]["LONG"] = "Desember";
    $bln["12"]["SHORT"] = "Des";
    
    $b = $t[1];
    
    if (strpos($t[2], ":") === false) { //tdk ada format waktu
      $jam = "";
    }
    else {
      $j = explode(" ",$t[2]);
      $t[2] = $j[0];
      $jam = $j[1];
    }
    
    return $t[2]."-".$bln[$b][$mode]."-".$t[0]." ".$jam;
  }
  else {
    return "-";
  }
}

function bulanIndo($b,$mode) {
  $bln["01"]["LONG"] = "Januari";
  $bln["01"]["SHORT"] = "Jan";
  $bln["1"]["LONG"] = "Januari";
  $bln["1"]["SHORT"] = "Jan";
  $bln["02"]["LONG"] = "Februari";
  $bln["02"]["SHORT"] = "Feb";
  $bln["2"]["LONG"] = "Februari";
  $bln["2"]["SHORT"] = "Feb";
  $bln["03"]["LONG"] = "Maret";
  $bln["03"]["SHORT"] = "Mar";
  $bln["3"]["LONG"] = "Maret";
  $bln["3"]["SHORT"] = "Mar";   
  $bln["04"]["LONG"] = "April";
  $bln["04"]["SHORT"] = "Apr";
  $bln["4"]["LONG"] = "April";
  $bln["4"]["SHORT"] = "Apr";
  $bln["05"]["LONG"] = "Mei";
  $bln["05"]["SHORT"] = "Mei";
  $bln["5"]["LONG"] = "Mei";
  $bln["5"]["SHORT"] = "Mei";
  $bln["06"]["LONG"] = "Juni";
  $bln["06"]["SHORT"] = "Jun";
  $bln["6"]["LONG"] = "Juni";
  $bln["6"]["SHORT"] = "Jun";
  $bln["07"]["LONG"] = "Juli";
  $bln["07"]["SHORT"] = "Jul";
  $bln["7"]["LONG"] = "Juli";
  $bln["7"]["SHORT"] = "Jul";
  $bln["08"]["LONG"] = "Agustus";
  $bln["08"]["SHORT"] = "Ags";
  $bln["8"]["LONG"] = "Agustus";
  $bln["8"]["SHORT"] = "Ags";
  $bln["09"]["LONG"] = "September";
  $bln["09"]["SHORT"] = "Sep";
  $bln["9"]["LONG"] = "September";
  $bln["9"]["SHORT"] = "Sep";
  $bln["10"]["LONG"] = "Oktober";
  $bln["10"]["SHORT"] = "Okt";
  $bln["11"]["LONG"] = "November";
  $bln["11"]["SHORT"] = "Nov";
  $bln["12"]["LONG"] = "Desember";
  $bln["12"]["SHORT"] = "Des";

  return $bln[$b][$mode];
}

function tglInggris($tgl,$mode) {
  if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00" && $tgl != "-") {
    $t = explode("-",$tgl);
    $bln = array();
    $bln["01"]["LONG"] = "January";
    $bln["01"]["SHORT"] = "Jan";
    $bln["1"]["LONG"] = "January";
    $bln["1"]["SHORT"] = "Jan";
    $bln["02"]["LONG"] = "February";
    $bln["02"]["SHORT"] = "Feb";
    $bln["2"]["LONG"] = "February";
    $bln["2"]["SHORT"] = "Feb";
    $bln["03"]["LONG"] = "March";
    $bln["03"]["SHORT"] = "Mar";
    $bln["3"]["LONG"] = "March";
    $bln["3"]["SHORT"] = "Mar";   
    $bln["04"]["LONG"] = "April";
    $bln["04"]["SHORT"] = "Apr";
    $bln["4"]["LONG"] = "April";
    $bln["4"]["SHORT"] = "Apr";
    $bln["05"]["LONG"] = "May";
    $bln["05"]["SHORT"] = "May";
    $bln["5"]["LONG"] = "May";
    $bln["5"]["SHORT"] = "May";
    $bln["06"]["LONG"] = "June";
    $bln["06"]["SHORT"] = "Jun";
    $bln["6"]["LONG"] = "June";
    $bln["6"]["SHORT"] = "Jun";
    $bln["07"]["LONG"] = "July";
    $bln["07"]["SHORT"] = "Jul";
    $bln["7"]["LONG"] = "July";
    $bln["7"]["SHORT"] = "Jul";
    $bln["08"]["LONG"] = "August";
    $bln["08"]["SHORT"] = "Aug";
    $bln["8"]["LONG"] = "August";
    $bln["8"]["SHORT"] = "Aug";
    $bln["09"]["LONG"] = "September";
    $bln["09"]["SHORT"] = "Sep";
    $bln["9"]["LONG"] = "September";
    $bln["9"]["SHORT"] = "Sep";
    $bln["10"]["LONG"] = "October";
    $bln["10"]["SHORT"] = "Oct";
    $bln["11"]["LONG"] = "November";
    $bln["11"]["SHORT"] = "Nov";
    $bln["12"]["LONG"] = "December";
    $bln["12"]["SHORT"] = "Dec";
    
    $b = $t[1];
    
    if (strpos($t[2], ":") === false) { //tdk ada format waktu
      $jam = "";
    }
    else {
      $j = explode(" ",$t[2]);
      $t[2] = $j[0];
      $jam = $j[1];
    }
    
    return $t[2]."-".$bln[$b][$mode]."-".$t[0]." ".$jam;
  }
  else {
    return "-";
  }
}

function blnInggris($aBln,$mode) {
  $bln = array();
  $bln["01"]["LONG"] = "January";
  $bln["01"]["SHORT"] = "Jan";
  $bln["1"]["LONG"] = "January";
  $bln["1"]["SHORT"] = "Jan";
  $bln["02"]["LONG"] = "February";
  $bln["02"]["SHORT"] = "Feb";
  $bln["2"]["LONG"] = "February";
  $bln["2"]["SHORT"] = "Feb";
  $bln["03"]["LONG"] = "March";
  $bln["03"]["SHORT"] = "Mar";
  $bln["3"]["LONG"] = "March";
  $bln["3"]["SHORT"] = "Mar";   
  $bln["04"]["LONG"] = "April";
  $bln["04"]["SHORT"] = "Apr";
  $bln["4"]["LONG"] = "April";
  $bln["4"]["SHORT"] = "Apr";
  $bln["05"]["LONG"] = "May";
  $bln["05"]["SHORT"] = "May";
  $bln["5"]["LONG"] = "May";
  $bln["5"]["SHORT"] = "May";
  $bln["06"]["LONG"] = "June";
  $bln["06"]["SHORT"] = "Jun";
  $bln["6"]["LONG"] = "June";
  $bln["6"]["SHORT"] = "Jun";
  $bln["07"]["LONG"] = "July";
  $bln["07"]["SHORT"] = "Jul";
  $bln["7"]["LONG"] = "July";
  $bln["7"]["SHORT"] = "Jul";
  $bln["08"]["LONG"] = "August";
  $bln["08"]["SHORT"] = "Ags";
  $bln["8"]["LONG"] = "August";
  $bln["8"]["SHORT"] = "Ags";
  $bln["09"]["LONG"] = "September";
  $bln["09"]["SHORT"] = "Sep";
  $bln["9"]["LONG"] = "September";
  $bln["9"]["SHORT"] = "Sep";
  $bln["10"]["LONG"] = "October";
  $bln["10"]["SHORT"] = "Oct";
  $bln["11"]["LONG"] = "November";
  $bln["11"]["SHORT"] = "Nov";
  $bln["12"]["LONG"] = "December";
  $bln["12"]["SHORT"] = "Des";

  return $bln[$aBln][$mode];
}

function dayDifference($dateA, $dateB) {  
  $dateDiff = $dateA - $dateB;
  $fullDays = floor($dateDiff/(60*60*24));

  return $fullDays;
}

function dayDifference2($dateA,$dateB,$inDaysOnly) {
  $date1 = new DateTime($dateA);
  $date2 = new DateTime($dateB);
  $interval = $date1->diff($date2);

  if($inDaysOnly == TRUE) {
    // shows the total amount of days (not divided into years, months and days like above)
    $arrDiff["DAY"] = $interval->days;
  }
  else {
    $arrDiff["DAY"] = $interval->d;
    $arrDiff["MONTH"] = $interval->m;
    $arrDiff["YEAR"] = $interval->y;
    $arrDiff["HOUR"] = $interval->h;
    $arrDiff["MINUTE"] = $interval->i;
    $arrDiff["SECOND"] = $interval->s;
  }
  
  return $arrDiff;
}

function forceDownload( $filename = '', $data = '' ) {
  if( $filename == '' || $data == '' )  return false;
  if(!file_exists( $data ))             return false;
  if(false === strpos( $filename, '.')) return false;

  // Grab the file extension
  $ext = explode( '.', $filename );
  $extension = strtolower( end( $ext ) );

  // our list of mime types
  $mime_types = array(
    'txt' => 'text/plain',
    'htm' => 'text/html',
    'html' => 'text/html',
    'php' => 'text/html',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'json' => 'application/json',
    'xml' => 'application/xml',
    'swf' => 'application/x-shockwave-flash',
    'flv' => 'video/x-flv',

    // images
    'png' => 'image/png',
    'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'ico' => 'image/vnd.microsoft.icon',
    'tiff' => 'image/tiff',
    'tif' => 'image/tiff',
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',

    // archives
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    'exe' => 'application/x-msdownload',
    'msi' => 'application/x-msdownload',
    'cab' => 'application/vnd.ms-cab-compressed',

    // audio/video
    'mp3' => 'audio/mpeg',
    'qt' => 'video/quicktime',
    'mov' => 'video/quicktime',

    // adobe
    'pdf' => 'application/pdf',
    'psd' => 'image/vnd.adobe.photoshop',
    'ai' => 'application/postscript',
    'eps' => 'application/postscript',
    'ps' => 'application/postscript',

    // ms office
    'doc' => 'application/msword',
    'rtf' => 'application/rtf',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',

    // open office
    'odt' => 'application/vnd.oasis.opendocument.text',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
  );

  // Set a default mime if we can't find it
  if( !isset( $mime_types[$extension])) {
    $mime = 'application/octet-stream';
  }
  else {
    $mime = ( is_array( $mime_types[$extension] ) ) ? $mime_types[$extension][0] : $mime_types[$extension];
  }
        
  // Generate the server headers
  if( strstr( $_SERVER['HTTP_USER_AGENT'], "MSIE" ))  {
    header( 'Content-Type: "'.$mime.'"' );
    header( 'Content-Disposition: attachment; filename="'.$filename.'"' );
    header( 'Expires: 0' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( "Content-Transfer-Encoding: binary" );
    header( 'Pragma: public' );
    header( "Content-Length: ".filesize( $data ) );
  }
  else {
    header( "Pragma: public" );
    header( "Expires: 0" );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Cache-Control: private", false );
    header( "Content-Type: ".$mime, true, 200 );
    header( 'Content-Length: '.filesize( $data ) );
    header( 'Content-Disposition: attachment; filename='.$filename);
    header( "Content-Transfer-Encoding: binary" );
  }
  readfile( $data );
  exit;
}

/* backup the db OR just a table */
function backupTables($host,$user,$pass,$name,$tables = '*') {  
  $link = mysqli_connect($host,$user,$pass);
  mysqli_select_db($link,$name);
  $return = "";
  
  //get all of the tables
  if($tables == '*') {
    $tables = array();
    $result = mysqli_query($link,'SHOW TABLES');
    while($row = mysqli_fetch_row($result)) {
      $tables[] = $row[0];
    }
  }
  else {
    $tables = is_array($tables) ? $tables : explode(',',$tables);
  }
  
  //cycle through
  foreach($tables as $table)  {
    $result = mysqli_query($link,'SELECT * FROM '.$table);
    $num_fields = mysqli_num_fields($result);
    
    $return .= 'DROP TABLE IF EXISTS '.$table.';';
    $row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE '.$table));
    $return .= "\n\n".$row2[1].";\n\n";
    
    for ($i = 0; $i < $num_fields; $i++) {
      while($row = mysqli_fetch_row($result)) {
        $return.= 'INSERT INTO '.$table.' VALUES(';
        for($j=0; $j < $num_fields; $j++) {
          $row[$j] = addslashes($row[$j]);
          $row[$j] = @ereg_replace("\n","\\n",$row[$j]);
          if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
          if ($j < ($num_fields-1)) { $return.= ','; }
        }
        $return.= ");\n";
      }
    }
    $return.="\n\n\n";
  }
  
  //save file
  $fileName = 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
  $handle = fopen($fileName,'w+');
  fwrite($handle,$return);
  fclose($handle);

  return $fileName;
}

function resizeImage($fileName) {
  // the file
  $a = explode(".", $fileName);
  $fileNameOnly = "";
  for($i=0; $i<count($a)-1; $i++) {
    $fileNameOnly .= $a[$i];
  }
  $fileExt = strtolower(end($a));

  if(strtolower($fileExt) == "jpg" || strtolower($fileExt) == "jpeg" || strtolower($fileExt) == "png" || strtolower($fileExt) == "gif")  {
    // the desired width of the image
    $imgWidth = 128;

    // content type
    if(strtolower($fileExt) == "jpg" || strtolower($fileExt) == "jpeg") header('Content-Type: image/jpeg');
    if(strtolower($fileExt) == "png")                                   header('Content-Type: image/png');
    if(strtolower($fileExt) == "gif")                                   header('Content-Type: image/gif');

    list($width_orig, $height_orig) = getimagesize($fileName);

    $ratio_orig = $width_orig/$height_orig;
    $height = $imgWidth/$ratio_orig;

    // resample
    $image_p = imagecreatetruecolor($imgWidth, $height);    
    if(strtolower($fileExt) == "jpg" || strtolower($fileExt) == "jpeg") $image = imagecreatefromjpeg($fileName);
    if(strtolower($fileExt) == "png")                                   $image = imagecreatefrompng($fileName);
    if(strtolower($fileExt) == "gif")                                   $image = imagecreatefromgif($fileName);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $imgWidth, $height, $width_orig, $height_orig);

    // output
    $resizedFileName = $fileName;
    if(strtolower($fileExt) == "jpg" || strtolower($fileExt) == "jpeg") imagejpeg($image_p, $resizedFileName, 80);
    if(strtolower($fileExt) == "png")                                   imagepng($image_p, $resizedFileName, 4);  
    if(strtolower($fileExt) == "gif")                                   imagegif($image_p, $resizedFileName);      
  }
}

function randomDigits($length = 5){
  $digits = "";
  $numbers = range(0,9);
  shuffle($numbers);
  for($i = 0;$i < $length;$i++) {
    $digits .= $numbers[$i];
  }
  return $digits;
}

function randomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

function createCode($codeLength) {
  $kode = strtoupper(substr(md5(randomDigits($codeLength)), 0,($codeLength-1) ));
  
  return $kode;
}

function createSlug($string,$withCode=0){     
  $replace = '-';         
  $string = strtolower($string);     

  //replace / and . with white space     
  $string = preg_replace("/[\/\.]/", " ", $string);     
  $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);     

  //remove multiple dashes or whitespaces     
  $string = preg_replace("/[\s-]+/", " ", $string);     

  //convert whitespaces and underscore to $replace     
  $string = preg_replace("/[\s_]/", $replace, $string); 

  //$string = $string."-".substr(md5(date("Y-m-d H:i:s")),0,4);    

  //limit the slug size     
  $string = substr($string, 0, 100);     

  //slug is generated     
  if(isset($withCode)) {
    if(intval($withCode) > 0 && intval($withCode) <= 7)  {
      return $string."-".createCode($withCode);   
    }
    else {
      return $string;   
    }
  }
  else {
    return $string; 
  }  
}

function getSetting($setId)  {
  $setValue = "";
  $setting = DB::select("SELECT SET_VALUE FROM coll_settings WHERE SET_ID = ? LIMIT 0,1",array($setId));
  if(count($setting) > 0) {
    $rs_setting = $setting[0];
    $setValue = $rs_setting->{"SET_VALUE"};
  }

  return $setValue;
}

function mapExcelColumn($idx) {
  if($idx == 0) $colName = "A";
  if($idx == 1) $colName = "B";
  if($idx == 2) $colName = "C";
  if($idx == 3) $colName = "D";
  if($idx == 4) $colName = "E";
  if($idx == 5) $colName = "F";
  if($idx == 6) $colName = "G";
  if($idx == 7) $colName = "H";
  if($idx == 8) $colName = "I";
  if($idx == 9) $colName = "J";
  if($idx == 10) $colName = "K";
  if($idx == 11) $colName = "L";
  if($idx == 12) $colName = "M";
  if($idx == 13) $colName = "N";
  if($idx == 14) $colName = "O";
  if($idx == 15) $colName = "P";
  if($idx == 16) $colName = "Q";
  if($idx == 17) $colName = "R";
  if($idx == 18) $colName = "S";
  if($idx == 19) $colName = "T";
  if($idx == 20) $colName = "U";
  if($idx == 21) $colName = "V";
  if($idx == 22) $colName = "W";
  if($idx == 23) $colName = "X";
  if($idx == 24) $colName = "Y";
  if($idx == 25) $colName = "Z";
  
  return $colName;    
}

function getReferenceInfo($refKtgId, $refValueId) {
  $ref = DB::table("coll_referensi")
    ->where("R_KATEGORI",$refKtgId)
    ->where("R_ID",$refValueId)
    ->first();

  if(count($ref) > 0) {
    return $ref->{"R_INFO"};
  }
  else {
    return "";
  }
}

function formatPonsel($ponsel,$prefix) {
  if(trim($ponsel) != "" && trim($ponsel) != "-") {
    if(substr($ponsel,0,5) == "+6262" || substr($ponsel,0,4) == "+620" || substr($ponsel,0,4) == "6262" || substr($ponsel,0,3) == "620") {
      //+626281xxxx
      if(substr($ponsel,0,5) == "+6262")  {
        if($prefix == "+62") { 
          $ponsel = "+62".substr($ponsel,5);  
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,5);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,5);  
        }   
      }

      //+62081xxxx    
      if(substr($ponsel,0,4) == "+620") {
        if($prefix == "+62") {
          $ponsel = "+62".substr($ponsel,4);  
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,4);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,4);  
        }
      }

      //626281xxxx
      if(substr($ponsel,0,4) == "6262") {
        if($prefix == "+62") { 
          $ponsel = "+62".substr($ponsel,4);  
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,4);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,4);  
        }   
      }

      //62081xxxx   
      if(substr($ponsel,0,3) == "620")  {
        if($prefix == "+62") { //no change
          $ponsel = "+62".substr($ponsel,3);  
        }
        if($prefix === "0") {
          $ponsel = "0".substr($ponsel,3);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,3);  
        }   
      }
    }
    else {
      //+6281xxxxx
      if(substr($ponsel,0,3) == "+62")  {
        if($prefix == "+62") { //no change

        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,3);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,3);  
        }   
      }

      //628xxxxx
      if(substr($ponsel,0,2) == "62") {
        if($prefix == "+62") {
          $ponsel = "+".$ponsel;
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,2);
        }
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,2);  
        }   
      }

      //8132333
      if(substr($ponsel,0,1) == "8")  {
        if($prefix == "+62") {
          $ponsel = "+62".$ponsel;
        }
        if($prefix == "0") {
          $ponsel = "0".$ponsel;
        }   
        if(trim($prefix) == "") { //no change
          
        }   
      }

      //081xxxxx
      if(substr($ponsel,0,2) == "08") {
        if($prefix == "+62") {
          $ponsel = "+62".substr($ponsel,1);
        }
        if($prefix == "0") { //no change
          
        }
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,1);  
        }   
      }
    }
  }

  return $ponsel;
}

function isInRole($role, $arrRoles) {
  foreach ($arrRoles as $aData) {
    if($aData->{"GR_ID"} === $role) return true;
  }

  return false;
}

function isLoginValid($userId, $loginToken) {
  $userData = DB::table("coll_user")
    ->where("U_ID", $userId)
    ->where("U_LOGIN_TOKEN", $loginToken)
    ->first();

  if(count($userData) <= 0) return false;

  $arrDiff = array();
  $arrDiff = dayDifference2(date("Y-m-d H:i:s"),$userData->{"U_LOGIN_WAKTU"},TRUE);

  if($arrDiff["DAY"] > intval(getSetting("LOGIN_EXPIRED_DAYS"))) return false;
    
  return true;
}

function validateReferenceEntry($refCategory, $entryValue) {
  $cek = DB::select("SELECT * FROM coll_referensi WHERE R_KATEGORI = ? AND R_ID = ?", array($refCategory, $entryValue));
  if(isset($cek) && count($cek) > 0) {
    return true;
  }
  else {
    return false;
  }
}

function getCenterFromDegrees($data) {
    /**
   * Get a center latitude,longitude from an array of like geopoints
   *
   * @param array data 2 dimensional array of latitudes and longitudes
   * For Example:
   * $data = array
   * (
   *   0 = > array(45.849382, 76.322333),
   *   1 = > array(45.843543, 75.324143),
   *   2 = > array(45.765744, 76.543223),
   *   3 = > array(45.784234, 74.542335)
   * );
  */
  
  if (!is_array($data)) return FALSE;

  $num_coords = count($data);

  $X = 0.0;
  $Y = 0.0;
  $Z = 0.0;

  foreach ($data as $coord) {
    $lat = $coord[0] * pi() / 180;
    $lon = $coord[1] * pi() / 180;

    $a = cos($lat) * cos($lon);
    $b = cos($lat) * sin($lon);
    $c = sin($lat);

    $X += $a;
    $Y += $b;
    $Z += $c;
  }

  $X /= $num_coords;
  $Y /= $num_coords;
  $Z /= $num_coords;

  $lon = atan2($Y, $X);
  $hyp = sqrt($X * $X + $Y * $Y);
  $lat = atan2($Z, $hyp);

  return array($lat * 180 / pi(), $lon * 180 / pi());
}

function parseToXML($htmlStr) { 
  $xmlStr = str_replace('<','&lt;',$htmlStr); 
  $xmlStr = str_replace('>','&gt;',$xmlStr); 
  $xmlStr = str_replace('"','&quot;',$xmlStr); 
  $xmlStr = str_replace("'",'&#39;',$xmlStr); 
  $xmlStr = str_replace("&",'&amp;',$xmlStr); 
  return $xmlStr; 
} 

// function to geocode address, it will return false if unable to geocode address
function geocode($address){ 
  // url encode the address
  $address = urlencode($address);
   
  // google map geocode api url
  $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

  // get the json response
  $resp_json = file_get_contents($url);   
  if(isset($resp_json) && trim($resp_json) != "") {
    // decode the json
    $resp = json_decode($resp_json, true);

    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
      // get the important data
      $lati = $resp['results'][0]['geometry']['location']['lat'];
      $longi = $resp['results'][0]['geometry']['location']['lng'];
      $formatted_address = $resp['results'][0]['formatted_address'];
       
      // verify if data is complete
      if($lati && $longi && $formatted_address){     
        // put the data in the array
        $data_arr = array();                     
        array_push(
          $data_arr, 
          $lati, 
          $longi, 
          $formatted_address
        );
           
        return $data_arr;         
      }
      else {
        return false;
      }       
    }
    else {
      return false;
    }
  }
  else {
    return false;
  }
}

function distance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } 
  else if ($unit == "N") {
    return ($miles * 0.8684);
  } 
  else {
    return $miles;
  }
}

function getAddress($lat,$lng) {
  $fullAddress = $lat.",".$lng;
  $address = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=true");
  if(isset($address)) {
    $jsonData = json_decode($address);
    $fullAddress = $jsonData->results[0]->formatted_address;
  }

  return $fullAddress;
}

function getLastDate($bln,$thn) {
  if($bln >= 1 && $bln <= 12 && $thn >= 1901) {
    return date("Y-m-t", mktime(0, 0, 0, $bln, 1, $thn));
  }
}

function addDaysWithDate($date,$days,$formatDate = 'Y-m-d'){
  $date = strtotime("+".$days." days", strtotime($date));
  
  return date($formatDate, $date);
}

function oprDaysWithDate($date,$daysWithOperator,$formatDate = 'Y-m-d'){
  $date = strtotime($daysWithOperator." days", strtotime($date));
  
  return date($formatDate, $date);
}

function addMonthsWithDate($date,$months,$formatDate = 'Y-m-d') {
  $date = strtotime("+".$months." months", strtotime($date));
  
  return date($formatDate, $date);  
}

function oprMonthsWithDate($date,$monthsWithOperator,$formatDate = 'Y-m-d'){
  $date = strtotime($monthsWithOperator." months", strtotime($date));
  
  return date($formatDate, $date);
}

function addYearsWithDate($date,$years,$formatDate = 'Y-m-d') {
  $date = strtotime("+".$years." years", strtotime($date));

  return date($formatDate, $date);  
}

function validateDate($date){
  if($date != "" && $date != "-" && $date != "0000-00-00") {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
  }
  else {
    return false;
  }
}
?>