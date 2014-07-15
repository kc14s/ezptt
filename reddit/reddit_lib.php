<?
function get_img_url($url) {
	if (preg_match("/imgur.com\/([a-zA-Z0-9.]+)$/", $url, $matches)) {
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
