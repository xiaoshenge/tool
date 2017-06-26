<?php
/**
 * Created by PhpStorm.
 * User: xiaoshenge
 * Date: 2017/4/2
 * Time: 上午7:58050015
 */

//$a = "a中问";
//
//echo mb_strlen($a);
//die;


define( 'ROOT_DIR', dirname(__FILE__) );

include ROOT_DIR . "/curl/curl.php";
include ROOT_DIR . "/commonFun.php";

require __DIR__.'/vendor/autoload.php';
use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
$ansi = new Ansi(new StreamWriter('php://stdout'));

$n = 0;
while (true) {

	$curl = new Curl;
	$str = "";

	$res = $curl->get("http://sqt.gtimg.cn/utf8/q=sh000001,sz399001,sz399006,sz399005,sh000300&offset=2,33");
	if ($res->body) {
		preg_match_all('/"(.*)";/', $res->body, $match);
		$outputStr = '';
		foreach ($match[1] as $v) {
			$tmp = explode("~", $v);
			$str .= sprintf("%s:%+.2F\t", $tmp[0], $tmp[1]);

			$ansi->color([SGR::COLOR_FG_WHITE])->text($tmp[0] . ": ");

			if ($tmp[1] > 0) {
				$color = [SGR::COLOR_FG_RED];
			} else {
				$color = [SGR::COLOR_FG_GREEN];
			}

			$ansi->color($color)
				//->blink()
				->text(sprintf("%+.2F", $tmp[1]))->nostyle()->text("\t");

		}
		$str .= "\n\n";

		$ansi->lf()->lf();

	}


	$funds = include ROOT_DIR . "/fundPool.php";
	$txGzUrl = "http://web.ifzq.gtimg.cn/fund/newfund/fundSsgz/getSsgz?app=web&symbol=jj";
	$ttGzUrl = "http://fundgz.1234567.com.cn/js/%s.js";
	$gzVal = include ROOT_DIR . "/guzhi.php";
//$str = sprintf("%s\t%s %s\t%s  %s  %s  %s\n", mb_substr("基金", 0, 4, "utf-8"), "日期", "时间", "腾讯估值", "腾讯涨幅", "天天估值", "天天涨幅");
//$str = "";
	$i = 0;
	foreach ($funds as $k => $v) {
		if ($i % 2 == 0) {
			$bg = SGR::COLOR_BG_WHITE_BRIGHT;
		} else {
			$bg = SGR::COLOR_BG_CYAN_BRIGHT;
		}
		//腾讯估值
		$res = json_decode($curl->get($txGzUrl . $k)->body);
		if ($res->data) {
			$date = $res->data->date;
			list($txNewTime, $txNewVal) = array_pop($res->data->data);
			$yesterdayVal = $res->data->yesterdayDwjz;
			$txRate = ($txNewVal * 10000 - $yesterdayVal * 10000) / ($yesterdayVal * 10000) * 100;
		} else {
			$date = date("Y-m-d");
			$txNewVal = '--';
			$txRate = '--';
			$txNewTime = date("Hi");
		}
		//天天基金估值
		$res = $curl->get(sprintf($ttGzUrl, $k));
		preg_match_all('/jsonpgz\((.*)\)/', $res->body, $match);
		if ($match[1][0]) {
			$ttData = json_decode($match[1][0], true);
			$ttNewVal = $ttData['gsz'];
			$ttNewRate = floatval($ttData['gszzl']);
			$ttNewTime = date("H:i", strtotime($ttData['gztime']));
		} else {
			$ttNewVal = '--';
			$ttNewRate = '--';
			$ttNewTime = '';
		}
		$txNewTime = substr($txNewTime, 0, 2) . ':' . substr($txNewTime, 2, 4);
		$date = substr($date, 5);
		$str .= sprintf("%s\t%s %s   腾 %.4f (%+.2F)  天 %.4f (%+0.2F)\n", mb_substr($v, 0, 11, "utf-8"), $date, $txNewTime, $txNewVal, $txRate, $ttNewVal, $ttNewRate);

		$ansi->color([SGR::COLOR_FG_BLACK, $bg])->text($v)->text("\t")->text($date . " " . $txNewTime)->text("\t")->nostyle();
		if ($txRate > 0) {
			$color = [SGR::COLOR_FG_RED, $bg];
		} else {
			$color = [SGR::COLOR_FG_GREEN, $bg];
		}
		$ansi->color($color)->text(sprintf('腾 %.4f (%+.2F)', $txNewVal, $txRate))->text("\t")->nostyle();

		if ($txRate > 0) {
			$color = [SGR::COLOR_FG_RED, $bg];
		} else {
			$color = [SGR::COLOR_FG_GREEN, $bg];
		}
		$ansi->color($color)->text(sprintf('天 %.4f (%+0.2F)', $ttNewVal, $ttNewRate))->text("\t")->nostyle();

		$ansi->lf();

		if ($txNewTime == "1500") {
			$gzVal[$k][$date]['tx'] = number_format($txNewVal, 4);
		}
		if ($ttNewTime == "15:00") {
			$gzVal[$k][$date]['tt'] = number_format($ttNewVal, 4);
		}

		$i++;
	}


	exportArrToFile($gzVal, ROOT_DIR . "/guzhi.php");
	file_put_contents(ROOT_DIR . "/output", $str);


	echo "已经运行 :      ";  // 5 characters of padding at the end
	echo "\033[5D";      // Move 5 characters backward
	echo str_pad($n * 60  . '秒', 3, ' ', STR_PAD_LEFT) . " %";    // Output is always 5 characters long

	sleep(60);
	system("clear");
	$n++;

}
