<?
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: /404');
	exit();
}
require_once('init.php');
require_once('i18n.php');
require_once('telnet.php');
$kw = trim(urlencode($_POST['kw']));
if ($kw == '') {
	$html = show_error(i18n('empty_kw'));
}
else {
	error_log("emule $kw");
	$html = file_get_contents("http://mldonkey.ucptt.com/submit?custom=Complex+Search&keywords=$kw&minsize=&minsize_unit=1048576&maxsize=&maxsize_unit=1048576&media=&media_propose=&format=&format_propose=&artist=&album=&title=&bitrate=&network=");
	preg_match('/Query (\d+) sent to/', $html, $matches);
	$search_id = $matches[1];
	sleep(2);
	$html = file_get_contents("http://mldonkey.ucptt.com/submit?q=vr+$search_id");
	$result_num = preg_match_all('/<td class="sr"><a href="ed2k:\/\/\|file\|([\d\D]+?)\|(\d+)\|(\w+)\|\/">Donkey<\/a><\/td><td [\d\D]+?<\/td><td class="sr ar">[\w\.]+<\/td>\s*<td class="sr ar">(\d*)<\/td>\s*<td class="sr ar">(\d*)<\/td>/', $html, $matches, PREG_SET_ORDER);
	foreach ($matches as $match) {
		if ($match[2] >= 512 * 1024 * 1024) $seeds[] = $match;
	}

	//print_r($seeds);
	if (!isset($seeds) || count($seeds) == 0) {
		$html = show_error(i18n('no_search_result'));
	}
	else {
		usort($seeds, 'sort_seeds');
		$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-10">';
		$html .= '<div class="panel panel-info"><table class="table table-striped">';
		$html .= '<tr><th>'.i18n('seed_name').'</th><th>'.i18n('seed_size').'</th><th>'.i18n('seed_available_sources').'</th><th>'.i18n('seed_completed_sources').'</th><th>'.i18n('seed_download_emule').'</th>';
		foreach ($seeds as $seed) {
			list($padding, $file_name, $file_size, $file_hash, $available_sources, $completed_sources) = $seed;
			if ($available_sources == '') $available_sources = 0;
			if ($completed_sources == '') $completed_sources = 0;
			$html .= "<tr><td>".urldecode($file_name)."</td><td>".human_filesize($file_size)."</td><td>$available_sources</td><td>$completed_sources</td><td><a href=\"ed2k://|file|$file_name|$file_size|$file_hash|/\">".i18n('seed_download_emule')."</a></td></tr>";
		}
		$html .= '</table></div>';
		$html .= '</div></div>';
	}
}
require_once('header.php');
echo $html;
require_once('footer.php');
function sort_seeds($seed1, $seed2) {
	if (!isset($seed1[4])) $seed1[4] = 0;
	if (!isset($seed2[4])) $seed2[4] = 0;
	return $seed2[4] - $seed1[4];
}
?>
