<?
require_once("init.php");
require_once("ads_lib.php");

$user_id = from_external_id($_GET['id']);
$db_conn = conn_ads_db();
$website = execute_scalar("select website from user where user_id = $user_id");
$html_title = $website;
$user_word_consumptions = array();
list($word_num, $charge_total) = execute_vector("select count(*), sum(cash) from consumption where user_id = $user_id");
$result = mysql_query("select word, consumption.cash cash, format(consumption.cash / consumption.click, 2) acp, word.word_id word_id, format(word.cash / word.click, 2) global_acp from word, consumption where user_id = $user_id and word.word_id = consumption.word_id order by consumption.cash desc limit 5");
while(list($word, $cash, $acp, $word_id, $global_acp) = mysql_fetch_array($result)) {
	$consumptions[] = array($word, $cash, $acp, $word_id, $global_acp, get_percentage($cash / $charge_total));
	$word_ids[] = $word_id;
	$top_word_consumptions[$word] = $cash;
	$user_word_consumptions[$website.' '.$word] = $cash;
}

$competitors = execute_dataset("select user.user_id, website, sum(cash) charge_total from user, consumption where word_id in (".join(',', $word_ids).") and user.user_id = consumption.user_id and user.user_id <> $user_id group by user.user_id order by charge_total desc limit 3");
foreach ($competitors as $competitor) {
	list($competitor_id, $competitor_website, $competitor_top_word_charge) = $competitor;
	$competitor_websites[] = $competitor_website;
	$competitor_consumptions = execute_dataset("select word, consumption.cash from word, consumption where user_id = $competitor_id and word.word_id = consumption.word_id order by consumption.cash desc limit 5");
	foreach ($competitor_consumptions as $competitor_consumption) {
		list($word, $charge) = $competitor_consumption;
		$top_word_consumptions[$word] += $charge;
		$user_word_consumptions[$competitor_website.' '.$word] = $charge;
	}
}
arsort($top_word_consumptions);
$top_words = array_keys($top_word_consumptions);
array_unshift($competitor_websites, $website);

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
	$html .= '<table class="table table-hover table-striped"><tr><th>#</th><th>广告词</th><th>广告主</th><th>月消费额估算（元）</th><th>点击单价（元）</th><th>平均点击单价（元）</th></tr>';
	for ($i = 1; $i <= count($consumptions); ++$i) {
		if (false && $i == 1) {
			$html .= "<tr><td>$i</td><td>***</td><td>$website</td><td>*</td></tr>";
		}
		else {
			list($word, $cash, $acp, $word_id, $global_acp, $percentage) = $consumptions[$i - 1];
			$word_id = to_external_id($word_id);
			$charge_estimated = valid_digit_1($cash);
			$charge_estimated = $cash;
			$html .= "<tr><td>$i</td><td><a href=\"/word/$word_id\">$word</a></td><td>$website</td><td>$charge_estimated</td><td>$acp</td><td>$global_acp</td></tr>";
		}
	}
	$html .= "<tr><td colspan=\"6\">监测到${website}共投放了${word_num}个广告词，估算消费额■■■■■■■■■元。查看完整报表。</td></tr>";
	$html .= '</table>';
	$html .= '<p>与同类广告主的比较</p>';
	$html .= '<table class="table table-hover table-striped"><tr><th>#</th><th>广告词</th>';
	foreach ($competitor_websites as $competitor_website) {
		$html .= "<th>$competitor_website</th>";
	}
	$html .= '</tr>';
	for ($i = 1; $i <= count($top_words); ++$i) {
		$word = $top_words[$i - 1];
		$html .= "<tr><td>$i</td><td>$word</td>";
		foreach ($competitor_websites as $competitor_website) {
			$html .= '<td>'.$user_word_consumptions[$competitor_website.' '.$word].'</td>';
		}
		$html .= '</tr>';
	}
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

