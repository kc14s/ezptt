#!/usr/bin/perl -w
use strict;
use warnings;
use DBI;
require("config.pl");
require("lib.pl");

if (0 && scalar @ARGV != 1) {
		die("usage: perl spider.pl spider.conf");
}
#load_config($ARGV[0]);
my $db_conn = init_db();
$db_conn->do('use ezptt');
#for (my $page = 3859; $page <= 5912; ++$page) {	#funny
for (my $page = 1318; $page <= 2170; ++$page) {		#beauty
		print "page $page\n";
		my $content = `wget 'http://jandan.net/ooxx/page-$page#comments' -O - 2>/dev/null`;	#beauty
#		my $content = `curl -s -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' 'http://jandan.net/pic/page-$page#comments'`;	#funny
#		$content = encode('gbk', decode('UTF8', $content));
#	print $content;
#	exit;
		my @articles = split('<li', $content);
		while ($content =~ /<li ([\d\D]+?)<\/li>/g) {
				my $article = $1;
				my $gid = $1 if ($article =~ /id="comment\-(\d+)"/);
				next if (!defined($gid));
#my $author = $1 if ($article =~ /<b>([\d\D]+?)<\/b>/);
				my $author = $1 if ($article =~ /<strong\s+title="[\d\D]+?"\s*>([\d\D]+?)<\/strong>/);
				if (!defined($author)) {
						print STDERR "$page $gid no author\n";
						next;
				}
#$author = $1 if ($author =~ />([\d\D]+?)</);
				my $oo = $1 if ($article =~ /<span id="cos_support-\d+">(\d+)<\/span>/);
				my $xx = $1 if ($article =~ /<span id="cos_unsupport-\d+">(\d+)<\/span>/);
				while ($article =~ /<img src="([\d\D]+?)"/g) {
						my $url = $1;
						my $status_line = `curl -e -L http://www.btsmth.com/ -m 10 -I --max-redirs 0 '$url' 2>/dev/null`;
#			if (index($status_line, '200') < 0 || index($status_line, 'Content-Type: text') > 0) {
				if (index($status_line, '200') < 0) {
						print $status_line."\n";
						next;
				}
				next if (execute_scalar("select count(*) from jandan_beauty where url = '$url'", $db_conn) > 0);
#				next if (execute_scalar("select count(*) from jandan_funny where url = '$url'", $db_conn) > 0);
				print "$page\t$gid\t$author\t$oo\t$xx\t$1\n";
				$db_conn->do("insert into jandan_beauty(gid, author, oo, xx, url) values($gid, '$author', $oo, $xx, '$1')");
#				$db_conn->do("insert into jandan_funny(gid, author, oo, xx, url) values($gid, '$author', $oo, $xx, '$1')");
		}
	}
}

