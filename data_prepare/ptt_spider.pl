#!/usr/bin/perl -w
use strict;
use DBI;
#use Test::LeakTrace;
use Scalar::Util;
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
my @boards = get_hot_boards();
update_all_boards(@boards);
#leaktrace {
foreach my $board (@boards) {
#	next if ($board->[2] ne 'Beauty');
	my $topics = get_topics($board);
#	download_topics($board, $topics);
}
#} sub {
#	my($ref, $file, $line) = @_;
#	warn "leaked $ref from $file $line\n";
#}
