<?
require_once("init.php");
$db_conn = conn_ezptt_db();
require_once("i18n.php");
$type = (int)$_GET['type'];
$table = $type == 0 ? 'jandan_beauty' : 'jandan_funny';
if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
}
else {
	list($min_id, $max_id) = execute_vector("select min(id), max(id) from $table");
	$id = rand($min_id, $max_id);
}
$start_id = $id;
$result = mysql_query("select id, oo, xx, url, bd_kw1, bd_kw2 from $table where enabled = 1 and id >= $id order by id limit 11");
while(list($id, $oo, $xx, $url, $kw1, $kw2) = mysql_fetch_array($result)) {
	$articles[] = array($id, $oo, $xx, $url, $kw1, $kw2);
	if (!isset($html_title) && (strlen($kw1) > 0 || strlen($kw2) > 0)) {
		$html_title = i18n("$kw1 $kw2");
	}
}

$html .= '<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.0";
fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
for ($i = 0; $i < 10; ++$i) {
	if (!isset($articles[$i])) break;
	list($id, $oo, $xx, $url, $kw1, $kw2) = $articles[$i];
	$html .= '<div class="row"><div class="col-sm-5 col-sm-offset-3 col-xs-12"><div class="thumbnail">';
	$html .= "<a href=\"/jd/$type/$id\"><img data-original=\"$url\" class=\"img-responsive\" width=\"100%\" /></a>";
	$html .= '<div class="caption">';
	/*
	if (strlen($kw1) > 0 || strlen($kw2) > 0) {
		$html .= "<h3>".i18n("$kw1 $kw2")."</h3>";
	}
	*/
	$html .= "<br /><span class=\"pull-right\"><span class=\"glyphicon glyphicon-thumbs-up\"></span> $oo <span class=\"glyphicon glyphicon-thumbs-down\"></span> $xx</span>";
	$html .= '</div></div>';
	if ($i == 0) { 
		$html .= '<div class="addthis_sharing_toolbox"></div>';
		$html .= '<div class="fb-comments" data-href="http://www.ucptt.com/jd/'.$type.'/'.$id.'" data-numposts="10" data-colorscheme="light"></div>';
	}
	$html .= '</div></div>';
}
$html .= '<div class="row"><div class="col-md-5 col-md-offset-3 col-xs-12"><ul class="pager">';
$prev_id = execute_scalar("select id from $table where enabled = 1 and id < $start_id order by id desc limit 10, 1");
if (isset($prev_id)) {
	$html .= '<li class="previous"><a href="/jd/'.$type.'/'.$prev_id.'">上一页</a></li>';
}
if (isset($articles[10])) {
	$next_id = $articles[10][0];
	$html .= '<li class="next"><a href="/jd/'.$type.'/'.$next_id.'">下一页</a></li>';
}
$html .= '</ul></div></div>';
require_once('header.php');
echo $html;
require_once('footer.php');
?>
