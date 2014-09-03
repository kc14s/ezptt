<?
require_once("init.php");
require_once("ads_lib.php");

$word_id = from_external_id($_GET['id']);
$db_conn = conn_ads_db();
$word = execute_scalar("select word from word where word_id = $word_id");
$html_title = $word;
$result = mysql_query("select website, charge, user.user_id user_id from user, consumption where word_id = $word_id and user.user_id = consumption.user_id order by charge desc");
$charge_total = 0;
while($row = mysql_fetch_array($result)) {
	$consumptions[] = $row;
	$charge_total += $row[1];
}

$html = '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10"><nav class="navbar navbar-default navbar-static-top" role="navigation">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="/">Ads Analysis</a>
</div>
<div class="collapse navbar-collapse">
<form class="navbar-form navbar-left" role="search" action="/search" method="POST">
<div class="form-group">
<input type="text" class="form-control" name="query" size="60" value="'.$word.'">
</div>
<div class="form-group"><select class="form-control" name="type">
<option value="1" selected="true">广告词查询</option>
<option value="2">广告主查询</option>
</select></div>
<button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</nav>
</div>
</div>';
$html .= '<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10">';
if (count($consumptions) > 0) {
	$html .= '<div class="center-block"><h2><b>'.$word.'</b> 广告投放数据</h2></div>';
	$html .= '<table class="table table-hover table-striped"><tr><th>#</th><th>广告词</th><th>广告主</th><th>比例</th></tr>';
	for ($i = 1; $i <= count($consumptions); ++$i) {
		list($website, $charge, $user_id) = $consumptions[$i - 1];
		$percentage = get_percentage($charge / $charge_total);
		$user_id = to_external_id($user_id);
		$html .= "<tr><td>$i</td><td>$word</td><td><a href=\"/advertiser/$user_id\">$website</a></td><td>$percentage</td></tr>";
	}
	$html .= '</table>';
}
else {
	$html .= '<div class="alert alert-danger" role="alert">抱歉，没找到这个广告词的投放信息。</div>';
}
$html .= '</div></div>';	//row
require_once('header.php');
echo $html;
require_once('footer.php');
?>

