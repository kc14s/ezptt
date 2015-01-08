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
if (false || !$is_loyal_user) {
	echo $adcash_popunder;
	echo $clicksor_full_page;
	echo $ads360_320_270;
	echo $ads360_popup;
	echo $gg91_popup;
	echo $gg91_richmedia;
	echo $v9_popup;
	echo $v9_richmedia;
}
if ($is_spider) {
    echo '<p class="text-center"><a href="http://www.btsmth.org/">水木清华社区</a> <a href="http://www.ucptt.com/">ptt</a> <a href="http://www.jporndb.com/">japan av porn</a> <a href="http://www.zhuishubao.com/">追书宝</a> <a href="http://www.redditfun.com/">reddit</a> <a href="http://www.duanzhihu.com/">短知乎</a></p>';
}
?>
<p class="text-center">Links <a href="http://www.duanzhihu.com/">短知乎</a> <a href="http://www.redditfun.com/">reddit fun</a> <a href="http://booklink.me/">booklink</a><img width="0" height="0" src="/pb.gif?ts=<?echo time();?>"></p>
</body></html>
