<div align="center"><? if (!$is_loyal_user) echo $cpc_chaping_cpc; ?></div>
</div>
<script> 
$(function() { 
	$("img").lazyload(); 
	effect : "fadeIn";

}); 
$("img").load(function(){
	if ($(this).width() > document.body.clientWidth - 30) {
		$(this).width(document.body.clientWidth - 30);
	}
});
</script>
<img src="<?php echo $_hmtPixel; ?>" width="0" height="0" /></body></html>
