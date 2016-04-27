<?
require_once("init.php");
require_once("dmm_lib.php");
require_once("i18n.php");
require_once("ads.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();

$sn = $_GET['sn'];
$db_conn = conn_dmm_db();
list($title, $release_date, $runtime, $director, $series, $company, $fav_count, $rating, $sample_image_num, $description, $channel, $snn) = execute_vector("select title, release_date, runtime, director, series, company, fav_count, rating, sample_image_num, description, channel, sn_normalized from video where sn = '$sn'");
$snn = snn_add_hyphen($snn);

$stars = execute_column("select star from star where sn = '$sn'");
$star_infos = execute_dataset("select star_info.id, star_info.name, star_info.pic_name from star_info, star where sn = '$sn' and star_info.name = star.star");
$genres = execute_column("select genre from genre where sn = '$sn'");
$html_title = "$title $snn ".implode(' ', $stars).' bittorrent '.i18n('download');

$html = '<div class="row"><div class="col-md-7 col-md-offset-1 col-xs-12">';
//$html .= "<div class=\"row\"><div class=\"col-md-12\"><h3>$html_title</h3></div></div>";
$html .= '<div class="panel panel-info">';
$html .= "<div class=\"panel-heading\"><h3>$title <small>$snn ".implode(' ', $stars)."</small></h3></div>";
$html .= '<div class="panel-body">';
$html .= '<div class="row">';
$html .= '<div class="col-md-3"><img data-original="'.get_cover_img_url($sn, $channel).'" /></div>';
$html .= '<div class="col-md-9">';
if (count($star_infos) > 0) {
	$html .= '<b>'.i18n('star').'</b>: ';
	foreach ($star_infos as $star_info) {
		list($star_id, $star_name, $star_pic_name) = $star_info;
		$html .= "<a href=\"/star/$star_id/1\">$star_name</a> &nbsp; ";
	}
	$html .= '<br>';
}
if ($company != '') $html .= '<b>'.i18n('company')."</b>: $company<br>";
if (count($genres) > 0) $html .= '<b>'.i18n('genre').'</b>: '.implode(' ', $genres).'<br>';
$html .= '<b>'.i18n('sn')."</b>: $sn<br>";
$html .= '<b>'.i18n('release_date')."</b>: $release_date<br>";
$html .= '<b>'.i18n('fav_count')."</b>: $fav_count<br>";
$html .= '<b>'.i18n('rating')."</b>: <img data-original=\"/img/$rating.gif\" /><br>";
$html .= '</div>';	//end of md-9
$html .= '</div>';	//end of row
if ($sample_image_num >= 1) {
	$html .= "<div class=\"row\"><div class=\"col-md-12\">";
	$img_url = get_sample_img_url($sn, $channel, 1);
	$href = "/snapshot/$sn/$channel/1";
	$html .= "<a href=\"$href\"><img width=\"100%\" data-original=\"$img_url\"></a>";
	$html .= '</div></div>';
}
$html .= "<div class=\"row\"><div class=\"col-md-12\">$description</div></div>";
$html .= '</div>';	//end of panel body
$html .= '</div>';	//end of panel
if (count($star_infos) > 0) {
	foreach ($star_infos as $star_info) {
		list($star_id, $star_name, $star_pic_name) = $star_info;
		$html .= '<div class="panel panel-info">';
		$html .= '<div class="panel-heading">'.$star_name.i18n('star_video').'</div>';
		$html .= '<div class="panel-body">';
		$counter = 1;
		$html .= '<div class="row"><div class="col-md-12">';
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/star/$star_id/1\"><img data-original=\"http://pics.dmm.co.jp/mono/actjpgs/$star_pic_name.jpg\"><br>$star_name".i18n('star_all_video')."</a></div></div>";
		$sns = execute_column("select sn from star where star = '$star_name' limit 8");
		$star_video_output_count = 0;
		foreach ($sns as $star_sn) {
			if ($star_sn == $sn) continue;
			if ($star_video_output_count == 7) break;
			++$star_video_output_count;
			list($star_title, $star_channel) = execute_vector("select title, channel from video where sn = '$star_sn'");
//			$short_title = !$is_spider && mb_strlen($star_title) > 20 ? mb_substr($star_title, 0, 20, 'utf-8') : $star_title;
			$short_title = $star_title;
			if ($counter % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
			$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$star_sn\"><img data-original=\"".get_cover_img_url($star_sn, $star_channel)."\"><br>$short_title</a></div></div>";
			if ($counter % 4 == 3) $html .= '</div></div>';
			++$counter;
		}
		if ($counter % 4 != 0) $html .= '</div></div>';
		$html .= '</div></div>';
	}
}

$seed_set = execute_dataset("select name, size_text, file_num, created, hot, seed_url, magnet from seed where sn = '$sn'");
if (count($seed_set) > 0) {
	$html .= '<div class="panel panel-info"><table class="table table-striped">';
	$html .= '<tr><th>'.i18n('seed_name').'</th><th>'.i18n('seed_size').'</th><th>'.i18n('seed_file_num').'</th><th>'.i18n('seed_created').'</th><th>'.i18n('seed_popularity').'</th><th>'.i18n('seed_torrent').'</th><th>'.i18n('seed_magnet').'</th></tr>';
	foreach ($seed_set as $seed_row) {
		$html .= '<tr><td>'.$seed_row['name'].'</td><td>'.$seed_row['size_text'].'</td><td>'.$seed_row['file_num'].'</td><td>'.$seed_row['created'].'</td><td>'.$seed_row['hot'].'</td><td><a href="'.$seed_row['seed_url'].'" target="_blank">'.i18n('seed_download_bt').'</a></td><td><a href="'.$seed_row['magnet'].'">'.i18n('seed_download_magnet').'</a></td></tr>';
	}
	$html .= '</table></div>';
}

if ($lang_short == 'zh') {
//	$html .= $ueads_av_pic;
	$html .= get_ck101_board_random_topic(70);
}
if ($is_spider) {
	$html .= get_rand_dmm_topic_html();
}
$html .= get_rand_dmm_thumb_html();
$html .= '<div id="uyan_frame"></div><script type="text/javascript" src="http://v2.uyan.cc/code/uyan.js?uid=2084908"></script>';
$html .= '</div>';	#left column end
$html .= '<div class="col-md-3">';	//thumb
if ($channel != 2) {
	$img_url = get_cover_img_large_url($sn, $channel);
	$href = $img_url;
	$href = "/snapshot/$sn/$channel/0";
	if ($is_spider) $href = '#';
	$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'"><img width="100%" src="'.$img_url.'" /></a></p></div>';
}
for ($i = 2; $i <= $sample_image_num; ++$i) {
	$img_url = get_sample_img_url($sn, $channel, $i);
	//$href = $img_url;
	$href = "/snapshot/$sn/$channel/$i";
	if ($is_spider) $href = '#';
	$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'"><img width="100%" src="'.$img_url.'" /></a></p></div>';
}
$html .= '</div></div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>

