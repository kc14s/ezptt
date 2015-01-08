#!/usr/bin/perl -w
use strict;
use HTML::Element;
use HTML::TreeBuilder;
require('config.pl');
require('lib.pl');
require('ty_lib.pl');

my $now = `date +'%F %T'`;
chomp $now;
print "spider starts at $now\n";
my $db_conn = init_db();
$db_conn->do("use $ENV{'database_ty'}");
my $board_index_url = 'http://focus.tianya.cn/thread/index.shtml';
my @boards = fetch_boards($board_index_url);
foreach (@boards) {
	fetch_threads($_);
}
