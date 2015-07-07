#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');

my $db_conn = DBI->connect("DBI:mysql:database=zhihu;host=localhost", 'root', '');
my $sql = 'select aid, max(ups) from comment where length(content) < 210 and ups >= 30 group by aid';
my $request = $db_conn->prepare($sql);
$request->execute;
while (my ($aid, $comment_ups) = $request->fetchrow_array) {
	my $answer_ups = execute_scalar('select ups from answer where aid = '.$aid, $db_conn);
	next if ($answer_ups == 0);
	if ($answer_ups < $comment_ups * 2) {
		print "$aid\t$answer_ups\t$comment_ups\n";
		$db_conn->do("update answer set reply = 1 where aid = $aid");
	}
}
