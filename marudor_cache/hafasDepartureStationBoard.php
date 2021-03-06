<?php

$iTtl = 60;
$iStation = intval($_GET['station']);
$sCachePath = 'cached/hdsb_' . $iStation . '.json';

if (!file_exists($sCachePath) || time()-filemtime($sCachePath) >= $iTtl) {
    $ch = curl_init('https://marudor.de/api/hafas/v1/departureStationBoard?station=' . $iStation);
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
header("Cache-Control: public, max-age=" . $iTtl);
// for MSIE 5
header("Cache-Control: pre-check=" . $iTtl, false);
header('Content-Type: application/json; charset=utf-8');
readfile($sCachePath);
