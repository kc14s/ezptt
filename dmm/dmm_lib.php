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
		return "http://pics.dmm.co.jp/digital/video/$sn/${sn}jp-$id.jpg";
	break;
	case 2:
		return "http://pics.dmm.co.jp/digital/amateur/$sn/${sn}jp-00$id.jpg";
	break;
	}
	return '';
}
?>
