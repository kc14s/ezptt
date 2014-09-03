<?
function get_img_url($url, $type) {
	if ($type == 1) {
		$regex = '/imgur.com\/([a-zA-Z0-9.]+)$/';
	}
	else {
		$regex = '/imgur.com\/([a-zA-Z0-9.]+)/';
	}
	if (preg_match($regex, $url, $matches)) {
		if (strpos($matches[1], '.') > 0) {
			$img_url = "http://i.imgur.com/".$matches[1];
		}
		else {
			$img_url = "http://i.imgur.com/".$matches[1].'.jpg';
		}
		return $img_url;
	}
}
?>
