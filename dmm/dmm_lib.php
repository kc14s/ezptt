<?
function get_cover_img_url($sn, $channel) {
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		return "http://pics.dmm.co.jp/digital/video/$sn/${sn}ps.jpg";
	break;
	case 2:
		return "http://pics.dmm.co.jp/digital/amateur/$sn/${sn}jp.jpg";
	break;
	}
}

function get_cover_img_large_url($sn, $channel) {
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		return "http://pics.dmm.co.jp/digital/video/$sn/${sn}pl.jpg";
	break;
	return '';
	}
}

function get_sample_img_url($sn, $channel, $id) {
	switch ($channel) {
	case 1:
	case 3:
	case 4:
		return "http://pics.dmm.co.jp/digital/video/$sn/${sn}jp-$id.jpg";
	break;
	case 2:
		return "http://pics.dmm.co.jp/digital/amateur/$sn/${sn}jp-00$id.jpg";
	break;
	}
	return '';
}

function get_thumb_url($star_pic_name) {
	return "http://pics.dmm.co.jp/mono/actjpgs/$star_pic_name.jpg";
}

function snn_add_hyphen($snn) {
	if (preg_match("/([a-z]+)(\d+)/", $snn, $matches)) {
		return $matches[1].'-'.$matches[2];
	}
	else {
		return $snn;
	}
}

function get_rand_dmm_thumb_html() {
	$html = '';
	$result = mysql_query("select title, sn, channel from video order by seed_popularity desc limit ".rand(0, 100).', 8');
	while (list($title, $sn, $channel) = mysql_fetch_array($result)) {
		$video = array($title, $sn, $channel);
		$videos[] = $video;
	}
	$html = '<div class="panel panel-info">';
	$html .= '<div class="panel-heading"><a href="/list/popularity/1">'.i18n('popularity').'</a></div>';
	$html .= '<div class="panel-body">';
	$col = 0;
	foreach ($videos as $video) {
		if ($col % 4 == 0) $html .= '<div class="row"><div class="col-md-12">';
		list($title, $sn, $channel) = $video;
		$html .= "<div class=\"col-xs-6 col-md-3\"><div class=\"thumbnail\"><a href=\"/video/$sn\"><img data-original=\"".get_cover_img_url($sn, $channel)."\"><br>$title</a></div></div>";
		if ($col % 4 == 3) $html .= '</div></div>';
		++$col;
	}
	if ($counter % 4 != 0) $html .= '</div></div>';
	$html .= '</div></div>';
	return $html;
}
?>
