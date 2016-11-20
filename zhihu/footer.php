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
<p align="center">Contact: admin{a[_()_]t}duanzhihu.com</p>
<?
if ($is_spider) {
	echo '<p align="center"><a href="http://www.btsmth.com/">水木清华社区</a> <a href="https://www.ucptt.com/">ptt</a> <a href="https://www.jporndb.com/">japan av porn</a></p>';
}
if (!$is_loyal_user) {
	$sub_domain = 'www';
	if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'tw') === 0) {
		$sub_domain = 'tw';
	}
	echo get_popup_script("https://$sub_domain.jav321.com/");
}
if (false || $is_google_spider) {
	if (!isset($title) || $title == '') $title = '短知乎';
	echo '<a href="http://tw.duanzhihu.com'.$_SERVER['REQUEST_URI'].'">'.$html_title.'</a>';
}
?>
</body></html>
