<?php
/*
 * 解析20140513到现在的空气质量数据，格式为csv
 *
 */
exit;
$dsn = "mysql:host=localhost;dbname=pm25";
$db = new PDO($dsn, 'root', 'root');
$db->query('SET NAMES utf8');

$filenames = scandir('../../csv');
for ($i = 2; $i < count($filenames); $i++) {
    $data_list = [];
    $file = fopen('../../csv/' . $filenames[$i], 'r');
    while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
        $data_list[] = $data;
    }
    $day = $data_list[1][0];//日期
    $r_count = count($data_list);//数据总共有多少行
    $c_count = count($data_list[0]);//数据总共有多少列
    $hours = array_column($data_list, 1);//小时列,防止数据混乱
    for ($j = 3; $j < $c_count; $j++) {//从第4列开始
        $sql = '';
        $column = array_column($data_list, $j);
        $area = $column[0];
        for ($k = 1; $k < $r_count; $k += 15) {
            //save data
        }
    }
}
fclose($file);


