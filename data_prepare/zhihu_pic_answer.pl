#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');
require('zhihu_lib.pl');

my $db_conn = DBI->connect("DBI:mysql:database=zhihu;host=localhost", 'root', 'wy7951610');
my $sql = 'select aid, content, ups, pic from answer where ups >= 3 and content like "%<img%"';
my $request = $db_conn->prepare($sql);
$request->execute;
while (my ($aid, $content, $ups, $pic_old) = $request->fetchrow_array) {
	my $pic = is_pic_answer($content, $ups);
	if ($pic != $pic_old) {
		$db_conn->do("update answer set pic = $pic where aid = $aid");
		print "$aid $ups $pic\n";
	}
}
