<?php
/**
 * Created by PhpStorm.
 * User: xiaoshenge
 * Date: 2016/10/6
 * Time: 下午4:18
 */
include "curl/curl.php";

$curl = new Curl;

$vars = [
	"account" => "",
	"captcha" => '',
	"password" => "",
];


$curl->headers['Referer'] = "https://www.caifupai.com/user/login";
$curl->cookie_file = "ygb_cookie";


$curl->options['CURLOPT_POSTFIELDS'] = json_encode($vars);
$res = $curl->post("https://h.caifupai.com/api/user_signin");

$curl->headers['Origin'] = 'https://h.caifupai.com';
//
$resp = $curl->post("https://h.caifupai.com/actapi_v2/mapi/checkIn");


