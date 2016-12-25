#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

my $db_conn = init_db();
my $dir = '/root/ptt/front';
open OUT, ">$dir/sitemap.xml";
open OUT_BAIDU, ">$dir/sitemap_baidu.xml";
print OUT '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
print OUT_BAIDU '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
my $max_id = execute_scalar("select max(id) from topic");
my $req = $db_conn->prepare('select en_name, tid1, tid2, date(pub_time) from board, topic where board.id = topic.bid and topic.id < '.rand($max_id).' order by topic.id desc limit 23000');
$req->execute();
while (my ($en_name, $tid1, $tid2, $pub_time) = $req->fetchrow_array) {
	my $url = "http://www.ucptt.com/article/$en_name/$tid1/$tid2";
	print OUT "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
	$url = "https://www.ucptt.com/article/$en_name/$tid1/$tid2";
	print OUT_BAIDU "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
}

$req = $db_conn->prepare('select tid, date(pub_time) from douban.topic order by tid desc limit 23000');
$req->execute();
while (my ($tid, $pub_time) = $req->fetchrow_array) {
	my $url = "http://www.ucptt.com/douban/$tid";
	print OUT "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
	$url = "https://www.ucptt.com/douban/$tid";
	print OUT_BAIDU "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
}

$req = $db_conn->prepare('select bid, tid, date(pub_time) from ck101.topic order by tid desc limit 4000');
$req->execute();
while (my ($bid, $tid, $pub_time) = $req->fetchrow_array) {
	my $url = "http://www.ucptt.com/ck101/$bid/$tid";
	print OUT "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
	$url = "https://www.ucptt.com/ck101/$bid/$tid";
	print OUT_BAIDU "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
}
print OUT '</urlset>';
close OUT;
print OUT_BAIDU '</urlset>';
close OUT_BAIDU;

