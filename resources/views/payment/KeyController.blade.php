<?php
echo "KeyController";

$data['type'] = "card";
$curl = curl_init('https://sandbox.api.pagseguro.com/public-keys/');
curl_setopt($curl,CURLOPT_HTTPHEADER,Array(
    'Content-Typer: application/json',
    'Authorization: {{token}}'
));
curl_setopt($curl,CURLOPT_POST,true);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));

$retorno = curl_exec($curl);
curl_close($curl);
echo json_decode($retorno)->public_key;

?>