<?
function from_external_id($id) {
	return ($id - 110512) / 3;
}

function to_external_id($id) {
	return $id * 3 + 110512;
}
?>
