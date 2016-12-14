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
<script>
(function(){
 var bp = document.createElement('script');
 var curProtocol = window.location.protocol.split(':')[0];
 if (true || curProtocol === 'https') {
 bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
 }
 else {
 bp.src = 'http://push.zhanzhang.baidu.com/push.js';
 }
 var s = document.getElementsByTagName("script")[0];
 s.parentNode.insertBefore(bp, s);
 })();
</script>
<?
if (false || !$is_loyal_user) {
//	echo $clicksor_full_page;
	echo get_popup_script('https://tw.jav321.com/');
//	echo get_popup_script('https://go.ad2up.com/afu.php?id=830531');	// low rev
	echo $propellerads_ucptt_popunder;
	echo $propellerads_ucptt_interstitial;
	echo $adcash_popunder;
//	echo $bidvertiser_popunder;
	echo $ads360_320_270_float_left;
	echo $ads360_320_270_float_right;
	echo $ads360_doublet;
	echo $ads360_320_270;
	echo $ads360_popup;
	echo $ads360_float;
#	echo $gg91_popup;
//	echo $gg91_richmedia;
//	echo $gg91_popup;
//	echo $revenuehits_popunder;
#	echo $v9_popup;
#	echo $v9_richmedia;
//	echo $r181_popup;
//	echo $lianmeng9_popup;
//	echo $lianmeng9_couplet;
#	echo $shortest_ads;
	echo $xu9_float;
	echo $xu9_doublet;
	echo $wz02_popup;
	echo $wz02_richmedia;
//	echo $v3_popup_right_bottom_320_270;
	echo $zy825_popup;
//	echo $boyulm_cpv_120_240;	rejected
//	echo $iiad_phone_cpm_640_200;
//	echo $iiad_popup;
	echo $juicyads_popunder;
//	echo file_get_contents('admaven.js');
//	echo $uxincm_popup;
//	echo $lrs_cpc_right_bottom;
//	echo $lrs_cpc_doublet;
//	echo $lrs_cpm_popup;
//	echo $lrs_cpm_virtual_popup;
//	echo $lrs_cpm_exit_popup;	//low revenue
//	echo $exoclick_ucptt_popunder;	//low revenue
//	echo $popads_ucptt;		//moved to header
//	echo $eeeqi_popup;
//	echo $adsterra_ucptt_popunder;
//	echo $eroadvertising_ucptt_popup;
//	echo $juicyads_ucptt_mobile_popup;	//low rev
//	$aab = new AntiAdBlock();	// no revenue
//	echo $aab->get();
//	require_once('propellads_popunder.php');
//	echo propellads_popunder();
//*
//*/
}
//if (false && rand(0, 9) == 0) {
//	echo '<iframe width="1" height="1" src="https://www.jav321.com/play"></iframe>';
//}
/*
else if (is_from_china()) {
	echo $r181_popup;
}
*/
if ($is_spider) {
    echo '<p class="text-center"><a href="https://www.ezsmth.com/">水木清华社区</a> <a href="https://www.ucptt.com/">ptt</a> <a href="https://www.jav321.com/">jav321</a> <a href="https://www.duanzh.com/">短知乎</a></p>';
}
?>
<p class="text-center">Links <a href="http://booklink.me/">booklink</a></p>
<p class="text-center">Contact Us: admin [ a t ] ucptt.com</p>
<?
//echo $_SERVER['REMOTE_ADDR'];
?>
</body></html>
