<?php
/**
 *
 *
 * @author: xiaoshenge
 * Date: 2017/4/5 17:08
 *
 */

function exportArrToFile($arr, $filePath)
{
	$str = var_export($arr, true);

	return file_put_contents("$filePath", "<?php\nreturn $str;");
}
