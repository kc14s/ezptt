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
<?
if (false || !$is_loyal_user) {
//	echo $adcash_popunder;
//	echo $clicksor_full_page;
//	echo $ads360_320_270;
	echo $ads360_popup;
//	echo $gg91_popup;
//	echo $gg91_richmedia;
//	echo $revenuehits_popunder;
	//echo $v9_popup;
//	echo $v9_richmedia;
//	echo $r181_popup;
//	echo $lianmeng9_popup;
//	echo $lianmeng9_couplet;
#	echo $shortest_ads;
//	echo $xu9_float;
//	echo $xu9_doublet;
}
/*
else if (is_from_china()) {
	echo $r181_popup;
}
*/
if ($is_spider) {
    echo '<p class="text-center"><a href="http://www.btsmth.org/">水木清华社区</a> <a href="http://www.ucptt.com/">ptt</a> <a href="http://www.jporndb.com/">japan av porn</a> <a href="http://www.redditfun.com/">reddit</a> <a href="http://www.duanzhihu.com/">短知乎</a></p>';
}
?>
<p class="text-center">Links <a href="http://www.duanzhihu.com/">短知乎</a> <a href="http://www.redditfun.com/">reddit fun</a> <a href="http://booklink.me/">booklink</a><img width="0" height="0" src="/pb.gif?ts=<?echo time();?>"></p>
<p class="text-center">Contact Us: admin [ a t ] ucptt.com</p>
</body></html>
