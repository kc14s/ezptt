<?
function process_answer_content($content, $aid) {
	global $type;
	global $is_spider;
	$content = str_replace('<img ', '<img class="img-responsive" ', $content);
	$content = preg_replace('/<a href="\/question\/\d+\/answer\/\d+" class="toggle-expand">显示全部<\/a>/', '', $content);
	$content = str_replace('<div class="fixed-summary-mask">', '', $content);
	$content = preg_replace('/<img [^>]+?src="\/\/.+?>/', '', $content);
	$content = str_replace('<noscript>', '', $content);
	$content = preg_replace('/[^>]*?<\/noscript>/', '', $content);
	$content = preg_replace('/pic\d+.zhimg.com/', 'image.duanzhihu.com', $content);
	if (!$is_spider && ($type == 'hot' || $type == 'reply')) {
		if (mb_strlen($content, 'utf8') > 140) {
			$content = str_replace('<br>', '`', $content);
			$content = strip_tags($content);
			$content = mb_substr($content, 0, 140, 'utf-8').'……<a href="/answer/'.$aid.'">[显示全部]</a>';
			$content = str_replace('`', '<br>', $content);
		}
	}
	$div_open_count = substr_count($content, '<div');
	$div_close_count = substr_count($content, '</div>');
	for ($i = $div_close_count; $i < $div_open_count; ++$i) {
		$content .= '</div>';
	}
	/*
	if (strpos($content, '<div class="highlight">') === false) {}
	else {
//		echo 'contains';
		$content .= '</div>';
	}
	*/
	return $content;
}
?>
