#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');

if (scalar @ARGV != 1) {
	die("usage: perl spider.pl spider.conf");
}

my $configs = load_config($ARGV[0]);
if (!init_db()) {
	print STDERR "init db failed\n";
	exit;
}

gen_beauty();
