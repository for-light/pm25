<?php
/**
 * Created by PhpStorm.
 * User: 鲁天松
 * 同步api的pm25以及aqi数据到数据库（每小时执行）
 * Date: 18-1-15
 * Time: 下午4:04
 */

$dsn = "mysql:host=localhost;dbname=pm25";
$db = new PDO($dsn, 'root', 'root');
$db->query('SET NAMES utf8');

$host = "https://ali-pm25.showapi.com";
$path = "/pm25-top";
$method = "GET";
$appcode = "50877461394c49ea89733c51498af352";
$headers = array();
array_push($headers, "Authorization:APPCODE " . $appcode);
$url = $host . $path;

$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_FAILONERROR, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
if (1 == strpos("$".$host, "https://"))
{
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
}
$content = json_decode(curl_exec($curl), true);
$data = array();
foreach ($content['showapi_res_body']['list']  as $row) {
    $data = array(
        'day' => date('Y-m-d'),
        'hour' => date('H'),
        'area' => $row['area'],
        'pm25' => $row['pm2_5'],
        'aqi' => $row['aqi'],
        'created' => date('Y-m-d H:i:s')
    );
    $sql = "insert into data(day, hour, area, pm25, aqi, created)values('{$data['day']}', {$data['hour']}, '{$data['area']}', {$data['pm25']}, {$data['aqi']}, '{$data['created']}')";
    $count = $db->exec($sql) or die(print_r($db->errorInfo(), true));
    echo $sql . $count;
}