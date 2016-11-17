<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$sort_by = $_GET['sort_by'];
$page = $_GET['page'];
$page_size = 48;

if ($sort_by == 'rank') {
	$order_by = 'rank';
	$group_name_key = 'hottest';
}
else if ($sort_by == 'popularity') {
	$order_by = 'seed_popularity desc';
	$group_name_key = 'popularity';
}
else if ($sort_by == 'favourite') {
	$order_by = 'bookmark_rank';
	$group_name_key = 'favourite';
}
else if ($sort_by == 'release_date') {
	$order_by = 'release_date desc';
	$group_name_key = 'latest';
}
if (isset($_GET['star_id'])) {
	if (isset($_GET['ave']) && $_GET['ave'] == 1) {
		$ave = 1;
		$star_name = urldecode($_GET['star_id']);
		$result = mysql_query('select title, video.sn, sn_normalized, channel, rating, seed_popularity from video, ave_sn_star where video.sn = ave_sn_star.sn and star = "'.$star_name.'"');
		$label = $star_name.i18n('star_all_video');
	}
	else if (isset($_GET['mgs']) && $_GET['mgs'] == 1) {
		$mgs = 1;
		$star_name = urldecode($_GET['star_id']);
		$result = mysql_query('select title, video.sn, sn_normalized, channel, rating, seed_popularity from video, star where video.sn = star.sn and star = "'.$star_name.'"');
		$label = $star_name.i18n('star_all_video');
	}
	else if (isset($_GET['1pondo']) && $_GET['1pondo'] == 1) {
		$pondo1 = 1;
		$star_name = execute_scalar('select name from 1pondo_star_info where id = '.$_GET['star_id']);
		$result = mysql_query('select title, video.sn, sn_normalized, channel, rating, seed_popularity from video, 1pondo_sn_star where video.sn = 1pondo_sn_star.sn and star_id = "'.$_GET['star_id'].'"');
		$label = $star_name.i18n('star_all_video');
	}
	else {
		$result = mysql_query("select title, video.sn, sn_normalized, channel, rating, video.seed_popularity from video, star, star_info where star_info.id = ".$_GET['star_id']." and star_info.name = star.star and star.sn = video.sn order by video.release_date limit ".(($page - 1) * $page_size).", $page_size");
		$label = execute_scalar('select name from star_info where id = '.$_GET['star_id']).i18n('star_all_video');
	}
	$group_name_key = "star_".$_GET['star_id'];
	$current_url = "star/".$_GET['star_id'].'/1';
}
else if (isset($_GET['release_year'])) {
	$release_year = $_GET['release_year'];
	$current_year = date('Y');
	$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where release_year = $release_year order by seed_popularity desc limit ".(($page - 1) * $page_size).", $page_size");
	$group_name_key = 'release_year_'.$release_year;
	$current_url = "best_seller/$release_year/$page";
}
else if (isset($_GET['channel'])) {
	$channel = $_GET['channel'];
	if ($channel == 1 || $channel == 1 || $channel == 3 || ($channel >= 5 && $channel <= 7) || $channel == 9 || $channel == 10) {
		$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where channel = $channel order by seed_popularity desc limit ".(($page - 1) * $page_size).", $page_size");
	}
	else {
		$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where channel = $channel order by fav_count desc limit ".(($page - 1) * $page_size).", $page_size");
	}
	$label = i18n("channel_$channel");
	$group_name_key = $label;
	$current_url = "channel/$channel/$page";
}
else if (isset($_GET['series'])) {
	$series_id = $_GET['series'];
	if (isset($_GET['1pondo']) && $_GET['1pondo'] == 1) {
		$label = execute_scalar("select name from 1pondo_series where id = $series_id");
		$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video join 1pondo_series on series_id = 1pondo_series.id where channel = 10 and 1pondo_series.id = $series_id order by seed_popularity desc limit ".(($page - 1) * $page_size).", $page_size");
		$current_url = "1pondo_series/$series_id/1";
	}
	else {
		$label = execute_scalar("select name from series where id = $series_id");
		$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video join series on series_id = series.id where series.id = $series_id order by fav_count desc limit ".(($page - 1) * $page_size).", $page_size");
		$current_url = "series/$series_id/1";
	}
	$group_name_key = $label;
}
else if (isset($_GET['genre'])) {
	$genre_id = $_GET['genre'];
	$label = i18n(execute_scalar("select genre from genre_list where id = $genre_id"));
	$result = mysql_query("select title, video.sn, sn_normalized, channel, rating, seed_popularity from video, genre, genre_list where video.sn = genre.sn and genre.genre = genre_list.genre and genre_list.id = $genre_id order by fav_count desc limit ".(($page - 1) * $page_size).", $page_size");
	$group_name_key = $label;
	$current_url = "genre/$genre_id/1";
}
else if (isset($_GET['snp'])) {
	$snp = $_GET['snp'];
	$snp_end = true;
	$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where  sn_normalized < '${snp}999' order by sn_normalized desc limit ".(($page - 1) * $page_size).", $page_size");
	$label = i18n('sn').strtoupper($snp).i18n('series');
	$group_name_key = $label;
	$current_url = "snp/$snp/$page";
}
else if (isset($_GET['company'])) {
	$company_url_encoded = $_GET['company'];
	$company = addslashes(urldecode($_GET['company']));
	$label = $company;
	$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where company = '$company' order by release_date desc limit ".(($page - 1) * $page_size).", $page_size");
	$current_url = "company/".$_GET['company'].'/1';
}
else if ($sort_by == 'release_date') {
	$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where release_date <= now() order by $order_by limit ".(($page - 1) * $page_size).", $page_size");
	$label = i18n($group_name_key);
	$current_url = "list/$order_by/$page";
}
else if ($sort_by == 'hot_download') {
	$result = mysql_query('select title, video.sn, sn_normalized, channel, rating, seed_popularity from video, pb_rank where video.sn = pb_rank.sn order by pb_rank.rank');
	$label = i18n('hot_download');
	$current_url = "list/hot_download/$page";
}
else {
	$result = mysql_query("select title, sn, sn_normalized, channel, rating, seed_popularity from video where channel = 1 order by $order_by limit ".(($page - 1) * $page_size).", $page_size");
	$label = i18n($group_name_key);
	$current_url = "list/$order_by/$page";
}
$html_title = $label;
while (list($title, $sn, $snn, $channel, $rating, $seed_popularity) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating, $seed_popularity);
	$videos[] = $video;
}
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
if (isset($release_year)) {
	$html .= '<div class="panel-heading">';
	$html .= '<h3 align="center">'.$release_year.' '.i18n('best_seller').'</h3>';
	$html .= '</div>';
}
else {
	$html .= '<div class="panel-heading"><h3 align="center">'.$label.'</h3></div>';
}
$html .= '<div class="panel-body">';
$column = 0;
foreach ($videos as $video) {
	list($title, $sn, $snn, $channel, $rating, $seed_popularity) = $video;
	$url = "/video/$sn";
	if ($column % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
	if (isset($snp) && strpos($snn, $snp) === false) {
		$snp_end = true;
		break;
	}
	else {
		$snp_end = false;
	}
	$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\">".get_img_tag(get_cover_img_url($sn, $channel, $rating, $snn))."<br>$title $snn".download_icon($seed_popularity)."</a></div></div>";
	//	$html .= '<div class="media"><a class="pull-left" href="'.$url.'"><img class="media-object" data-original="'.get_cover_img_url($sn, $channel).'" /></a>';
	//	$html .= '<div class="media-body"><h4 class="media-heading"><a href="'.$url.'">'.$title.'</a></h4></div></div>';
	if ($column % 4 == 3) $html .= '</div></div>';
	++$column;
}
if (!$is_spider) {
	$html .= '<div class="row"><div class="col-md-12"><ul class="pager">';
	if (isset($_GET['star_id'])) {
		$page_up_enabled = $page > 1;
		if ($page > 1) {
			$html .= '<li class="previous"><a href="/star/'.$_GET['star_id'].'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		if ($column == $page_size) {
			$html .= '<li class="next"><a href="/star/'.$_GET['star_id'].'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
		}
	}
	else if (isset($release_year)) {
		if ($release_year < $current_year) {
			$html .= '<li class="previous"><a href="/best_seller/'.($release_year + 1).'/1" target="_self">'.($release_year + 1).' '.i18n('best_seller').'</a></li>';
		}
		if ($page > 1) {
			$html .= '<li class="previous"><a href="/best_seller/'.$release_year.'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		$html .= '<li class="next"><a href="/best_seller/'.($release_year - 1).'/1" target="_self">'.($release_year - 1).' '.i18n('best_seller').'</a></li>';
		$html .= '<li class="next"><a href="/best_seller/'.$release_year.'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	}
	else if (isset($_GET['channel'])) {
		$channel = $_GET['channel'];
		if ($page > 1) {
			$html .= "<li class=\"previous\"><a href=\"/channel/$channel/".($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		$html .= "<li class=\"next\"><a href=\"/channel/$channel/".($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	}
	else if (isset($series_id)) {
		if ($page > 1) {
			$html .= "<li class=\"previous\"><a href=\"/series/$series_id/".($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		if ($column == $page_size) {
			$html .= "<li class=\"next\"><a href=\"/series/$series_id/".($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
		}
	}
	else if (isset($genre_id)) {
		if ($page > 1) {
			$html .= "<li class=\"previous\"><a href=\"/genre/$genre_id/".($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		if ($column == $page_size) {
			$html .= "<li class=\"next\"><a href=\"/genre/$genre_id/".($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
		}
	}
	else if (isset($snp)) {
		if ($page > 1) {
			$html .= "<li class=\"previous\"><a href=\"/snp/$snp/".($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		if (!$snp_end) {
			$html .= "<li class=\"next\"><a href=\"/snp/$snp/".($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
		}
	}
	else if (isset($company)) {
		if ($page > 1) {
			$html .= "<li class=\"previous\"><a href=\"/company/$company_url_encoded/".($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		if ($column == $page_size) {
			$html .= "<li class=\"next\"><a href=\"/company/$company_url_encoded/".($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
		}
	}
	else if ($sort_by == 'hot_download') {}
	else {
		if ($page > 1) {
			$html .= '<li class="previous"><a href="/list/'.$sort_by.'/'.($page - 1).'" target="_self">&larr; '.i18n('page_up').'</a></li>';
		}
		$html .= '<li class="next"><a href="/list/'.$sort_by.'/'.($page + 1).'" target="_self">&rarr; '.i18n('page_down').'</a></li>';
	}
	$html .= '</ul></div></div>';
}
$html .= '</div></div>';
$html .= duoshuo_html('jporndb', $group_name_key, $html_title, "https://cn.jporndb.com/$current_url");
$html .= '</div></div>';

$target = '_blank';
if (!isset($html_title)) {
	$html_title = 'Japan Porn Database';
}
require_once('header.php');
echo $html;
require_once('footer.php');

function output_group($videos, $group_name, $sort_by) {
	return $html;
}

?>
