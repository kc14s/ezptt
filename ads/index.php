<? require_once('header.php');
require_once("init.php");
require_once("ads_lib.php");

$db_conn = conn_ads_db();
$rand = rand(0, 1000);
$words = execute_dataset("select word_id, word from word limit $rand, 5");
$users = execute_dataset("select user_id, website from user limit $rand, 5");
?>
<p>&nbsp; </p>
<p>&nbsp; </p>
<p>&nbsp; </p>
<p>&nbsp; </p>

<h1 class="text-center">Ads Analysis</h1>
<p>&nbsp; </p>
<div class="row"><div class="col-md-8 col-md-offset-3 col-xs-10">
<form class="form-inline" role="form" action="/search" method="POST">
<div class="form-group" width="100"><input class="form-control" name="query" size="60"></div>
<div class="form-group"><select class="form-control" name="type">
<option value="1" selected="true">广告词查询</option>
<option value="2">广告主查询</option>
</select></div>
<button type="submit" class="btn btn-default">查询</button>
</form>
</div></div>
<p> </p>
<div class="row"><div class="col-md-8 col-md-offset-3 col-xs-10"><p>热门广告词：
<?
foreach ($words as $word_info) {
	list($word_id, $word) = $word_info;
	$word_id = to_external_id($word_id);
	echo "<a href=\"/word/$word_id\">$word</a> &nbsp; ";
}
?>
</p></div></div>
<div class="row"><div class="col-md-8 col-md-offset-3 col-xs-10"><p>热门广告主：
<?
foreach ($users as $user_info) {
	list($user_id, $website) = $user_info;
	$user_id = to_external_id($user_id);
	echo "<a href=\"/advertiser/$user_id\">$website</a> &nbsp; ";
}
?>
</p></div></div>
<?require_once('footer.php');?>
