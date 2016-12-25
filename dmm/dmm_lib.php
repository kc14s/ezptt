<?
function get_cover_img_url($sn, $channel, $rating = 2, $snn = '') {
	global $dmm_static_host;
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		return "${dmm_static_host}digital/video/$sn/${sn}ps.jpg";
	break;
	case 2:
		return "${dmm_static_host}digital/amateur/$sn/${sn}jp.jpg";
	break;
	case 5:
	case 6:
	case 7:
		$dvd = $channel == 7 ? 2 : 1;
		$ave_img_folder = ($rating == 1 ? 'new' : 'archive');
		global $ave_static_host;
		return "$ave_static_host$ave_img_folder/jacket_images/dvd$dvd$sn.jpg";
//		return "http://imgs.aventertainments.com/$ave_img_folder/jacket_images/dvd$dvd$sn.jpg";
	case 8:
		global $mgs_static_host;
		if (isset($GLOBALS['company_en_'.$sn])) {
			$company_en = $GLOBALS['company_en_'.$sn];
		}
		else {
			$company_en = execute_scalar("select company_en from video, mgs_company where sn = '$sn' and video.company = mgs_company.company");
			$GLOBALS['company_en_'.$sn] = $company_en;
		}
		preg_match_all('/(.*[A-Za-z]+)\-*(\d+)$/', $sn, $matches, PREG_SET_ORDER);
		return $mgs_static_host.'images/'.$company_en.'/'.$matches[0][1].'/'.$matches[0][2].'/pf_o1_'.$sn.'.jpg';
	case 9:
		global $tkh_static_host;
		return "${tkh_static_host}media/$sn/package/_v.jpg";
	case 10:
		global $pondo1_static_host;
		return "${pondo1_static_host}assets/sample/$sn/thum_b.jpg";
		return "http://www.1pondo.tv/assets/sample/$sn/thum_b.jpg";
	case 11:
		$video_id = substr($sn, 2);
		//return "http://www.5xww3.com/media/videos/tmb1/$video_id/default.jpg";
		return "http://www.5xww3.com/media/videos/tmb/$video_id/1.jpg";
	}
}

function get_cover_img_large_url($sn, $channel, $rating = 2) {
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		global $dmm_static_host;
		return "${dmm_static_host}/digital/video/$sn/${sn}pl.jpg";
	case 5:
	case 6:
	case 7:
		$dvd = $channel == 7 ? 2 : 1;
		$ave_img_folder = ($rating == 1 ? 'new' : 'archive');
		global $ave_static_host;
		return "$ave_static_host$ave_img_folder/bigcover/dvd$dvd$sn.jpg";
		//return "http://imgs.aventertainments.com/$ave_img_folder/bigcover/dvd$dvd$sn.jpg";
	case 8:
		global $mgs_static_host;
		if (isset($GLOBALS['company_en_'.$sn])) {
			$company_en = $GLOBALS['company_en_'.$sn];
		}
		else {
			$company_en = execute_scalar("select company_en from video, mgs_company where sn = '$sn' and video.company = mgs_company.company");
			$GLOBALS['company_en_'.$sn] = $company_en;
		}
		preg_match_all('/(.*[A-Za-z]+)\-*(\d+)$/', $sn, $matches, PREG_SET_ORDER);
		return $mgs_static_host.'images/'.$company_en.'/'.$matches[0][1].'/'.$matches[0][2].'/pb_e_'.$sn.'.jpg';
	case 10:
		global $pondo1_static_host;
		return "$pondo1_static_host/assets/sample/$sn/str.jpg";
		return "http://www.1pondo.tv/assets/sample/$sn/str.jpg";
	}
	return '';
}

function get_sample_img_thumb_url($sn, $channel, $id, $rating) {
	global $dmm_static_host;
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		return "${dmm_static_host}digital/video/$sn/${sn}-$id.jpg";
		//return "http://pics.dmm.co.jp/digital/video/$sn/${sn}jp-$id.jpg";
	case 2:
		return "${dmm_static_host}digital/amateur/$sn/${sn}js-00$id.jpg";
	break;
	case 5:
	case 6:
	case 7:
		$dvd = $channel == 7 ? 2 : 1;
		$ave_img_folder = ($rating == 1 ? 'new' : 'archive');
		global $ave_static_host;
		return "$ave_static_host$ave_img_folder/screen_shot/dvd$dvd$sn.jpg";
		//return "http://imgs.aventertainments.com/$ave_img_folder/screen_shot/dvd$dvd$sn.jpg";
	case 8:
		global $mgs_static_host;
		if (isset($GLOBALS['company_en_'.$sn])) {
			$company_en = $GLOBALS['company_en_'.$sn];
		}
		else {
			$company_en = execute_scalar("select company_en from video, mgs_company where sn = '$sn' and video.company = mgs_company.company");
			$GLOBALS['company_en_'.$sn] = $company_en;
		}
		preg_match_all('/(.*[A-Za-z]+)\-*(\d+)$/', $sn, $matches, PREG_SET_ORDER);
		return $mgs_static_host.'/images/'.$company_en.'/'.$matches[0][1].'/'.$matches[0][2].'/cap_t1_'.($id - 1).'_'.$sn.'.jpg';
	case 10:
		global $pondo1_static_host;
		return "$pondo1_static_host/assets/sample/$sn/thum_106/$id.jpg";
		return "http://www.1pondo.tv/assets/sample/$sn/thum_106/$id.jpg";
	case 11:
		$video_id = substr($sn, 2);
		return "http://www.5xww3.com/media/videos/tmb/$video_id/$id.jpg";
	}
	return '';
}

function get_sample_img_url($sn, $channel, $id, $rating) {
	global $dmm_static_host;
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		return "${dmm_static_host}digital/video/$sn/${sn}jp-$id.jpg";
		//return "http://pics.dmm.co.jp/digital/video/$sn/${sn}jp-$id.jpg";
	case 2:
		return "${dmm_static_host}digital/amateur/$sn/${sn}jp-00$id.jpg";
	break;
	case 5:
	case 6:
	case 7:
		$dvd = $channel == 7 ? 2 : 1;
		$ave_img_folder = ($rating == 1 ? 'new' : 'archive');
		global $ave_static_host;
		return "$ave_static_host$ave_img_folder/screen_shot/dvd$dvd$sn.jpg";
		//return "http://imgs.aventertainments.com/$ave_img_folder/screen_shot/dvd$dvd$sn.jpg";
	case 8:
		global $mgs_static_host;
		if (isset($GLOBALS['company_en_'.$sn])) {
			$company_en = $GLOBALS['company_en_'.$sn];
		}
		else {
			$company_en = execute_scalar("select company_en from video, mgs_company where sn = '$sn' and video.company = mgs_company.company");
			$GLOBALS['company_en_'.$sn] = $company_en;
		}
		preg_match_all('/(.*[A-Za-z]+)\-*(\d+)$/', $sn, $matches, PREG_SET_ORDER);
		return $mgs_static_host.'/images/'.$company_en.'/'.$matches[0][1].'/'.$matches[0][2].'/cap_e_'.($id - 1).'_'.$sn.'.jpg';
	case 9:
		$file_name_str = execute_scalar("select url from sample_url where sn = '$sn'");
		$file_names = explode("\t", $file_name_str);
		$file_name = $file_names[$id - 1];
		global $tkh_static_host;
		return "${tkh_static_host}/media/$sn/scap/$file_name.jpg";
	case 10:
		global $pondo1_static_host;
		return "${pondo1_static_host}assets/sample/$sn/popu/$id.jpg";
		return "http://www.1pondo.tv/assets/sample/$sn/popu/$id.jpg";
	}
	return '';
}

function get_thumb_url($star_pic_name) {
	global $dmm_static_host;
	return "${dmm_static_host}mono/actjpgs/$star_pic_name.jpg";
	return "http://pics.dmm.co.jp/mono/actjpgs/$star_pic_name.jpg";
}

function snn_add_hyphen($snn) {
	if (strpos($snn, '1pondo') === 0) {
		return "1pondo-".substr($snn, 6);
	}
	if (strpos($snn, '-') > 0) return $snn;
	if (preg_match("/([A-Za-z]+)(\d+)/", $snn, $matches)) {
		if (strlen($matches[1]) > 1) {
			return $matches[1].'-'.$matches[2];
		}
		else {
			return $snn;
		}
	}
	else {
		return $snn;
	}
}

function get_rand_dmm_thumb_html() {
	$html = '';
	$result = mysql_query("select title, sn, channel, rating, seed_popularity from video where channel = 1 order by seed_popularity desc limit ".rand(0, 200).', 8');
	while (list($title, $sn, $channel, $rating, $seed_popularity) = mysql_fetch_array($result)) {
		$video = array($title, $sn, $channel, $rating, $seed_popularity);
		$videos[] = $video;
	}
	$html = '<div class="panel panel-info">';
	$html .= '<div class="panel-heading"><a href="/list/popularity/1">'.i18n('popularity').'</a></div>';
	$html .= '<div class="panel-body">';
	$col = 0;
	foreach ($videos as $video) {
		if ($col % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		list($title, $sn, $channel, $rating, $seed_popularity) = $video;
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\"><img data-original=\"".get_cover_img_url($sn, $channel, $rating)."\"><br>$title".download_icon($seed_popularity)."</a></div></div>";
		if ($col % 4 == 3) $html .= '</div></div>';
		++$col;
	}
	if ($counter % 4 != 0) $html .= '</div></div>';
	$html .= '</div></div>';
	return $html;
}

function download_icon($seed_popularity) {
	return $seed_popularity > 0 ? '<span class="glyphicon glyphicon-download"></span>' : '';
}

function save_emule($kw, $seeds) {
	$db_conn = conn_dmm_db();
	if (!isset($seeds) || count($seeds) == 0) return;
	if (preg_match('/([a-zA-Z]+)[ \-]?(\d+)/', $kw, $matches) == 1) {
		$snn = sprintf("%s%03s", $matches[1], $matches[2]);
	}
	else return;
	$sn = execute_scalar("select sn from video where sn_normalized = '$snn'");
#	error_log("emule $sn $snn");
	if (isset($sn)) {
		foreach ($seeds as $seed) {
			list($padding, $file_name, $file_size, $file_hash, $available_sources, $completed_sources) = $seed;
			$file_name = urldecode($file_name);
			if (validate_seed_name($snn, $file_name)) {
				error_log("emule save $sn $snn $file_name");
				mysql_query("replace into emule(sn, hash, name, size, available_sources, completed_sources) values('$sn', '$file_hash', '".addslashes($file_name)."', $file_size, $available_sources, $completed_sources)");
				error_log("replace into emule(sn, hash, name, size, available_sources, completed_sources) values('$sn', '$file_hash', '".addslashes($file_name)."', $file_size, $available_sources, $completed_sources)");
			}
			else {
				error_log("emule mismatch $sn $snn $file_name");
			}
		}
	}
}

function validate_seed_name($snn, $name) {
	if (preg_match('/([a-zA-Z]+)[ \-]?0*(\d+)/', $snn, $matches) == 1) {
		$snn = strtolower($matches[1]).$matches[2];
		$snn_prefix = strtolower($matches[1]);
		$snn_suffix = $matches[2];
	}
	$alphabet = '';
//	preg_match_all('/([a-zA-Z\d]+)/', $name, $matches);
	if (preg_match("/${snn_prefix}[ \-]?$snn_suffix/", strtolower($name), $matches) == 1) {
		error_log("emule validate passed, $snn");
		return true;
	}
	return false;
	foreach ($matches[1] as $match) {
		$str = strtolower($match);
		$str = trim($str, '0');
		$alphabet .= $str;
	}
	return str_contain($alphabet, $snn);
}

function get_company_icon_html($source, $icon) {
	if ($icon == '') return '';
	$img_url = get_company_icon($source, $icon);
	return "<img data-original=\"$img_url\" />";
}

function get_company_icon($source, $logo) {
	switch ($source) {
	case 0:
		global $dmm_logo_static_host;
		return "$dmm_logo_static_host/p/maker_logo/$logo.gif";
		//return "http://p.dmm.co.jp/p/maker_logo/$logo.gif";
	case 1:
		global $mgs_static_host;
		return "${mgs_static_host}img/pc/$logo.gif";
	case 2:
		global $ave_static_host;
		return "${ave_static_host}img/studio_ic/$logo";
	}
}

function get_img_tag($img_url) {
	$str_pos = strpos($img_url, 'dmm.co.jp');
	if ($str_pos > 0) {
		$failover_url = 'https://static.jporndb.com'.substr($img_url, $str_pos + 9);
		return "<img class=\"img-responsive\" src=\"$img_url\" onerror=\"this.onerror=null;this.src='$failover_url'\">";
	}
	$str_pos = strpos($img_url, '5xww3');
	if ($str_pos > 0) {
		$failover_url = str_replace('tmb', 'tmb1', $img_url);
		return "<img class=\"img-responsive\" src=\"$img_url\" onerror=\"this.onerror=null;this.src='$failover_url'\">";
	}
	return "<img class=\"img-responsive\" src=\"$img_url\">";
}

function sort_by_release_date($video1, $video2) {
	return -strcmp($video1['release_date'], $video2['release_date']);
}

function get_companies_new_release($companies) {
	$videos = array();
	foreach ($companies as $company) {
		$filtered_videos = array();
		$company_videos = execute_dataset("select title, sn, sn_normalized, channel, rating, release_date from video where company = '$company' order by release_date desc limit 8");
		foreach ($company_videos as $company_video) {
			if (strpos($company_video['sn_normalized'], 'smbd') === 0
			|| strpos($company_video['sn_normalized'], 'cwpbd') === 0
			|| strpos($company_video['sn_normalized'], 'drgbd') === 0
			|| strpos($company_video['sn_normalized'], 'dsambd') === 0
			|| strpos($company_video['sn_normalized'], 'cw3d2dbd') === 0
			) {}
			else {
				$filtered_videos[] = $company_video;
			}
		}
		$videos = array_merge($videos, $filtered_videos);
	}
	$company_videos = execute_dataset("select title, sn, sn_normalized, channel, rating, release_date from video where sn_normalized < 'n9' order by sn_normalized desc limit 2");
	$videos = array_merge($videos, $company_videos);
	usort($videos, 'sort_by_release_date');

	return array_slice($videos, 0, 12);
	return $videos;
}

function process_play_topic($content) {
	global $is_spider;
	if ($is_spider) return $content;
	$content = preg_replace('/<a .+?\>/', '', $content);
	$content = preg_replace('/<\/a>/', '', $content);
	$content = preg_replace('/<img /', '<img class="img-responsive" ', $content);
	$content = preg_replace('/\d\.bp\.blogspot\.com/', 'static.jporndb.com', $content);
	$content = preg_replace('/onload="thumbImg\(this\)"/', '', $content);
	$content = preg_replace('/src="\//', 'src="https://static.jporndb.com/', $content);
	return $content;
}

?>
