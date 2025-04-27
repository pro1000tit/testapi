<?php

$process = curl_init("https://b2bapi.onliner.by/pricelists/1067cc125029b0a0b37e/report?access_token=45b31b8c87a7b42d787338c6b1ed05734e39f223");
curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));

curl_setopt($process, CURLOPT_POST, 0);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($process, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
$result = curl_exec($process);
curl_close($process);

$str = json_decode($result,true);

print_r($str);

