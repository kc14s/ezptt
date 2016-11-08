#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');
require('ave_lib.pl');

#my @dept_ids = (29, 31, 43);
my @dept_ids = (43);
our $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
for my $dept_id (@dept_ids) {
	my $url = "http://www.aventertainments.com/studiolists.aspx?&Dept_ID=$dept_id&languageID=2";
	my $studio_list_html = get_url($url);
	my %video_list_urls;
	while ($studio_list_html =~ /(http:\/\/www\.aventertainments\.com\/studio_products\.aspx\?StudioID=\d+&languageID=\d+&Dept_ID=\d+)/g) {
		$video_list_urls{$1} = 0;
#		last;
	}
	download_video_list(keys %video_list_urls);
}
