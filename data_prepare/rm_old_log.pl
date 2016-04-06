#!/usr/bin/perl -w
require('lib.pl');

my $date_str = get_date_str(-5);
`rm log/*$date_str*`;
`rm ../spider/log/*$date_str*`;
