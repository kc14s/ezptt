<?
require_once("init.php");
require_once("ads_lib.php");

$user_id = from_external_id($_GET['id']);
$db_conn = conn_ads_db();
$website = execute_scalar("select website from user where user_id = $user_id");
$html_title = $website;
list($word_num, $charge_total) = execute_vector("select count(*), sum(charge) from consumption where user_id = $user_id");
$result = mysql_query("select word, charge, word.word_id word_id from word, consumption where user_id = $user_id and word.word_id = consumption.word_id order by charge desc limit 5");
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
<option value="1">广告词查询</option>
<option value="2" selected="true">广告主查询</option>
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
	$html .= '<div class="center-block"><h2><b>'.$website.'</b> 广告投放数据</h2></div>';
	$html .= '<table class="table table-hover table-striped"><tr><th>#</th><th>广告词</th><th>广告主</th><th>费用比例</th></tr>';
	for ($i = 1; $i <= count($consumptions); ++$i) {
		list($word, $charge, $word_id) = $consumptions[$i - 1];
		$percentage = get_percentage($charge / $charge_total);
		$word_id = to_external_id($word_id);
		$html .= "<tr><td>$i</td><td><a href=\"/word/$word_id\">$word</a></td><td>$website</td><td>$percentage</td></tr>";
	}
	$html .= "<tr><td colspan=\"4\">监测到${website}共投放了${word_num}个广告词，估算消费额■■■■■■■■■元。查看完整报表。</td></tr>";
	$html .= '</table>';
}
else {
	$html .= '<div class="alert alert-danger" role="alert">抱歉，没找到这个广告主的投放信息。</div>';
}
$html .= '</div></div>';	//row
require_once('header.php');
echo $html;
require_once('footer.php');
?>

