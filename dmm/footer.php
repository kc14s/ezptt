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
<script>	//baidu push
(function(){
 var bp = document.createElement('script');
 var curProtocol = window.location.protocol.split(':')[0];
 if (curProtocol === 'https') {
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
if (false && !$is_loyal_user) {
	echo $cpm365_popup;
	echo $ads360_320_270_float_left;
	echo $ads360_320_270_float_right;
	echo $ads360_doublet;
	echo $ads360_popup;
	echo $ads360_float;
//	echo $uxincm_popup;
	echo $r181_popup;
}
if ($is_spider) {
	echo get_inter_link();
}
?>
</body></html>
