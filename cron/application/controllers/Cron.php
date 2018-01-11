<?php
/**
 * Created by PhpStorm.
 * User: syw
 * Date: 18-1-11
 * Time: 下午8:25
 */
class Cron extends CI_Controller
{
    private $DB;

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            die();
        }

        $this->DB = $this->load->database('pm25', true);
    }

    /*
     * 同步全部地区到数据表
     * 执行一次(已执行)
     */
    public function synAreas()
    {
        exit;
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
                'name' => $row['area'],
                'pinyin' => $row['area_code'],
                'created' => date('Y-m-d H:i:s')
            );
            $this->DB->insert('areas', $data);
        }
    }

    /*
     * 每小时执行，同步api上的pm25排行数据到数据库
     */
    public function synData()
    {

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
            $this->DB->insert('data', $data);
        }
    }
}