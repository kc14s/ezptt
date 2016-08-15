#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

#my $req = $db_conn->prepare("select sn, release_date from video order by seed_popularity desc limit 50000");
my $req = $db_conn->prepare("select sn, release_date from video order by release_date desc limit 50000");
#my $req = $db_conn->prepare("select sn, release_date from video");
$req->execute;
print '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
while (my ($sn, $release_date) = $req->fetchrow_array) {
	my $url = "http://cn.jporndb.com/video/$sn";
	print "<url><loc>$url</loc><lastmod>$release_date</lastmod><changefreq>never</changefreq></url>\n";
}
print '</urlset>';
