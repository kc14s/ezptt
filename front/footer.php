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
	echo $ads360_320_270_float_left;
	echo $ads360_320_270_float_right;
	echo $ads360_doublet;
	echo $ads360_320_270;
	echo $ads360_popup;
	echo $ads360_float;
#	echo $gg91_popup;
	echo $gg91_richmedia;
//	echo $revenuehits_popunder;
#	echo $v9_popup;
#	echo $v9_richmedia;
	echo $r181_popup;
//	echo $lianmeng9_popup;
//	echo $lianmeng9_couplet;
#	echo $shortest_ads;
//	echo $xu9_float;
//	echo $xu9_doublet;
	echo $cpm365_popup;
	echo $v3_popup_right_bottom_320_270;
	echo $zy825_popup;
	echo $boyulm_cpv_120_240;
	echo $iiad_phone_cpm_640_200;
//	echo $uxincm_popup;
//	echo $lrs_cpc_right_bottom;
//	echo $lrs_cpc_doublet;
//	echo $lrs_cpm_popup;
//	echo $lrs_cpm_virtual_popup;
//	echo $lrs_cpm_exit_popup;
/*
	echo '<script type="text/javascript">
	window.onclick = function() {
//	    if (Math.floor(Math.random() * ( 10 + 1)) == 1) {
		        window.open("http://www.17k.com/book/1337071.html");
//	    }
	}
</script>';
*/
}
if (false && rand(0, 9) == 0) {
	echo '<iframe width="1" height="1" src="http://www.17k.com/book/1337071.html"></iframe>';
}
/*
else if (is_from_china()) {
	echo $r181_popup;
}
*/
if ($is_spider) {
    echo '<p class="text-center"><a href="http://www.btsmth.com/">水木清华社区</a> <a href="http://www.ucptt.com/">ptt</a> <a href="https://cn.jporndb.com/">japan av porn</a> <a href="http://www.duanzhihu.com/">短知乎</a></p>';
}
?>
<p class="text-center">Links <a href="http://booklink.me/">booklink</a></p>
<p class="text-center">Contact Us: admin [ a t ] ucptt.com</p>
<?
//echo $_SERVER['REMOTE_ADDR'];
?>
</body></html>
