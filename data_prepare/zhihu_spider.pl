#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('zhihu_lib.pl');

our $db_conn = init_db();
$db_conn->do("use zhihu");
#$db_conn->do("set names utf8");

my $groups = get_groups();
foreach my $group (@$groups) {
	my $boards = get_boards($group);
	foreach my $board (@$boards) {
		my $questions = get_zhihu_questions($board);
		foreach my $question (values %$questions) {
			get_zhihu_question($question);
		}
	}
}
