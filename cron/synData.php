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
$year = date('Y');
$month = date('m');
$day = date('d');
$hour = date('H');
//$day = '2018-01-16';
//$hour = '16';
$sql = "select id from data where year = '{$year}' and month = '{$month}' and day = '{$day}' and hour = '{$hour}' limit 1";
$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
if (!$result) {
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
    $sql = '';
//var_dump($content);exit();
    foreach ($content['showapi_res_body']['list'] as $row) {
        $data = array(
            'area' => $row['area'],
            'pm25' => is_numeric($row['pm2_5']) ? $row['pm2_5'] : 0,
            'aqi' => is_numeric($row['aqi']) ? $row['aqi'] : 0,
        );
        $sql .= "insert into data(year, month, day, hour, area, pm25, aqi, created)values('{$year}', {$month}, {$day}, {$hour}, '{$data['area']}', {$data['pm25']}, {$data['aqi']}, now());";
    }
    $db->exec($sql) or die(var_dump($db->errorInfo()));
    //$db->exec($sql) or die(print_r(date('Y-m-d H:i:s') . $db->errorInfo() . 'error' . PHP_EOL));
    echo date('Y-m-d H:i:s') . 'complete!' . PHP_EOL;
}

