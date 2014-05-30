#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');

if (scalar @ARGV != 1) {
	die("usage: perl spider.pl spider.conf");
}

my $now = `date +'%F %T'`;
chomp $now;
print "spider starts at $now\n";
my $configs = load_config($ARGV[0]);
load_proxy();
if (!init_db()) {
	print STDERR "init db failed\n";
	exit;
}

my @boards = get_all_boards($configs);
update_all_boards(@boards);
foreach my $board (@boards) {
#	next if ($board->[2] ne 'Beauty');
	my $topics = get_topics($board);
	download_topics($board, $topics);
}
