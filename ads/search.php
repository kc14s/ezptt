<?
require_once("init.php");
require_once("ads_lib.php");

$query = $_POST['query'];
$db_conn = conn_ads_db();
$query = mysql_real_escape_string($query);
$type = (int)$_POST['type'];
if ($type === 1) {
	$word_id = execute_scalar("select word_id from word where word = '$query'");
	if (isset($word_id)) {
		$word_id = to_external_id($word_id);
		Header( "HTTP/1.1 301 Found");
		header("Location: /word/$word_id");
	}
	else {
		$result_html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10"><div class="alert alert-warning" role="alert">抱歉，没有找到此广告词的投放记录。</div></div></div>';
	}
}
else if ($type === 2) {
	$domain = get_domain($query);
	$users = execute_dataset("select user_id, website from user where domain = '$domain'");
	if (count($users) > 0) {
		$result_html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10"><div class="list-group">';
		foreach ($users as $user) {
			list($user_id, $website) = $user;
			$user_id = to_external_id($user_id);
			$result_html .= "<a href=\"/advertiser/$user_id\" class=\"list-group-item\">$website</a>";
		}
		$result_html .= '</div></div></div>';
	}
	else {
		$result_html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10"><div class="alert alert-warning" role="alert">抱歉，没有找到此广告主的投放记录。</div></div></div>';
	}
}

$html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10"><nav class="navbar navbar-default navbar-static-top" role="navigation">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="/">Ads Analysis</a>
</div>
<div class="collapse navbar-collapse">
<form class="navbar-form navbar-left" role="search" action="/search" method="POST">
<div class="form-group">
<input type="text" class="form-control" name="query" size="60" value="'.$query.'">
</div>
<div class="form-group"><select class="form-control" name="type">
<option value="1"'.($type == 1 ? ' selected="true"' : '').'>广告词查询</option>
<option value="2"'.($type == 2 ? ' selected="true"' : '').'>广告主查询</option>
</select></div>
<button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</nav>
</div>
</div>';
$html .= $result_html;
require_once('header.php');
echo $html;
require_once('footer.php');
?>

