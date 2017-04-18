<?php
/**
 * Created by PhpStorm.
 * User: xiaoshenge
 * Date: 2017/4/2
 * Time: 上午7:58050015
 */
define( 'ROOT_DIR', dirname(__FILE__) );

include ROOT_DIR . "/curl/curl.php";
include ROOT_DIR . "/commonFun.php";

$curl = new Curl;
$str = "";

$res = $curl->get("http://sqt.gtimg.cn/utf8/q=sh000001,sz399001,sz399006,sz399005,sh000300&offset=2,33");
if ($res->body)
{
	preg_match_all('/"(.*)";/', $res->body, $match);
	foreach ($match[1] as $v)
	{
		$tmp = explode("~", $v);
		$str .= sprintf("%s:%+.2F\t", $tmp[0], $tmp[1]);
	}
	$str .= "\n\n";
}


$funds = include ROOT_DIR . "/fundPool.php";


$txGzUrl = "http://web.ifzq.gtimg.cn/fund/newfund/fundSsgz/getSsgz?app=web&symbol=jj";
$ttGzUrl = "http://fundgz.1234567.com.cn/js/%s.js";

$gzVal = include ROOT_DIR ."/guzhi.php";

//$str = sprintf("%s\t%s %s\t%s  %s  %s  %s\n", mb_substr("基金", 0, 4, "utf-8"), "日期", "时间", "腾讯估值", "腾讯涨幅", "天天估值", "天天涨幅");
//$str = "";
foreach ($funds as $k => $v)
{
	//腾讯估值
	$res = json_decode($curl->get($txGzUrl.$k)->body);

	if ($res->data)
	{
		$date = $res->data->date;
		list($txNewTime, $txNewVal) = array_pop($res->data->data);
		$yesterdayVal = $res->data->yesterdayDwjz;

		$txRate = ($txNewVal * 10000 - $yesterdayVal*10000) / ($yesterdayVal * 10000) * 100;
	}
	else
	{
		$date = date("Y-m-d");
		$txNewVal = '--';
		$txRate = '--';
		$txNewTime = date("Hi");
	}



	//天天基金估值
	$res = $curl->get(sprintf($ttGzUrl, $k));
	preg_match_all('/jsonpgz\((.*)\)/', $res->body, $match);
	if ($match[1][0])
	{
		$ttData = json_decode($match[1][0],true);
		$ttNewVal = $ttData['gsz'];
		$ttNewRate = floatval($ttData['gszzl']);
		$ttNewTime = date("H:i", strtotime($ttData['gztime']));


	}
	else
	{
		$ttNewVal = '--';
		$ttNewRate = '--';
		$ttNewTime = '';
	}

	$txNewTime = substr($txNewTime, 0, 2) . ':' . substr($txNewTime, 2, 4);
	$date = substr($date, 5);


	$str .= sprintf("%s\t%s %s   腾 %.4f (%+.2F)  天 %.4f (%+0.2F)\n", mb_substr($v, 0, 11, "utf-8"), $date, $txNewTime, $txNewVal, $txRate, $ttNewVal, $ttNewRate);



	if ($txNewTime == "1500")
	{
		$gzVal[$k][$date]['tx'] = number_format($txNewVal, 4);
	}

	if ($ttNewTime == "15:00")
	{
		$gzVal[$k][$date]['tt'] = number_format($ttNewVal, 4);
	}
}


exportArrToFile($gzVal, ROOT_DIR ."/guzhi.php");

file_put_contents(ROOT_DIR ."/output", $str);




