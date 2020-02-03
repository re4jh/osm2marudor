<?php

// TTL: 1 Week
$iTtl = 604800;

$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);

$sCachePath = "cached/${z}_${x}_$y.png";

if (!file_exists($sCachePath) || time()-filemtime($filename) >= $iTtl) {

    $sApiKey = file_get_contents("../json_data/api_key_thunderforest.json");
    $aApiKey = json_decode($sApiKey, true);

    $sUrlBase = 'https://tile.thunderforest.com/transport/';
    $sUrlBase .= "/".$z."/".$x."/".$y.".png?apikey=" . $aApiKey['apikey'];

    $ch = curl_init($sUrlBase);
    $fp = fopen($sCachePath, "w");
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    $iHttpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($iHttpStatus == 200) {
        fflush($fp);
    }
    fclose($fp);

    if ($iHttpStatus != 200) {
        http_response_code($iHttpStatus);
        unlink($sCachePath);
        die("Error on Marudor-Download");
    }
}

$exp_gmt = gmdate("D, d M Y H:i:s", time() + $iTtl) . " GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", filemtime($sCachePath)) . " GMT";
header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: public, max-age=" . $iTtl * 60);
// for MSIE 5
header("Cache-Control: pre-check=" . $iTtl * 60, false);
header('Content-Type: image/png; charset=utf-8');
readfile($sCachePath);
