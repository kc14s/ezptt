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
$snn = snn_add_hyphen($snn);
$stars = execute_column("select star from star where sn = '$sn'");
$html_title = "$title $snn ".implode(' ', $stars);
if ($index == 0) {
	$img_url = get_cover_img_large_url($sn, $channel);
}
else {
	$img_url = get_sample_img_url($sn, $channel, $index);
}
$html = '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-10">';
$html .= '<div class="panel panel-info">';
$html .= "<div class=\"panel-heading\"><h3><a href=\"/video/$sn\">$title <small>$snn ".implode(' ', $stars)."</small></a></h3></div>";
$html .= '<div class="panel-body">';
$html .= '<div class="row">';
if ($index < $sample_image_num) {
	$html .= '<div class="col-md-12"><a href="/snapshot/'."$sn/$channel/".($index + 1).'"><img data-original="'.$img_url.'" /></a></div>';
}
else {
	$html .= '<div class="col-md-12"><img data-original="'.$img_url.'" /></div>';
}
$html .= '</div></div></div></div></div>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>
