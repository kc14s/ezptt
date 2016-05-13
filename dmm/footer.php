<script> 
var interval = setInterval(function(){
	$("img").lazyload();
	clearInterval(interval);
},1000);
$(function() { 
	$("img").lazyload(); 
	effect : "fadeIn";

}); 
</script>
<?
if (!$is_loyal_user) {
	echo $cpm365_popup;
	echo $ads360_320_270_float_left;
	echo $ads360_320_270_float_right;
	echo $ads360_doublet;
	echo $ads360_popup;
	echo $ads360_float;
//	echo $uxincm_popup;
	echo $r181_popup;
}
?>
</body></html>
