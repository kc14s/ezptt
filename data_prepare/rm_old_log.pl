#!/usr/bin/perl -w
require('lib.pl');

my $date_str = get_date_str(-3);
`log/*$date_str*`;
`../spider/log/*$date_str*`;
