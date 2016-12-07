<?
function str2html($str) {
	global $img_host;
	$str = str_replace("&", "&amp;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	$str = preg_replace("/\\\\r[\\[\\d;]+m/", "", $str);
	$str = ereg_replace("\\x20\\x20", "&nbsp;", $str);
	$lines = explode("\\n", $str);
	$ret = '';
	$flag = true;
	for ($i = 0; $i < count($lines); ++$i) {
		$line = $lines[$i];
		if (strstr($line, ': 标 &nbsp;题') !== false) continue;
		if (strstr($line, ': 发信站') !== false) continue;
		if (strstr($line, ': 发信人') !== false) continue;
		if (strstr($line, ': 【 以下文字转载自') !== false) continue;
		if (strstr($line, ": ..........") !== false) continue;
		if (trim($line) == '') continue;
		if (strpos($line, ': ') === 0) {
			if ($flag) $line = "<font color=\"gray\">$line</font>";
			else continue;
			$flag = false;
		}
		$line = stripslashes($line);
		if ($i != 0) $ret .= '<br>';
		$ret .= $line;
	}
	$ret = preg_replace('/\[em([a-z]*)(\d+)\]/', "<img src=\"$img_host/image/em\${1}/\${2}.gif\">", $ret);
	return $ret;
}
?>
