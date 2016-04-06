#!/usr/bin/perl -w
use strict;

open IN, 'mysql -pwy7951610 ezptt -Ne "select en_name from board" | ';
while (<IN>) {
	chomp;
	print "'$_',\n";
}
close IN;
