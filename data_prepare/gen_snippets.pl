#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');
require('config.pl');

if (!init_db()) {
	print STDERR "init db failed\n";
	exit;
}

gen_ptt_index();
