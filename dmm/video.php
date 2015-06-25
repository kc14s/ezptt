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

$stars = execute_column("select star from star where sn = '$sn'");
$genres = execute_column("select genre from genre where sn = '$sn'");
$html_title = "$title $snn ".implode(' ', $stars).' bittorrent '.i18n('download');

$html = '<div class="row"><div class="col-md-6 col-md-offset-2 col-xs-12">';
//$html .= "<div class=\"row\"><div class=\"col-md-12\"><h3>$html_title</h3></div></div>";
$html .= '<div class="panel panel-info">';
$html .= "<div class=\"panel-heading\"><h3>$title <small>$snn ".implode(' ', $stars)."</small></h3></div>";
$html .= '<div class="panel-body">';
$html .= '<div class="row">';
$html .= '<div class="col-md-3"><img data-original="'.get_cover_img_url($sn, $channel).'" /></div>';
$html .= '<div class="col-md-9">';
if (count($stars) > 0) $html .= '<b>'.i18n('star').'</b>: '.implode(' ', $stars).'<br>';
if ($company != '') $html .= '<b>'.i18n('company')."</b>: $company<br>";
if (count($genres) > 0) $html .= '<b>'.i18n('genre').'</b>: '.implode(' ', $genres).'<br>';
$html .= '<b>'.i18n('sn')."</b>: $sn<br>";
$html .= '<b>'.i18n('release_date')."</b>: $release_date<br>";
$html .= '<b>'.i18n('fav_count')."</b>: $fav_count<br>";
$html .= '<b>'.i18n('rating')."</b>: <img data-original=\"/img/$rating.gif\" /><br>";
$html .= '</div>';	//end of md-9
$html .= '</div>';	//end of row
$html .= "<div class=\"row\"><div class=\"col-md-12\">$description</div></div>";
$html .= '</div>';	//end of panel body
$html .= '</div>';	//end of panel
$counter = 0;
if (count($stars) > 0) {
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.implode(', ', $stars).i18n('star_video').'</div>';
	$html .= '<div class="panel-body">';
	foreach ($stars as $star) {
		$sns = execute_column("select sn from star where star = '$star' limit 8");
		foreach ($sns as $star_sn) {
			if ($star_sn == $sn) continue;
			list($star_title, $star_channel) = execute_vector("select title, channel from video where sn = '$star_sn' limit 8");
//			$short_title = !$is_spider && mb_strlen($star_title) > 20 ? mb_substr($star_title, 0, 20, 'utf-8') : $star_title;
			$short_title = $star_title;
			if ($counter % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
			$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$star_sn\"><img data-original=\"".get_cover_img_url($star_sn, $channel)."\"><br>$short_title</a></div></div>";
			if ($counter % 4 == 3) $html .= '</div></div>';
			++$counter;
		}
	}
	if ($counter % 4 != 0) $html .= '</div></div>';
	$html .= '</div></div>';
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
$html .= get_rand_dmm_topic_html();
$html .= '</div>';	#left column end
$html .= '<div class="col-md-2">';	//thumb
if ($channel != 2) {
	$img_url = get_cover_img_large_url($sn, $channel);
	$href = $img_url;
	$href = "/snapshot/$sn/$channel/0";
	if ($is_spider) $href = '#';
	$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'"><img width="300" data-original="'.$img_url.'" /></a></p></div>';
}
for ($i = 1; $i <= $sample_image_num; ++$i) {
	$img_url = get_sample_img_url($sn, $channel, $i);
	$href = $img_url;
	$href = "/snapshot/$sn/$channel/$i";
	if ($is_spider) $href = '#';
	$html .= '<div class="col-xs-12 col-md-12"><p><a href="'.$href.'"><img width="300" data-original="'.$img_url.'" /></a></p></div>';
}
$html .= '</div></div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>

