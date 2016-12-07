</div>
<script> 
var interval = setInterval(function(){
	$("img").lazyload();
	clearInterval(interval);
},1000);
$(function() { 
				$("img").lazyload({ 
effect : "fadeIn" 
}); 
				}); 
</script>
<script>
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
<!-- <p align="center">Contact: admin{a[_()_]t}duanzhihu.com</p> -->
<?
if ($is_spider) {
	echo get_inter_link();
}
if (!$is_loyal_user) {
//	echo get_popup_script("https://www.jav321.com/");
}
if (false || $is_google_spider) {
	if (!isset($html_title) || $html_title == '') $html_title = '短知乎';
	echo '<a href="https://tw.duanzh.com/">'.$html_title.'</a>';
}
?>
</body></html>
