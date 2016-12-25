<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$result = mysql_query("select title, video.sn, sn_normalized, channel, rating from video, pb_rank where video.sn = pb_rank.sn order by pb_rank.rank limit 24");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('hot_download'), '/list/hot_download');

$result = mysql_query('select tid, title, img_url, snn from play_topic order by pub_time desc limit 8');
while (list($tid, $title, $img_url, $snn) = mysql_fetch_array($result)) {
	$topics[] = array($tid, i18n($title), $img_url, $snn);
}
$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading">';
$html .= '<a href="topic_list/1">'.i18n('av_news').'</a>';
$html .= '</div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($topics as $topic) {
	list($tid, $title, $img_url, $snn) = $topic;
	$url = "/topic/$tid";
	if ($column % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"$url\">".get_img_tag($img_url)."<br>$title $snn</a></div></div>";
	if ($column % 4 == 3) $html .= '</div></div>';
	++$column;
}
$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="topic_list/1">'.i18n('more').'</a></p></div></div>';
$html .= '</div></div></div></div>';

$videos = array();
$result = mysql_query("select title, video.sn, sn_normalized, channel, rating from video, sixav where video.sn_normalized = sixav.snn order by sixav.id desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('play_list'), '/play_list');

/*
$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video order by rank limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('hottest'), '/list/rank');
*/

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where release_date between date_sub(now(), interval 7 day) and now() and type = 1 order by fav_count desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('latest'), '/list/release_date');

$snps = execute_column('select snp from snp order by seed_popularity desc limit 12');
$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading"><a href="/snp_list/1">'.i18n('sn').'</a> &nbsp; <a href="/snp_list/1">'.i18n('more').'</a></div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($snps as $snp) {
	if ($column % 6 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-2\"><a href=\"/snp/$snp/1\">".strtoupper($snp)."</a></div>";
	if ($column % 6 == 5) $html .= '</div></div>';
	++$column;
}
$html .= '</div></div></div></div>';

if (false) {
	$videos = get_snps_new_release(array('siro', 'luxu', 'gana', 'dcv', 'ore'));
}
else {
$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where release_date between date_sub(now(), interval 7 day) and now() and type = 2 order by fav_count desc limit 12");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
}
$html .= output_group($videos, i18n('type_2'), '/type/2');

//$videos = get_snps_new_release(array('smd199', 'dsam', 'll', 'tkg', 'dz', 'bt', 'red', 'hey', 'pt', '1pondo', 'mcdv', 'drc', 'cwpbd', 'sky'));
$videos = get_companies_new_release(array('スーパーモデルメディア', 'サムライポルノ', 'ルチャリブレ', '小天狗', 'Climax Zipang', 'キャットウォーク', 'スタジオテリヤキ', 'レッドホットコレクション', 'HEYZO', '一本道', 'メルシーボークー', 'CATCHEYE'));
$html .= output_group($videos, i18n('type_3'), '/type/3');

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where sn like '5x%' order by sn desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('type_6'), '/snp/5x');

//$star_infos = execute_dataset("select id, name, pic_name from star_info order by rank limit 24");
$star_infos = execute_dataset("select id, name, pic_name from star, star_info, pb_rank where star.sn = pb_rank.sn and star.star_id = star_info.id and pic_name <> '' order by pb_rank.rank limit 24");
$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading">'.i18n('top_stars').' &nbsp; <a href="/stars/1">'.i18n('more').'</a></div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($star_infos as $star_info) {
	list($star_id, $star_name, $star_pic_name) = $star_info;
	if ($column % 6 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-2\"><div class=\"thumbnail\"><a href=\"/star/$star_id/1\">".get_img_tag(get_thumb_url($star_pic_name))."<div class=\"caption\"><h4 align=\"center\">$star_name</h3></div></a></div></div>";
	if ($column % 6 == 5) $html .= '</div></div>';
	++$column;
}
$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="/stars/1">'.i18n('more').'</a></p></div></div>';
$html .= '</div></div></div></div>';

$genre_list = execute_dataset('select id, genre from genre_list where featured > 0 order by featured');
$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
//$html .= '<div class="panel-heading">'.i18n('genre').' &nbsp; <a href="/genre_list/1">'.i18n('more').'</a></div>';
$html .= '<div class="panel-heading">'.i18n('genre').'</div>';
$html .= '<div class="panel-body">';
$column = 0;
foreach ($genre_list as $genre_info) {
	list($genre_id, $genre) = $genre_info;
	if ($column % 6 == 0) $html .= '<div class="row"><div class="col-md-12">';
	$html .= "<div class=\"col-xs-6 col-md-2\"><a href=\"/genre/$genre_id/1\">$genre</a></div>";
	if ($column % 6 == 5) $html .= '</div></div>';
	++$column;
}
//$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="/genre_list/1">'.i18n('more').'</a></p></div></div>';
$html .= '</div></div></div></div>';

$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading"><a href="/series_list/1">'.i18n('series').'</a></div>';
$html .= '<div class="panel-body">';
$series_rank_base = rand(0, 40);
for ($series_rank = 1; $series_rank <= 4; ++$series_rank) {
	list($series_id, $series_name) = execute_vector("select id, name from series where rank = $series_rank + ".$series_rank_base);
	$series_set = execute_dataset("select sn, sn_normalized, title, channel from video where series_id = $series_id order by release_date desc limit 4");
	if (count($series_set) == 0) continue;
	$html .= '<div class="row"><div class="col-md-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading"><a href="/series/'.$series_id.'/1">'.$series_name.'</a> &nbsp; <a href="/series/'.$series_id.'/1">'.i18n('more').'</a></div>';
	$html .= '<div class="panel-body">';
	foreach ($series_set as $series_video) {
		list($series_sn, $series_snn, $series_title, $series_channel) = $series_video;
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$series_sn\">".get_img_tag(get_cover_img_url($series_sn, $series_channel))."\"><br>$series_title $series_snn</a></div></div>";
	}
	$html .= '</div></div></div></div>';
}
$html .= '</div></div></div></div>';

$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
$html .= '<div class="panel panel-info">';
$html .= '<div class="panel-heading"><a href="/companies">'.i18n('company').'</a> &nbsp; <a href="/companies">'.i18n('more').'</a></div>';
$html .= '<div class="panel-body">';
$html .= '<div class="row">';
$companies = array('エスワン ナンバーワンスタイル', 'プレステージ', 'アイデアポケット', 'マドンナ', 'SODクリエイト', 'ナチュラルハイ', 'ムーディーズ', 'ワンズファクトリー');
foreach ($companies as $company) {
	list($company_source, $company_logo) = execute_vector("select source, logo from company where name = '$company'");
	$html .= '<div class="col-md-3"><a href="/company/'.urlencode($company).'/1">'.get_company_icon_html($company_source, $company_logo).'</a></div>';
}
$html .= '</div></div></div></div></div>';

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where type = 1 order by seed_popularity desc limit 12");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('popularity'), '/type/1');

/*
$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where channel = 1 order by fav_count desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('favourite'), '/list/favourite');

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where channel = 2 order by fav_count desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('channel_2'), '/channel/2');
*/

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where type = 4 order by seed_popularity desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('type_4'), '/type/4');

$videos = array();
$result = mysql_query("select title, sn, sn_normalized, channel, rating from video where type = 5 order by seed_popularity desc limit 8");
while (list($title, $sn, $snn, $channel, $rating) = mysql_fetch_array($result)) {
	$snn = snn_add_hyphen($snn);
	$video = array($title, $sn, $snn, $channel, $rating);
	$videos[] = $video;
}
$html .= output_group($videos, i18n('type_5'), '/type/5');

$target = '_blank';
$html_title = 'JAV321';
require_once('header.php');
echo $html;
require_once('footer.php');

function output_group($videos, $group_name, $url_prefix) {
	$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	if (isset($url_prefix)) 
		$html .= '<a href="'.$url_prefix.'/1">'.$group_name.'</a> &nbsp; <a href="'.$url_prefix.'/1">'.i18n('more').'</a>';
	else {
		$html .= $group_name;
	}
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	$column = 0;
	foreach ($videos as $video) {
		list($title, $sn, $snn, $channel, $rating) = $video;
		$url = "/video/$sn";
		if ($column % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\">".get_img_tag(get_cover_img_url($sn, $channel, $rating))."<br>$title $snn</a></div></div>";
		if ($column % 4 == 3) $html .= '</div></div>';
		++$column;
	}
	if (isset($url_prefix)) {
		$html .= '<div class="row"><div class="col-md-12"><p class="pull-right"><a href="'.$url_prefix.'/1">'.i18n('more').'</a></p></div></div>';
	}
	$html .= '</div></div></div></div>';
	return $html;
}

?>
