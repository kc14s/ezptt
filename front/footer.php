</div>
<script> 
var interval = setInterval(function(){
$("img").lazyload();
clearInterval(interval);
},1000);
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
<img src="<?php echo $_hmtPixel; ?>" width="0" height="0" />
<p class="text-center">Links <a href="http://www.redditfun.com/">reddit fun</a></p>
</body></html>
