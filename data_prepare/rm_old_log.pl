#!/usr/bin/perl -w
require('lib.pl');

my $date_str = get_date_str(-3);
`rm log/*$date_str*`;
`rm ../spider/log/*$date_str*`;
