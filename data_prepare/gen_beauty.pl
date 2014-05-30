#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

if (!init_db()) {
	print STDERR "init db failed\n";
	exit;
}

gen_beauty();
