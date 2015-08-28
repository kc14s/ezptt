<?
require_once('init.php');
require_once('zhihu_lib.php');
$page = 1;
$db_conn = conn_db();
mysql_select_db('zhihu', $db_conn);
mysql_query('set names utf8');
#set_loyal_user();

$condition = '';
if (isset($_GET['before'])) {
	$ts = $_GET['before'];
	$condition = 'and pub_time < "'.date('Y-m-d H:i:s', $ts).'" order by pub_time desc';
}
else if (isset($_GET['after'])) {
	$ts = $_GET['after'];
	$condition = 'and pub_time > "'.date('Y-m-d H:i:s', $ts).'" order by pub_time';
}
else {
	$condition = 'order by pub_time desc';
}
$type = 'good';
if (isset($_GET['hot']) && $_GET['hot'] == 1) $type = 'hot';
else if (isset($_GET['reply']) && $_GET['reply'] == 1) $type = 'reply';
else if (isset($_GET['pic']) && $_GET['pic'] == 1) $type = 'pic';
$result = mysql_query("select aid, ups, author, nick, answer.content, pub_time, qid from answer where $type = 1 $condition limit $page_size");
while (list($aid, $ups, $author, $nick, $content, $pub_time, $qid) = mysql_fetch_array($result)) {
	list($title, $bid, $sbid) = execute_vector("select title, bid, sbid from question where qid = $qid");
	if (!isset($title) || $title == '') continue;
	$board_name = execute_scalar("select name from board where bid = $bid");
	$sub_board_name = execute_scalar("select name from sub_board where sbid = $sbid");
	$article = array($bid, $sbid, $board_name, $sub_board_name, $title, $aid, $ups, $author, $nick, $content, $pub_time);
	if ($type == 'reply') {
		list($comment_author, $comment_ups, $comment_pub_date, $comment_content) = execute_vector("select author, ups, pub_date, content from comment where aid = $aid order by ups desc limit 1");
		if (true || $comment_ups * 3 > $ups) {
			array_push($article, $comment_author, $comment_ups, $comment_pub_date, $comment_content);
		}
	}
	$articles[] = $article;
}

$html = '<div class="row"><div class="col-md-6 col-md-offset-3 col-xs-12">';
list($pubtime_min, $pubtime_max) = array(0, 0);
foreach ($articles as $article) {
	list($bid, $sbid, $board_name, $sub_board_name, $title, $aid, $ups, $author, $nick, $content, $pub_time, $comment_author, $comment_ups, $comment_pub_date, $comment_content) = $article;
#	if (strpos($content, '<img') > 0) continue;
	$answer_url = "/answer/$aid";
//	$html .= '<div class="row"><div class="col-sm-6 col-sm-offset-3 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= "[<a href=\"/topic/$bid\">$board_name</a>/<a href=\"/topic/$sbid\">$sub_board_name</a>] <a href=\"$answer_url\">$title</a>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	if ($author == '') $author = '知乎用户';
	$html .= "<strong>$author</strong>";
	//if ($nick != '') $html .= " ($nick)";
	$html .= " &nbsp; &nbsp; <span class=\"glyphicon glyphicon-thumbs-up\"></span> $ups";
	$html .= "<span class=\"pull-right\">$pub_time</span><br>";
	$html .= process_answer_content($content, $aid);
	if (isset($comment_author)) {
		if ($comment_author == '') $comment_author = '知乎用户';
		$html .= "<hr><strong>$comment_author</strong>";
		$html .= " &nbsp; &nbsp; <span class=\"glyphicon glyphicon-thumbs-up\"></span> $comment_ups";
		$html .= "<span class=\"pull-right\">$comment_pub_date</span><br>";
		$html .="<p>$comment_content</p>";
	}
//	$html .= '</div></div></div></div>';
	$html .= '</div></div>';
	$ts = strtotime($pub_time);
	if ($pubtime_min == 0 || $pubtime_min > $ts) $pubtime_min = $ts;
	if ($pubtime_max == 0 || $pubtime_max < $ts) $pubtime_max = $ts;
}
$html .= '</div></div>';

//*
$char = 's';
if ($type == 'hot') $char = 'h';
else if ($type == 'reply') $char = 'r';
else if ($type == 'pic') $char = 'p';
$html .= '<div class="row"><div class="col-sm-6 col-sm-offset-3 col-xs-12"><ul class="pager">';
if (!isset($_GET['before']) && !isset($_GET['before'])) {
	$html .= '<li class="previous disabled"><a href="#">&larr; 上一页</a></li>';
}
else {
	$html .= '<li class="previous"><a href="/'.$char.'after/'.$pubtime_max.'" target="_self">&larr; 上一页</a></li>';
}
$html .= '<li class="next"><a href="/'.$char.'before/'.$pubtime_min.'" target="_self">下一页 &rarr;</a></li>';
$html .= ' <li class="next"><a href="/random/'.$type.'" target="_self">随机</a></li>';
$html .= '</ul></div></div>';
//*/

$target = '_blank';
require_once('header.php');
echo $html;
require_once('footer.php');

?>
