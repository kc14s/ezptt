<div align="center"><? //echo $baidu_480_160; ?></div>
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
