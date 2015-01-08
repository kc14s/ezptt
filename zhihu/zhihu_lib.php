<?
function process_answer_content($content) {
	$content = str_replace('<img ', '<img class="img-responsive" ', $content);
	$content = preg_replace('/<a href="\/question\/\d+\/answer\/\d+" class="toggle-expand">显示全部<\/a>/', '', $content);
	$content = str_replace('<div class="fixed-summary-mask">', '', $content);
	return $content;
}
?>
