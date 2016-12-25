#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('zhihu_lib.pl');

our $db_conn = init_db();
$db_conn->do("use zhihu");
$db_conn->do("set names utf8");

my $max_id = execute_scalar("select max(id) from answer");
print '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
my $req = $db_conn->prepare('select aid, date(pub_time) from answer where id < '.rand($max_id).' order by id desc limit 50000');
$req->execute();
while (my ($aid, $pub_time) = $req->fetchrow_array) {
	my $url = "https://www.duanzh.com/answer/$aid";
	print "<url><loc>$url</loc><lastmod>$pub_time</lastmod><changefreq>never</changefreq></url>\n";
}
print '</urlset>';

