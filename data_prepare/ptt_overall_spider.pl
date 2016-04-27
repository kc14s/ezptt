#!/usr/bin/perl -w
use strict;
use DBI;
use Test::LeakTrace;

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

#update_board_category();
my @boards = get_all_boards();
#update_all_boards(@boards);
foreach my $board (@boards) {
#	next if ($board->[2] ne 'Beauty');
#	leaktrace {
		get_topics($board);
#	} -verbose;
#	last;
#	} sub {
#		my($ref, $file, $line) = @_;
#		warn "leaked $ref from $file $line\n";
#	}
#	download_topics($board, $topics);
}
