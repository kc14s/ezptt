
my $ptt_db_conn;
sub init_ptt_db {
	my $db_server = '106.184.2.97';
	my $database = 'douban';
	my $user = 'root';
	my $password = 'wy7951610';
	$ptt_db_conn = DBI->connect("DBI:mysql:database=$database;host=$db_server", $user, $password,  {RaiseError => 1, AutoCommit =>1, mysql_auto_reconnect=>1});
	$ptt_db_conn->do("set names UTF8");
	$ptt_db_conn->do("SET time_zone = '+8:00'");
	$ptt_db_conn->do('SET LOW_PRIORITY_UPDATES=1');
	$ENV{'ptt_db_conn'} = $ptt_db_conn;
	return $ptt_db_conn;
}
sub discover_boards {
	my ($html, $featured) = @_;
	my @arr = split("<li class=\"\">", $html);
	for my $item (@arr) {
		my ($bid, $pic_name, $bname, $member_num);
		if ($item =~ /<a href="https:\/\/www\.douban\.com\/group\/(\w+)\/"><img src="https:\/\/img\d\.doubanio\.com\/icon\/([\w\-\.]+)" class="">/) {
			($bid, $pic_name) = ($1, $2);
		}
		else {
			next;
		}
		if ($item =~ /<a href="https:\/\/www\.douban\.com\/group\/\w+\/" class="">([\d\D]+?)<\/a>/) {
			$bname = $1;
		}
		else {
			next;
		}
		if ($item =~ /<span class="num">\s*(\d+)/) {
			$member_num = $1;
		}
		else {
			next;
		}
		print "board $featured $bid, $pic_name, $bname, $member_num\n";
		$db_conn->do("replace into board(bid, bname, pic_name, member_num, featured) values('$bid', '$bname', '$pic_name', $member_num, 1)");
#		$ENV{'ptt_db_conn'}->do("replace into board(bid, bname, pic_name, member_num, featured) values('$bid', '$bname', '$pic_name', $member_num, 1)");
	}

	while ($html =~ /<span class="from">来自<a href="https:\/\/www\.douban\.com\/group\/(\w+)\/">([\d\D]+?)<\/a><\/span>/g) {
		my ($bid, $bname) = ($1, $2);
		save_board($bid, $bname);
	}
}

sub save_board {
	my ($bid, $bname, $pic_name, $member_num, $featured) = @_;
	$featured = 0 if (!defined $featured);
	my $exist = execute_scalar("select count(*) from board where bid = '$bid'");
	if (defined $pic_name) {
		if ($exist) {
			$db_conn->do("update board set bname = '$bname', pic_name = '$pic_name', member_num = $member_num, featured = $featured where bid = '$bid'");
		}
		else {
			$db_conn->do("replace into board(bid, bname, pic_name, member_num, featured) values('$bid', '$bname', '$pic_name', $member_num, $featured)");
		}
	}
	else {
		if (!$exist) {
			$db_conn->do("insert into board(bid, bname) values('$bid', '$bname')");
		}
	}
}

sub download_featured_topics {
	my ($html) = @_;
	my $continue = 1;
	while ($continue && $html =~ /<h3><a href="https:\/\/www\.douban\.com\/group\/topic\/(\d+)\/">([\d\D]+?)<\/a><\/h3>/g) {
		download_douban_topic($1);
	}
}

sub download_douban_topic {
	my ($bid, $tid) = @_;
	my $html = get_url("https://www.douban.com/group/topic/$tid/");
	my ($uname, $nick, $uid, $uicon, $pub_time, $title, $content, $ups);
	my %users;
	if ($html =~ /<a href="https:\/\/www\.douban\.com\/people\/([\w\-]+)\/"><img class="pil" src="https:\/\/img\d\.doubanio\.com\/icon\/([\w\-]+)\.jpg" alt="([\d\D]+?)"\/><\/a>/) {
		($uid, $uicon, $uname) = ($1, $2, $3);
	}
	if (!defined($uid)) {
		print "https://www.douban.com/group/topic/$tid/ uid not defined\n";
		return 1;
	}
	if ($html =~ /<a href="https:\/\/www\.douban\.com\/people\/$uid\/">\Q$uname\E<\/a>\(([\d\D]+?)\)<\/span>/) {
		$nick =$1;
	}
	else {
		$nick = '';
	}
	if ($html =~ /<span class="color-green">([\d\- :]+)<\/span>/) {
		$pub_time = $1;
	}
	else {
		print "https://www.douban.com/group/topic/$tid/ pub_time not found\n";
		$pub_time = '2000-01-01';
	}
	if ($html =~ /<h1>\s*([\d\D]+?)\s*<\/h1>/) {
		$title = $1;
	}
	else {
		print "https://www.douban.com/group/topic/$tid/ title not found\n";
		$title = '';
	}
	if ($html =~ /<div class="topic-content">\s*([\d\D]+?)\s*<\/div>\s*<\/div>/) {
		$content = $1;
	}
	else {
		print "https://www.douban.com/group/topic/$tid/ content not found\n";
		$content = '';
	}
	$ups = 0;
	if ($html =~ /type=like#sep">(\d+)/) {
		$ups = $1;
	}
	$users{$uid} = [$uname, $nick, $uid, $uicon];
	my $continue = 0;
	print "topic\t$bid $tid $uid $ups $title $pub_time\n";
	if (execute_scalar("select count(*) from topic where tid = $tid") == 0) {
		$continue = 1;
		$db_conn->do("insert into topic(bid, tid, uid, ups, title, pub_time, content) values('$bid', $tid, '$uid', $ups, ".$db_conn->quote($title).", '$pub_time', ".$db_conn->quote($content).")");
	}
#	if (execute_scalar("select count(*) from topic where tid = $tid", $ptt_db_conn) == 0) {
#		$continue = 1;
#		$ptt_db_conn->do("insert into topic(bid, tid, uid, title, pub_time, content) values('$bid', $tid, '$uid', ".$db_conn->quote($title).", '$pub_time', ".$db_conn->quote($content).")");
#	}
	my $json_parser = new JSON;
	my $commentUps = [];
	if ($html =~ /var commentsVotes = '(.+?)'/) {
		$commentUps = $json_parser->decode($1);
	}
	my @replies = split('<li class="clearfix comment-item"', $html);
	for my $reply (@replies) {
		my ($cid, $uname, $nick, $uid, $uicon, $pub_time, $content);
		if ($reply =~ /data-cid="(\d+)"/) {
			$cid = $1;
		}
		else {
			next;
		}
		if ($reply =~ /<a href="https:\/\/www\.douban\.com\/people\/([\w\-]+)\/" class="">([\d\D]+?)<\/a>\(([\d\D]+?)\)/) {
			($uid, $uname, $nick) = ($1, $2, $3);
		}
		elsif ($reply =~ /<a href="https:\/\/www\.douban\.com\/people\/([\w\-]+)\/" class="">([\d\D]+?)<\/a>/) {
			($uid, $uname, $nick) = ($1, $2, '');
		}
		else {
			($uid, $uname, $nick) = ('', '', '');
			print "$tid $cid uid uname not found\n";
		}
		if ($reply =~ /<img class="pil" src="https:\/\/img\d\.doubanio\.com\/icon\/([\w\-\.]+?)"/) {
			$uicon = $1;
		}
		else {
			$uicon = '';
		}
		if ($reply =~ /<span class="pubtime">([\d\- :]+?)<\/span>/) {
			$pub_time = $1;
		}
		else {
			print "$tid $cid pub_time not found\n";
			$pub_time = '2000-01-01';
		}
		if ($reply =~ /<\/div>\s*([\d\D]+?)\s*<div class="operation_div" id="/) {
			$content = $1;
		}
		else {
			print "$tid $cid content not found\n";
			next;
		}
		my $ups = 0;
		$ups = $commentUps{"c$cid"} if (defined $commentUps{"c$cid"});
		$users{$uid} = [$uname, $nick, $uid, $uicon];
		print "comment\t$tid $cid $uid $pub_time $ups\n";
		$db_conn->do("replace into comment(tid, cid, uid, pub_time, ups, content) values($tid, $cid, '$uid', '$pub_time', $ups, ".$db_conn->quote($content).")");
#		$ptt_db_conn->do("replace into comment(tid, cid, uid, pub_time, ups, content) values($tid, $cid, '$uid', '$pub_time', $ups, ".$db_conn->quote($content).")");
#		print "replace into comment(tid, cid, uid, pub_time, ups, content) values($tid, $cid, $uid, '$pub_time', $ups, ".$db_conn->quote($content).")\n";
	}
	for $pa (values %users) {
		my ($uname, $nick, $uid, $uicon) = @$pa;
		print "user\t$uid $uname $nick\n";
		$db_conn->do("replace into user(uid, uname, nick, uicon) values('$uid', ".$db_conn->quote($uname).", ".$db_conn->quote($nick).", '$uicon')");
#		$ptt_db_conn->do("replace into user(uid, uname, nick, uicon) values('$uid', ".$db_conn->quote($uname).", ".$db_conn->quote($nick).", '$uicon')");
#		print "replace into user(uid, uname, nick, uicon) values($uid, ".$db_conn->quote($uname).", ".$db_conn->quote($nick).", '$uicon')\n";
	}
	return $continue;

}

1;
