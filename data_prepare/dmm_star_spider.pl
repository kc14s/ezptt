#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

$db_conn->do("update star_info set rank = 10000 where rank <> 10000");
for (my $page = 1; $page <= 5; ++$page) {
	my $url = "http://www.dmm.co.jp/digital/videoa/-/ranking/=/term=monthly/type=actress/page=$page/";
	my $html = get_url($url);
	while ($html =~ /<span class="rank">(\d+)<\/span><a href="\/digital\/videoa\/\-\/list\/=\/article=actress\/id=(\d+)\/">/g) {
		$db_conn->do("update star_info set rank = $1 where id = $2");
		print "$1\t$2\n";
	}
}

my @keywords = qw(a i u e o ka ki ku ke ko sa si su se so ta ti tu te to na ni ne no ha hi hu he ho ma mi mu me mo ya yu yo ra ri ru re ro wa);
for my $keyword (@keywords) {
	for (my $page = 1; ; ++$page) {
		my $url = "http://www.dmm.co.jp/digital/videoa/-/actress/=/keyword=$keyword/page=$page/";
		my $html = get_url($url);
		my $count = 0;
		while ($html =~ /<a href="http:\/\/www\.dmm\.co\.jp\/digital\/videoa\/\-\/list\/=\/article=actress\/id=(\d+)\/sort=ranking\/"><img src="http:\/\/pics\.dmm\.co\.jp\/mono\/actjpgs\/medium\/(\w+)\.jpg" width="100" height="100" alt="([\d\D]+?)"><br>/g) {
			if (execute_scalar("select count(*) from star_info where id = $1") == 0) {
				$db_conn->do("insert into star_info(id, name, pic_name) values($1, '$3', '$2')");
				print "1\t";
			}
			else {
				$db_conn->do("update star_info set name = '$3', pic_name = '$2' where id = $1");
				print "2\t";
			}
			print "$1\t$3\t$2\n";
			++$count;
		}
		last if ($count == 0);
	}
}

