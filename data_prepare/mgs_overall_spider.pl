#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');
require('mgs_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my $page_size = 120;
my @url_templates = qw(
http://www.mgstage.com/search/search.php?image_word_ids[]=shirouto
http://www.mgstage.com/search/search.php?image_word_ids[]=nanpatv
http://www.mgstage.com/search/search.php?monthly_limit=0&is_monthly=0&sort=popular
);

for my $url_template (@url_templates) {
	for (my $page = 1; ; ++$page) {
		my $url = "$url_template&list_cnt=$page_size&page=$page";
		my $list_html = get_url($url);
		my $count = 0;
		while ($list_html =~ /<a href="\/product\/product_detail\/([\w\-]+)\/"><img/g) {
			++$count;
			my $sn = lc($1);
			my $snn = normalize_sn($sn);
			my $dmm_sn = execute_scalar("select sn from video where sn_normalized = '$snn' and channel < 8");
			if ($dmm_sn ne '0') {
				print "dmm mgs conflict $dmm_sn $1\n";
				next;
			}
			download_mgs_video($1, $db_conn);
		}
		last if ($count == 0);
	}
}
