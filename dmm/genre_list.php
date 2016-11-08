<?
require_once('init.php');
require_once('i18n.php');
require_once('dmm_lib.php');
$db_conn = conn_dmm_db();

$page = $_GET['page'];
$page_size = 12;

$categories = array('シチュエーション', 'タイプ', 'コスチューム', 'ジャンル', 'プレイ', 'その他');

$html = '<div class="h1 text-center">'.i18n('genre').'</div>';
foreach ($categories as $category) {
	$genre_set = execute_dataset("select id, genre from genre_list where category = '$category'");
	$html .= '<div class="row"><div class="col-md-10 col-md-offset-1 col-xs-12">';
	$html .= '<div class="panel panel-info">';
	$html .= '<div class="panel-heading">'.i18n($category).'</div>';
	$html .= '<div class="panel-body">';
	for ($col = 0; $col < count($genre_set); ++$col) {
		list($genre_id, $genre) = $genre_set[$col];
		if ($col % 3 == 0) {
			$html .= '<div class="row">';
		}
		$html .= '<div class="col-md-4 col-xs-6"><a href="/genre/'.$genre_id.'/1">'.i18n($genre).'</a></div>';
		if ($col % 3 == 2 || $col == count($genre_set) - 1) {
			$html .= '</div>';
		}
	}
	$html .= '</div></div></div></div>';
}

$target = '_blank';
$html_title = 'Japan Porn Database';
require_once('header.php');
echo $html;
require_once('footer.php');

?>
