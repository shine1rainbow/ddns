<?php

require_once dirname(__FILE__).'/DDNS.php';

$accessKeyId = "YourAliyunAccessKeyId";
$accessKeySecret = 'YourAliyunAccessKeySecret';
$myDomainName = 'YourDomainName';

/**
 * Get localhost IP
 *
 * @param  void
 * @return array $data
 */
function getLocalIp()
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://[2001:470:1:18::223:250]');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	$data = curl_exec($curl);
	curl_close($curl);
	return json_decode($data, true);
}

$ddns = new DDns($accessKeyId, $accessKeySecret);

$params = [
    'DomainName' => $myDomainName,
    'Prefix' => 'ddns',
    'Type' => 'AAAA',
];

$res = $ddns->getRecord($params);

if (!is_null($res)) {
   $recordId = $res['RecordId']; 
} else {
    echo "not found";die;    
}

$resInfo = getLocalIp();
$localIp = $resInfo['ip'];

$query = [
    'RecordId' => $recordId,
    'RR' => 'ddns',
    'Type' => 'AAAA',
    'Value' => $localIp,
];

$res = $ddns->updateRecord($query);

var_dump($res);
