#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('wxc_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");

for (my $page = 1; $page <= 273; ++$page) {
	my $url = "http://www.playno1.com/portal.php?mod=list&catid=3&page=$page";
	my $list_html = get_url($url);
	my @arr = split('<div class=\'fire_float\'>', $list_html);
	for (my $i = 1; $i < @arr; ++$i) {
		my $item = $arr[$i];
		my ($img_url, $title) = ($1, $2) if ($item =~ /<img src="([^"]+?)" title="([\d\D]+?)"/);
		my $tid = $1 if ($item =~ /<a href="article-(\d+)-1\.html"/);
		my $pub_time = $1 if ($item =~ /<span class="fire_left">([\d\- :]+)<\/span>/);
		print "$tid $pub_time $title\n";
		next if (execute_scalar("select count(*) from play_topic where tid = $tid") > 0);
		my $html = get_url("http://www.playno1.com/article-$tid-1.html");
		my $content = $1 if ($html =~ /<!--\${ \/if}-->\s*([\d\D]+?)<\/td><\/tr><\/table>/);
		my $snn = '';
		if ($content =~ /番：(\w+)-?(\d{3})/) {
			$snn = lc($1).$2;
		}
		print "snn $snn\n";
		$title = $db_conn->quote($title);
		$content = $db_conn->quote($content);
		$db_conn->do("insert into play_topic(tid, pub_time, snn, img_url, title, content) values($tid, '$pub_time', '$snn', '$img_url', $title, $content)");
	}
	last;
}
