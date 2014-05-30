#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

my $now = `date +'%F %T'`;
chomp $now;
print "spider starts at $now\n";
#load_proxy();
if (!init_db()) {
	print STDERR "init db failed\n";
	exit;
}

update_board_category();
my @boards = get_all_boards();
update_all_boards(@boards);
foreach my $board (@boards) {
#	next if ($board->[2] ne 'Beauty');
	my $topics = get_topics($board);
#	download_topics($board, $topics);
}
