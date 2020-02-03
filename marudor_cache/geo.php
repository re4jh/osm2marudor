<?php
$iTtl = 31622400; //1 year
$fLat = floatval($_GET['lat']);
$fLng = floatval($_GET['lng']);

$ch = curl_init('https://marudor.de/api/station/v1/geoSearch?lat=' . $fLat . '&lng=' . $fLng);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
$iHttpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($iHttpStatus == 200) {
  $exp_gmt = gmdate("D, d M Y H:i:s", time() + $iTtl) . " GMT";
  $mod_gmt = gmdate("D, d M Y H:i:s", filemtime($sCachePath)) . " GMT";
  header("Expires: " . $exp_gmt);
  header("Last-Modified: " . $mod_gmt);
  header("Cache-Control: public, max-age=" . $iTtl * 60);
  // for MSIE 5
  header("Cache-Control: pre-check=" . $iTtl * 60, false);
  header('Content-Type: application/json; charset=utf-8');
  echo $result;;

} else {
    http_response_code($iHttpStatus);
    die("Error on Marudor-Download");
}
