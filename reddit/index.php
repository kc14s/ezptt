<?
require_once('init.php');
require_once('reddit_lib.php');
$page = 1;
$db_conn = conn_reddit_db();
if (isset($_GET['page'])) {
	$page = $_GET['page'];
}
$result = mysql_query("select subreddit, title, ups, url, selftext, id from topic where good = 1 order by created desc limit ".(($page - 1) * $page_size).", $page_size");
while (list($subreddit, $title, $ups, $url, $selftext, $id) = mysql_fetch_array($result)) {
	$article = array($subreddit, $title, $ups, $url, $selftext, $id);
	list($id, $author, $ups, $body) = execute_vector("select id, author, ups, body from reply where tid = $id order by ups desc limit 1");
	if ($ups > 10) {
		array_push($article, $id, $author, $body);
	}
	$articles[] = $article;
}

$html = '';
foreach ($articles as $article) {
	list($subreddit, $title, $ups, $url, $selftext, $tid, $rid, $rauthor, $body) = $article;
	$img_url = get_img_url($url, 1);
	if (!isset($img_url)) continue;
	$thread_url = "/reddit/$subreddit/$tid/".str_to_url($title);
	$html .= '<div class="row"><div class="col-sm-5 col-sm-offset-3 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">';
	$html .= "[$subreddit] <a href=\"$thread_url\">$title</a>";
	$html .= '</div>';
	$html .= '<div class="panel-body">';
	$html .= "<p><span class=\"glyphicon glyphicon-thumbs-up\"></span> $ups";
	$html .= "$selftext</p>";
	$html .= "<a href=\"$thread_url\"><img data-original=\"$img_url\" class=\"img-responsive\" /></a>";
	if (isset($rid)) {
		$html .= "<p>$rauthor: $body</p>";
	}
	$html .= '</div></div></div></div>';
}

$html .= '<div class="row"><div class="col-sm-5 col-sm-offset-3 col-xs-11"><ul class="pager">';
if ($page == '1') {
	$html .= '<li class="previous disabled"><a href="#">&larr; Newer</a></li>';
}
else {
	$html .= '<li class="previous"><a href="/hot/'.($page - 1).'" target="_self">&larr; Newer</a></li>';
}
$html .= '<li class="next"><a href="/hot/'.($page + 1).'" target="_self">Older &rarr;</a></li>';
$html .= '</ul></div></div>';

$target = '_blank';
require_once('header.php');
echo $html;
require_once('footer.php');

?>
