#!/usr/bin/perl -w
use strict;
use warnings;
use DBI;
require('config.pl');
require("lib.pl");

if (0 && scalar @ARGV == 0) {
		die("usage: perl spider.pl spider.conf");
}
my $db_conn = init_db();
for my $table_name ('jandan_beauty', 'jandan_funny') {
#		my $request = $db_conn->prepare("select id, url from $table_name where url like '\%$ARGV[0]%' and enabled = 1");
		my $request = $db_conn->prepare("select id, url from $table_name where enabled = 1");
		$request->execute();
		while (my ($id, $url) = $request->fetchrow_array) {
#				next if (index($url, $ARGV[0]) < 0);
				my $status_line = `curl -L -e http://www.btsmth.com/ -I --max-redirs 0 '$url' 2>/dev/null`;
				if ($status_line !~ /HTTP\/1.\d 200/ || index($status_line, 'Content-Type: text') > 0) {
						$db_conn->do("update $table_name set enabled = 0 where url = '$url'");
						print "$url\n";
#                       print "$status_line\n";
				}
				else {
#						print "$status_line\n";
				}
		}
}
