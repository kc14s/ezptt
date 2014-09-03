<?
require_once('header.php');
?>

<div class="row"><div class="col-md-8 col-md-offset-2 col-xs-10"><nav class="navbar navbar-default navbar-static-top" role="navigation">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="/">Ads Analysis</a>
</div>
<div class="collapse navbar-collapse">
<form class="navbar-form navbar-left" role="search" action="/search" method="POST">
<div class="form-group">
<input type="text" class="form-control" name="query" size="60" value="">
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
</div>
<?
require_once('footer.php');
?>
