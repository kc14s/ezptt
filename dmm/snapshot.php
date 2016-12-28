<?
require_once("init.php");
require_once("dmm_lib.php");
require_once("i18n.php");

$sn = $_GET['sn'];
$channel = $_GET['channel'];
$index = $_GET['index'];
$sn = $_GET['sn'];
$db_conn = conn_dmm_db();
list($title, $release_date, $runtime, $director, $series, $company, $fav_count, $rating, $sample_image_num, $description, $channel, $snn) = execute_vector("select title, release_date, runtime, director, series, company, fav_count, rating, sample_image_num, description, channel, sn_normalized from video where sn = '$sn'");
if ($channel <= 4 || $channel == 8 || $channel == 9) {
	$stars = execute_column("select star from star where sn = '$sn'");
}
else if ($channel <= 7) {
	$stars = execute_column("select star from ave_sn_star where sn = '$sn'");
}
else if ($channel == 10) {
	$stars = execute_column("select name from 1pondo_sn_star, 1pondo_star_info where sn = '$sn' and id = star_id");
}
$html_title = "$title $snn ".implode(' ', $stars);
if ($index == 0) {
	$img_url = get_cover_img_large_url($sn, $channel, $rating);
}
else {
	$img_url = get_sample_img_url($sn, $channel, $index, $rating);
}
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-10">';
$html .= '<div class="panel panel-info">';
$html .= "<div class=\"panel-heading\"><h3><a href=\"/video/$sn\">$title <small>$snn ".implode(' ', $stars)."</small></a></h3></div>";
$html .= '<div class="panel-body">';
$html .= '<div class="row">';
if (!$is_spider) {
	$html .= $trafficjunky_jav_950_250;
}
if ((($channel <= 4 || $channel == 8 || $channel == 9 || $channel == 10) && $index < $sample_image_num)
|| ($channel <= 7 && $index < 1)) {
	$html .= '<div class="col-md-12"><a href="/snapshot/'."$sn/$channel/".($index + 1).'"><img class="img-responsive" data-original="'.$img_url.'" /></a></div>';
}
else {
	$html .= '<div class="col-md-12"><img class="img-responsive" data-original="'.$img_url.'" /></div>';
}
if (!$is_spider) {
	$html .= $trafficjunky_jav_950_250;
}
//$html .= $juicyads_jav321_728_90;
$html .= '</div></div></div></div></div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>
