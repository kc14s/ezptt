<?
require_once('init.php');
require_once('zhihu_lib.php');
$page = 1;
$db_conn = conn_db();
mysql_select_db('zhihu', $db_conn);
mysql_query('set names utf8');

$condition = '';
if (isset($_GET['before'])) {
	$ts = $_GET['before'];
	$condition = ' and pub_time < "'.date('Y-m-d H:i:s', $ts).'"';
}
else if (isset($_GET['after'])) {
	$ts = $_GET['after'];
	$condition = ' and pub_time > "'.date('Y-m-d H:i:s', $ts).'"';
}
$result = mysql_query("select name, title, aid, ups, author, nick, answer.content, pub_time from sub_board, question, answer where sub_board.sbid = question.sbid and question.qid = answer.qid and good = 1 $condition order by pub_time desc limit $page_size");
//echo "select name, title, aid, ups, author, nick, answer.content, pub_time from sub_board, question, answer where sub_board.sbid = question.sbid and question.qid = answer.qid and good = 1 $condition order by pub_time desc limit $page_size";
while (list($board_name, $title, $aid, $ups, $author, $nick, $content, $pub_time) = mysql_fetch_array($result)) {
	$article = array($board_name, $title, $aid, $ups, $author, $nick, $content, $pub_time);
	list($comment_author, $comment_ups, $comment_content) = execute_vector("select author, ups, content from comment where aid = $aid order by ups desc limit 1");
	if ($comment_ups * 2 > $ups) {
		array_push($article, $comment_author, $comment_ups, $comment_content);
	}
	$articles[] = $article;
}

$html = '';
list($pubtime_min, $pubtime_max) = array(0, 0);
foreach ($articles as $article) {
	list($board_name, $title, $aid, $ups, $author, $nick, $content, $pub_time, $comment_author, $comment_ups, $comment_content) = $article;
	if (strpos($content, '<img') > 0) continue;
	$answer_url = "/answer/$aid";
	$html .= '<div class="row"><div class="col-sm-5 col-sm-offset-3 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= "[$board_name] <a href=\"$answer_url\">$title</a>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	if ($author == '') $author = '知乎用户';
	$html .= "<strong>$author</strong>";
	//if ($nick != '') $html .= " ($nick)";
	$html .= " &nbsp; &nbsp; <span class=\"glyphicon glyphicon-thumbs-up\"></span> $ups";
	$html .= "<span class=\"pull-right\">$pub_time</span><br>";
	$html .= "<p>".process_answer_content($content)."</p>";
	if (isset($comment_author)) {
		$html .= "<p>$comment_author: $comment_content</p>";
	}
	$html .= '</div></div></div></div>';
	$ts = strtotime($pub_time);
	if ($pubtime_min == 0 || $pubtime_min > $ts) $pubtime_min = $ts;
	if ($pubtime_max == 0 || $pubtime_max < $ts) $pubtime_max = $ts;
}

//*
$html .= '<div class="row"><div class="col-sm-5 col-sm-offset-3 col-xs-12"><ul class="pager">';
if (!isset($_GET['before']) && !isset($_GET['before'])) {
	$html .= '<li class="previous disabled"><a href="#">&larr; Newer</a></li>';
}
else {
	$html .= '<li class="previous"><a href="/safter/'.$pubtime_max.'" target="_self">&larr; Newer</a></li>';
}
$html .= '<li class="next"><a href="/sbefore/'.$pubtime_min.'" target="_self">Older &rarr;</a></li>';
$html .= '</ul></div></div>';
//*/

$target = '_blank';
require_once('header.php');
echo $html;
require_once('footer.php');

?>
