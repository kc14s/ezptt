#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');
require('ave_lib.pl');

my %dept_ids = (
#29 => 45,
#31 => 262,
#43 => 461
29 => 43,
31 => 278,
43 => 462
);
our $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
my @video_list_urls;
while (my ($dept_id, $sub_dept_id) = each %dept_ids) {
	my $url = "http://www.aventertainments.com/subdept_products.aspx?dept_id=$dept_id&subdept_id=$sub_dept_id&languageID=2";
	push @video_list_urls, $url;
}
%dept_ids = (
29 => 45,
31 => 262,
43 => 461
);
while (my ($dept_id, $sub_dept_id) = each %dept_ids) {
	my $url = "http://www.aventertainments.com/subdept_products.aspx?dept_id=$dept_id&subdept_id=$sub_dept_id&languageID=2";
	push @video_list_urls, $url;
}
#@video_list_urls = ('http://www.aventertainments.com/studio_products.aspx?StudioID=722&Dept_ID=43&languageID=2');
download_video_list(@video_list_urls);
