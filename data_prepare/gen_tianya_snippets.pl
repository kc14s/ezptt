#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');
require('config.pl');

my $db_conn = init_db();
$db_conn->do('use tianya');

gen_tianya_index();
