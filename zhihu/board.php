<?
require_once("init.php");
$is_spider = is_spider();
$is_from_search_engine = is_from_search_engine();

$bid = $_GET['bid'];
$target = '_blank';
$db_conn = conn_db();
mysql_select_db('zhihu', $db_conn);
mysql_query('set names utf8');
$order_by = 'desc';
$condition = '';
if (isset($_GET['before'])) {
	$order_by = 'desc';
	$condition = 'and pub_time < "'.date('Y-m-d H:i:s', $_GET['before']).'"';
}
else if (isset($_GET['after'])){
	$order_by = 'asc';
	$condition = 'and pub_time > "'.date('Y-m-d H:i:s', $_GET['after']).'"';
}
if ($bid < 65536) {
	$answers = execute_dataset("select aid, ups, author, qid, pub_time from answer force index(index_bid_pubtime) where answer.bid = $bid and ups > 0 $condition order by pub_time $order_by limit 40");
}
else {
	$answers = execute_dataset("select aid, ups, author, qid, pub_time from answer where answer.sbid = $bid and ups > 0 $condition order by pub_time $order_by limit 40");
}
$clustered_answers = array();
foreach ($answers as $answer) {
	list($aid, $ups, $author, $qid, $pub_time) = $answer;
	if (isset($clustered_answers[$qid])) {
		if ($clustered_answers[$qid][1] < $ups) {
			$clustered_answers[$qid] = array($aid, $ups, $author, $qid, $pub_time);
		}
	}
	else {
		$clustered_answers[$qid] = array($aid, $ups, $author, $qid, $pub_time);
	}
}
if ($bid < 65536) {
	$bname = execute_scalar("select name from board where bid = $bid");
}
else {
	$bname = execute_scalar("select name from sub_board where sbid = $bid");
}
$html_title = "$bname";

$html = "<div class=\"col-md-8 col-md-offset-2 col-xs-12\"><ol class=\"breadcrumb\"><li><a href=\"/\">知乎</a></li><li><a href=\"/topic/$bid\">$bname</a></li>";
$html .= "</ol><h3>$bname</h3>";

if (!$is_loyal_user) {
//	$html .= $google_320_100;
//	$html .= $chitika_468_60;
//	$html .= $bloggerads_banner;
//	$html .= $scupio_728_90;
//	$html .= $adcash_popunder;
}
$html .= '<div class="list-group">';
list($pubtime_min, $pubtime_max) = array(0, 0);
foreach ($clustered_answers as $answer) {
//foreach ($answers as $answer) {
	list($aid, $ups, $author, $qid, $pub_time) = $answer;
	$title = execute_scalar("select title from question where qid = $qid");
	$html .="<a class=\"list-group-item\" href=\"/answer/$aid\"><span class=\"glyphicon glyphicon-thumbs-up\"></span> $ups $title<span class=\"pull-right\">$author</span>";
	$ts = strtotime($pub_time);
	if ($pubtime_min == 0 || $pubtime_min > $ts) $pubtime_min = $ts;
	if ($pubtime_max == 0 || $pubtime_max < $ts) $pubtime_max = $ts;
}
$html .= '</div></div>';

$html .= '<div class="row"><div class="col-sm-6 col-sm-offset-3 col-xs-12"><ul class="pager">';
if (!isset($_GET['before']) && !isset($_GET['before'])) {
	$html .= '<li class="previous disabled"><a href="#">&larr; Newer</a></li>';
}
else {  
	$html .= '<li class="previous"><a href="/topic/'.$bid.'/after/'.$pubtime_max.'" target="_self">&larr; Newer</a></li>';
}
$html .= '<li class="next"><a href="/topic/'.$bid.'/before/'.$pubtime_min.'" target="_self">Older &rarr;</a></li>';
$html .= '</ul></div></div>';
/*
if (isset($prev_topics)) {
		$html .= '<div class="panel panel-default"><div class="panel-heading">'.i18n('jixuyuedu').'</div>';
		$html .= '<div class="list-group">';
		foreach ($prev_topics as $topic) {
				list($title, $tid1, $tid2) = $topic;
				$html .= "<a href=\"/article/$en_name/$tid1/$tid2\" class=\"list-group-item\">".i18n($title)."</a>";
		}
		$html .= '</div></div>';
}
if (false || $is_spider) {
	$html .= get_rand_zhihu_topic_html();
}
if (true || $is_spider) {
#	$html .= get_old_ck101_topic_html();
}
*/
//$html .= '<p><a href="/">PTT</a> <a href="/disp">disp</a></p></div>';
//$html .= '<script type="text/javascript">var zx_aid = 1;var zx_uid = 10799;var zoneid = 11554;</script><script type="text/javascript" charset="utf-8" src="http://click.9cpc.com/view.js"></script>';

require_once('header.php');
echo $html;
require_once('footer.php');
?>

