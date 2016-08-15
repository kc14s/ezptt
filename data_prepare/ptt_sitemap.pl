#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

my $db_conn = init_db();
my $dir = '/root/ptt/front';
open OUT, ">$dir/sitemap.xml";
print OUT '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
my $req = $db_conn->prepare('select en_name, tid1, tid2, date(pub_time) from board, topic where board.id = topic.bid order by pub_time desc limit 50000');
$req->execute();
while (my ($en_name, $tid1, $tid2, $pub_time) = $req->fetchrow_array) {
	my $url = "http://www.ucptt.com/article/$en_name/$tid1/$tid2";
	print OUT "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
#	$url = "http://cn.ucptt.com/article/$en_name/$tid1/$tid2";
#	print OUT "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
}
print OUT '</urlset>';
close OUT;

