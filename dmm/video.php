<?
require_once("init.php");
require_once("dmm_lib.php");
require_once("i18n.php");
require_once("ads.php");

$sn = $_GET['sn'];
$db_conn = conn_dmm_db();
list($title, $release_date, $runtime, $director, $series_id, $company, $fav_count, $rating, $sample_image_num, $description, $channel, $snn) = execute_vector("select title, release_date, runtime, director, series_id, company, fav_count, rating, sample_image_num, description, channel, sn_normalized from video where sn = '$sn'");

$sn_prefix = $snn;
if (strpos($snn, '1pondo') === 0) {
	$sn_prefix = '1pondo';
}
else if (strpos($sn, '-') > 0) {
	$sn_prefix = substr($sn, 0, strpos($sn, '-'));
}
else {
	if (preg_match("/([A-Za-z]+)(\d+)/", $snn, $matches)) {
		$sn_prefix = $matches[1];
	}
}

if ($series_id != 0) {
	if ($channel <= 4) {
		$series_name = execute_scalar("select name from series where id = $series_id");
		$series_set = execute_dataset("select sn, sn_normalized, title, channel, seed_popularity from video join series on video.series_id = series.id where series_id = $series_id and sn <> '$sn' order by release_date desc limit 8");
	}
	else if ($channel == 10) {
		$series_name = execute_scalar("select name from 1pondo_series where id = $series_id");
		$series_set = execute_dataset("select sn, sn_normalized, title, channel, seed_popularity from video join 1pondo_series on video.series_id = 1pondo_series.id where series_id = $series_id and channel = 10 and sn <> '$sn' order by release_date desc limit 8");
	}
}

$sn_set = execute_dataset("select sn, sn_normalized, title, channel, rating, seed_popularity from video where sn_normalized < '$snn' order by sn_normalized desc limit 8");
foreach ($sn_set as $index => $video) {
	if ($sn_prefix == '1pondo') {
		if (strpos($video[1], $sn_prefix) === false) {
			unset($sn_set[$index]);
		}
	}
	else {
		if (preg_match("/(\w+)\-/", $video[1], $matches)) {
			if ($sn_prefix != $matches[1]) {
				unset($sn_set[$index]);
			}
		}
		else if (preg_match("/([A-Za-z]+)(\d+)/", $video[1], $matches)) {
			if ($sn_prefix != $matches[1]) {
				unset($sn_set[$index]);
			}
		}
	}
}

$snn = snn_add_hyphen($snn);

if ($channel <= 4) {
	$stars = execute_column("select star from star where sn = '$sn'");
	$star_infos = execute_dataset("select star_info.id, star_info.name, star_info.pic_name from star_info, star where sn = '$sn' and star_info.name = star.star");
}
else if ($channel <= 7) {
	$stars = execute_column("select star from ave_sn_star where sn = '$sn'");
	$star_infos = execute_dataset("select star, star from ave_sn_star where sn = '$sn'");
}
else if ($channel == 8 || $channel == 9) {
	$stars = execute_column("select star from star where sn = '$sn'");
	$star_infos = execute_dataset("select star, star from star where sn = '$sn'");
}
else if ($channel == 10) {
	$stars = execute_column("select name from 1pondo_sn_star, 1pondo_star_info where 1pondo_sn_star.star_id = 1pondo_star_info.id and sn = '$sn'");
	$star_infos = execute_dataset("select 1pondo_star_info.id, name from 1pondo_star_info, 1pondo_sn_star where star_id = 1pondo_star_info.id and sn = '$sn'");
}
$genres = execute_dataset("select genre_list.id, genre.genre from genre_list, genre where sn = '$sn' and genre.genre = genre_list.genre");
$html_title = "$title $snn ".implode(' ', $stars).' bittorrent '.i18n('download');

$html = '<div class="row"><div class="col-md-7 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= "<div class=\"panel-heading\"><h3>$title <small>$snn ".implode(' ', $stars)."</small></h3></div>";
$html .= '<div class="panel-body">';
$html .= '<div class="row">';
$html .= '<div class="col-md-3">'.get_img_tag(get_cover_img_url($sn, $channel, $rating, $snn)).'</div>';
$html .= '<div class="col-md-9">';
if (count($star_infos) > 0) {
	$html .= '<b>'.i18n('star').'</b>: ';
	foreach ($star_infos as $star_info) {
		list($star_id, $star_name, $star_pic_name) = $star_info;
		if ($channel <= 4) {
			$html .= "<a href=\"/star/$star_id/1\">$star_name</a> &nbsp; ";
		}
		else if ($channel <= 7) {
			$html .= "<a href=\"/ave_star/".urlencode($star_name)."/1\">$star_name</a> &nbsp; ";
		}
		else if ($channel == 8 || $channel == 9) {
			$html .= "$star_name &nbsp; ";
		}
		else if ($channel == 10) {
			$html .= "<a href=\"/1pondo_star/$star_id/1\">$star_name</a> &nbsp; ";
		}
	}
	$html .= '<br>';
}
if ($company != '') $html .= '<b>'.i18n('company')."</b>: <a href=\"/company/".urlencode($company)."/1\">$company</a><br>";
if (count($genres) > 0) {
	$html .= '<b>'.i18n('genre').'</b>: ';
	foreach ($genres as $genre_info) {
		list($genre_id, $genre) = $genre_info;
		$html .= " <a href=\"/genre/$genre_id/1\">".i18n($genre)."</a>";
	}
	$html .= '<br>';
}
$html .= '<b>'.i18n('sn')."</b>: $snn<br>";
$html .= '<b>'.i18n('release_date')."</b>: $release_date<br>";
$html .= '<b>'.i18n('runtime')."</b>: $runtime ".i18n('minute')."<br>";
if ($channel <= 4) {
	$html .= '<b>'.i18n('fav_count')."</b>: $fav_count<br>";
	$html .= '<b>'.i18n('rating')."</b>: <img data-original=\"/img/$rating.gif\" /><br>";
}
else if ($channel == 8) {
	$html .= '<b>'.i18n('fav_count')."</b>: $fav_count<br>";
	$html .= '<b>'.i18n('rating')."</b>: ".($rating / 10)."<br>";
}
if ($series_id != 0) {
	if ($channel <= 4) {
		$html .= '<b>'.i18n('series')."</b>: <a href=\"/series/$series_id/1\">$series_name</a>";
	}
	else if ($channel == 10) {
		$html .= '<b>'.i18n('series')."</b>: <a href=\"/1pondo_series/$series_id/1\">$series_name</a>";
	}
}
$html .= '</div>';	//end of md-9
$html .= '</div>';	//end of row
if ($channel == 8) {
	$mgs_company_en = execute_scalar("select company_en from mgs_company where company = '$company'");
	$prev_video = $mgs_company_en;
	if (isset($prev_video)) {
		$sn_arr = explode('-', $sn);
		$sn_prefix_lower = strtolower($sn_arr[0]);
		$sn_prefix_upper = strtoupper($sn_arr[0]);
		$sn_suffix= $sn_arr[1];
		$sn_upper = strtoupper($sn);
		$html .= '<video controls="controls" width="100%">';
		$html .= "<source src=\"http://chdl34.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/${sn_upper}_sample.mp4\">";
		$html .= "<source src=\"http://chdl34.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/${sn_upper}.mp4\">";
		$html .= "<source src=\"http://chdl33.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/${sn_upper}_sample.mp4\">";
		$html .= "<source src=\"http://chdl33.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/${sn_upper}.mp4\">";
		$html .= "<source src=\"http://chdl34.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/${sn_upper}_35a.mp4\">";
		if (preg_match('/\d+(.+)/', $sn_upper, $matches)) {
			$html .= "<source src=\"http://chdl33.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/".$matches[1].".mp4\">";
			$html .= "<source src=\"http://chdl34.mgstage.com/sample/$mgs_company_en/$sn_prefix_lower/$sn_suffix/".$matches[1].".mp4\">";
			$html .= "<source src=\"http://chdl33.mgstage.com/sample/$mgs_company_en/$sn_prefix_upper/$sn_suffix/".$matches[1]."_sample.mp4\">";
		}
		$html .= '</video>';
	}
}
else if ($channel <= 4) {
	$prev_video = execute_scalar("select url from sample_url where sn = 'v_$sn'");
	if (isset($prev_video)) {
		$html .= '<video controls="controls" width="100%">';
		$html .= "<source src=\"http://cc3001.r18.com/litevideo/freepv/".substr($sn, 0, 1)."/".substr($sn, 0, 3)."/$prev_video/${prev_video}_dmb_w.mp4\" type=\"video/mp4\">";
		$html .= "<source src=\"http://cc3001.r18.com/litevideo/freepv/".substr($sn, 0, 1)."/".substr($sn, 0, 3)."/$prev_video/${prev_video}_sm_s.mp4\" type=\"video/mp4\">";
		$html .= "<source src=\"http://cc3001.r18.com/litevideo/freepv/".substr($sn, 0, 1)."/".substr($sn, 0, 3)."/$prev_video/${prev_video}_sm_w.mp4\" type=\"video/mp4\">";
		$html .= "</video>";
	}
}
else if ($channel == 9) {
	$html .= '<video controls="controls" width="100%">';
	$html .= "<source src=\"http://my.cdn.tokyo-hot.com/media/samples/$sn.mp4\" type=\"video/mp4\">";
	$html .= '</video>';
}
else if ($channel == 10) {
	$html .= '<video controls="controls" width="100%">';
	global $pondo1_static_host;
	$html .= "<source src=\"$pondo1_static_host/sample/movies/$sn/480p.mp4\" type=\"video/mp4\">";
	$html .= '</video>';
}
else {
	if ($sample_image_num >= 1) {
		$html .= "<div class=\"row\"><div class=\"col-md-12\">";
		$img_url = get_sample_img_url($sn, $channel, 1, 0);
		$href = "/snapshot/$sn/$channel/1";
		$html .= "<a href=\"$href\">".get_img_tag($img_url)."</a>";
		$html .= '</div></div>';
	}
}
$html .= "<div class=\"row\"><div class=\"col-md-12\">$description</div></div>";
$html .= '</div>';	//end of panel body
$html .= '</div>';	//end of panel
if (count($star_infos) > 0 && $channel <= 4) {
	foreach ($star_infos as $star_info) {
		list($star_id, $star_name, $star_pic_name) = $star_info;
		$html .= '<div class="panel panel-info">';
		$html .= '<div class="panel-heading"><a href="/star/'.$star_id.'/1">'.$star_name.'</a>'.i18n('star_video').'</div>';
		$html .= '<div class="panel-body">';
		$counter = 1;
		$html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/star/$star_id/1\">".get_img_tag(get_thumb_url($star_pic_name))."<br>$star_name".i18n('star_all_video')."</a></div></div>";
		$sns = execute_column("select star.sn from star, video where star.sn = video.sn and star.sn <> '$sn' and star.star = '$star_name' and channel <= 4 order by seed_popularity desc limit 8");
		$star_video_output_count = 0;
		foreach ($sns as $star_sn) {
			if ($star_sn == $sn) continue;
			if ($star_video_output_count == 7) break;
			++$star_video_output_count;
			list($star_snn, $star_title, $star_channel, $star_seed_popularity) = execute_vector("select sn_normalized, title, channel, seed_popularity from video where sn = '$star_sn'");
			if ($counter % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
			$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$star_sn\">".get_img_tag(get_cover_img_url($star_sn, $star_channel, 2, $star_snn))."<br>$star_title".download_icon($star_seed_popularity)."</a></div></div>";
			if ($counter % 4 == 3) $html .= '</div></div>';
			++$counter;
		}
		if ($counter % 4 != 0) $html .= '</div></div>';
		$html .= '</div></div>';
	}
}
else if (count($star_infos) > 0 && $channel == 10) {
	foreach ($star_infos as $star_info) {
		list($star_id, $star_name) = $star_info;
		$sns = execute_column("select sn from 1pondo_sn_star where sn <> '$sn' and star_id = $star_id limit 8");
		if (count($sns) == 0) continue;
		$html .= '<div class="panel panel-info">';
		$html .= '<div class="panel-heading"><a href="/1pondo_star/'.$star_id.'/1">'.$star_name.'</a>'.i18n('star_video').'</div>';
		$html .= '<div class="panel-body">';
		$counter = 0;
		//$html .= '<div class="row"><div class="col-md-12">';
		$star_video_output_count = 0;
		foreach ($sns as $star_sn) {
			if ($star_sn == $sn) continue;
			if ($star_video_output_count == 8) break;
			++$star_video_output_count;
			list($star_snn, $star_title, $star_channel, $star_seed_popularity) = execute_vector("select sn_normalized, title, channel, seed_popularity from video where sn = '$star_sn'");
			if ($counter % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
			$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$star_sn\">".get_img_tag(get_cover_img_url($star_sn, $star_channel, 2, $star_snn))."><br>$star_title".download_icon($star_seed_popularity)."</a></div></div>";
			if ($counter % 4 == 3) $html .= '</div></div>';
			++$counter;
		}
		if ($counter % 4 != 0) $html .= '</div></div>';
		$html .= '</div></div>';
	}
}
if (isset($series_set) && count($series_set) > 0) {
	$html .= '<div class="panel panel-info">';
	if ($channel <= 4) {
		$html .= "<div class=\"panel-heading\"><a href=\"/series/$series_id/1\">$series_name</a></div>";
	}
	else if ($channel == 10) {
		$html .= "<div class=\"panel-heading\"><a href=\"/1pondo_series/$series_id/1\">$series_name</a></div>";
	}
	$html .= '<div class="panel-body">';
	$counter = 0;
	foreach ($series_set as $series_video) {
		list($series_sn, $series_snn, $series_title, $series_channel, $series_seed_popularity) = $series_video;
		if ($counter % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$series_sn\">".get_img_tag(get_cover_img_url($series_sn, $series_channel, 2, $series_snn))."<br>$series_title".download_icon($series_seed_popularity)."</a></div></div>";
		++$counter;
		if ($counter % 4 == 0) $html .= '</div></div>';
	}
	if ($counter % 4 != 0) $html .= '</div></div>';
	$html .= '</div></div>';
}

if (isset($sn_set) && count($sn_set) > 0) {
	$html .= '<div class="panel panel-info">';
	$html .= "<div class=\"panel-heading\"><a href=\"/snp/$sn_prefix/1\">".strtoupper($sn_prefix)."</a>".i18n('series')."</div>";
	$html .= '<div class="panel-body">';
	$counter = 0;
	list($company_source, $company_logo) = execute_vector("select source, logo from company where name = '$company'");
	if (isset($company_source)) {
		$html .= '<span class="pull-right"><a href="/company/'.urlencode($company).'/1">'.get_company_icon_html($company_source, $company_logo).'</a></span>';
	}
	foreach ($sn_set as $sn_video) {
		list($sn_sn, $sn_snn, $sn_title, $sn_channel, $sn_rating, $sn_seed_popularity) = $sn_video;
		if ($counter % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn_sn\">".get_img_tag(get_cover_img_url($sn_sn, $sn_channel, $sn_rating, $sn_snn))."<br>$sn_title".download_icon($sn_seed_popularity)."</a></div></div>";
		++$counter;
		if ($counter % 4 == 0) $html .= '</div></div>';
	}
	if ($counter % 4 != 0) $html .= '</div></div>';
	$html .= '</div></div>';
}

$seed_set = execute_dataset("select name, size, hot, magnet from seed where sn = '$sn' and magnet like 'magnet:%'");
if (count($seed_set) > 0) {
	$html .= '<div class="panel panel-info"><table class="table table-striped">';
	$html .= '<tr><th>'.i18n('seed_name').'</th><th>'.i18n('seed_size').'</th><th>'.i18n('seed_popularity').'</th><th>'.i18n('seed_magnet').'</th></tr>';
	foreach ($seed_set as $seed_row) {
		$html .= '<tr><td>'.$seed_row['name'].'</td><td>'.human_filesize($seed_row['size']).'</td><td>'.$seed_row['hot'].'</td><td><a href="'.$seed_row['magnet'].'">'.i18n('seed_download_magnet').'</a></td></tr>';
	}
	$html .= '</table></div>';
}

$emule_set = execute_dataset("select sn, hash, name, size, available_sources, completed_sources from emule where sn = '$sn'");
if (count($emule_set) > 0) {
	$html .= '<div class="panel panel-info"><table class="table table-striped">';
	$html .= '<tr><th>'.i18n('emule_name').'</th><th>'.i18n('seed_size').'</th><th>'.i18n('seed_available_sources').'</th><th>'.i18n('seed_completed_sources').'</th><th>'.i18n('download').'</th></tr>';
	foreach ($emule_set as $emule_row) {
		$html .= '<tr><td>'.$emule_row['name'].'</td><td>'.human_filesize($emule_row['size']).'</td><td>'.$emule_row['available_sources'].'</td><td>'.$emule_row['completed_sources'].'</td><td><a href="ed2k://|file|'.$emule_row['name'].'|'.$emule_row['size'].'|'.$emule_row['hash'].'|/">'.i18n('download').'</a></td></tr>';
	}
	$html .= '</table></div>';
}
$html .= '<div class="panel panel-info">'.i18n('share_request').'<div class="addthis_sharing_toolbox"></div></div>';
//$html .= '<div class="panel panel-info">'.i18n('share_request').'<div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_mshare" data-cmd="mshare" title="分享到一键分享"></a></div><script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{},"image":{"viewList":["qzone","tsina","tqq","renren","weixin","mshare"],"viewText":"分享到：","viewSize":"32"}};with(document)0[(getElementsByTagName(\'head\')[0]||body).appendChild(createElement(\'script\')).src=\'//bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=\'+~(-new Date()/36e5)];</script></div>';

if ($lang_short == 'zh') {
//	$html .= $ueads_av_pic;
	$html .= get_ck101_board_random_topic(70);
}
if ($is_spider) {
	$html .= get_rand_dmm_topic_html();
}
$html .= get_rand_dmm_thumb_html();
//$html .= '<div id="uyan_frame"></div><script type="text/javascript" src="http://v2.uyan.cc/code/uyan.js?uid=2084908"></script>';
$html .= duoshuo_html('jporndb', $sn, $title, "https://www.jav321.com/video/$sn");
$html .= '</div>';	#left column end
$html .= '<div class="col-md-3">';	//thumb
if ($channel != 2 && $channel != 9) {
	$img_url = get_cover_img_large_url($sn, $channel, $rating);
	$href = $img_url;
	$href = "/snapshot/$sn/$channel/0";
	if ($is_spider) $href = '#';
	$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'">'.get_img_tag($img_url).'</a></p></div>';
}
if ($channel <= 4 || $channel == 8 || $channel == 10) {
	$sample_start_index = isset($prev_video) ? 1 : 2;
	for ($i = $sample_start_index; $i <= $sample_image_num; ++$i) {
		if (true || isset($_COOKIE['xM2S_2132_auth'])) {
			$img_url = get_sample_img_url($sn, $channel, $i, 0);
		}
		else {
			$img_url = get_sample_img_thumb_url($sn, $channel, $i, 0);
		}
		$href = "/snapshot/$sn/$channel/$i";
		if ($is_spider) $href = '#';
		$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'">'.get_img_tag($img_url).'</a></p></div>';
	}
}
else if ($channel <= 7) {
	$img_url = get_sample_img_url($sn, $channel, 1, $rating);
	$href = "/snapshot/$sn/$channel/1";
	if ($is_spider) $href = '#';
	$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'">'.get_img_tag($img_url).'</a></p></div>';
}
else if ($channel == 9) {
	$file_name_str = execute_scalar("select url from sample_url where sn = '$sn'");
	$file_names = explode("\t", $file_name_str);
	for ($i = 2; $i <= count($file_names); ++$i) {
		$href = "/snapshot/$sn/$channel/$i";
		$file_name = $file_names[$i - 1];
		global $tkh_static_host;
		if (true || isset($_COOKIE['xM2S_2132_auth'])) {
			$img_url = "${tkh_static_host}media/$sn/scap/$file_name.jpg";
		}
		else {
			$img_url = "${tkh_static_host}media/$sn/scap/$file_name/150x150_default.jpg";
		}
		if ($is_spider) $href = '#';
		$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'">'.get_img_tag($img_url).'</a></p></div>';
	}
}
$html .= '</div></div>';
$html .= '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54c4990a04963235" async="async"></script>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>

