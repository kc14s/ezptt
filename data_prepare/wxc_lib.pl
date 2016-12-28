use DBI;
my $db;

sub get_wxc_boards {
	my %boards;
	$db = shift;
	my $url = 'http://bbs.wenxuecity.com/catalog/';
	my $html = get_url($url);
	my @arr = split('<h2', $html);
	for (my $i = 2; $i < @arr; ++$i) {
		my $item = $arr[$i];
		my $group_name = $1 if ($item =~ />([\d\D]+?)<\/h2>/);
		while ($item =~ /<li><a href="\/(\w+)\/">([\d\D]+?)<\/a><\/li>/g) {
			$boards{$1} = [$1, $2, $group_name];
#			$db->do("replace into board(en_name, cn_name, group_name) values('$1', ".$db->quote($2).", '$group_name')");
			print "$group_name\t$1\t$2\n";
		}
	}
	my @boards = values %boards;
	return \@boards;
}

sub get_wxc_topic {
	my $board = shift;
	my ($board_en_name, $board_cn_name, $board_group_name) = @$board;
	my $url = "http://bbs.wenxuecity.com/$board_en_name/?page=1";
	my $list_html = get_url($url);
	my $last_page = 500;
	while ($list_html =~ /\?page=(\d+)"/g) {
		$last_page = $1;
	}
#	for (my $page = $last_page; $page >= 1; --$page) {
	my $first_page = execute_scalar("select page from board where en_name = '$board_en_name'");
	for (my $page = $first_page; $page <= $last_page; ++$page) {
		$db->do("use wxc");
		$db->do("set names utf8");
		$db->do("update board set page = $page where en_name = '$board_en_name'");
		if ($board_en_name eq 'travel' && $page >= $last_page - 1) {
			last;
		}
		my $continue = 0;
		my $url = "http://bbs.wenxuecity.com/$board_en_name/?page=$page";
		my $list_html = get_url($url);
		my $last_page = 500;
		while ($list_html =~ /\?page=(\d+)"/g) {
			$last_page = $1;
		}
		if ($page > $last_page) {
			print "exit $board_en_name last page $last_page\n";
			last;
		}
		my @arr = split(/<div class="(odd|even)">/, $list_html);
		my $found = 0;
		foreach my $item (@arr) {
			next if (length($item) < 20);
			my @articles;
		#	while ($item =~ /<a href="\.\/(\d+)\.html" class="post" title="[\d\D]+?">\s*([\d\D]+?)\s*<\/a>.+?act=profile&cid=[^"]+">([\d\D]+?)<\/a>/g) {
			while ($item =~ /<a href="\.\/(\d+)\.html" class="post" title="[\d\D]+?">\s*([\d\D]+?)\s*<\/a>[\d\D]+?act=profile&cid=[^"]+">([\d\D]+?)<\/a>[\d\D]+?(\d{2})\/(\d{2})\/(\d{4})&nbsp;\s*([\d:]+)/g) {
				my ($aid, $title, $author, $month, $day, $year, $time) = ($1, $2, $3, $4, $5, $6, $7);
				push @articles, [$aid, $title, $author, "$year-$month-$day $time"];
			}
			next if (@articles <= 0);
			my $tid = $articles[0]->[0];
			if (1 || execute_scalar("select count(*) from topic where board_en_name = '$board_en_name' and tid = $tid") == 0) {
				get_wxc_topic_content($board_en_name, $tid, $articles[0]->[1], $articles[0]->[2], scalar @articles - 1, $articles[0]->[3]);
				$continue = 1;
			}
			else {
				if (execute_scalar("select count(*) from topic where board_en_name = '$board_en_name' and tid = $tid and pub_time = '20010101'") > 0) {
					$db->do("update topic set pub_time = '$articles[0]->[3]' where board_en_name = '$board_en_name' and tid = $tid");
					$continue = 1;
				}
			}
			print "topic $board_en_name $tid $articles[0]->[1] $articles[0]->[2] $articles[0]->[3]\n";
			for (my $i = 1; $i < @articles; ++$i) {
				my $reply = $articles[$i];
				my ($rid, $title, $author, $pub_time) = @$reply;
				print "reply $rid $title $author $pub_time\n";
				if (execute_scalar("select count(*) from reply where board_en_name = '$board_en_name' and tid = $tid and rid = $rid") > 0) {
					$db->do("update reply set title = ".$db->quote($title).", author = ".$db->quote($author).", pub_time = '$pub_time' where board_en_name = '$board_en_name' and tid = $tid and rid = $rid");
				}
				else {
					$db->do("insert into reply(board_en_name, tid, rid, title, author, pub_time) values('$board_en_name', $tid, $rid, ".$db->quote($title).", ".$db->quote($author).", '$pub_time')");
				}
			}
			$found = 1;
		}
		last if (!$found);
#		last if (!$continue);
	}
	print "exit board $board_en_name\n";
}

sub get_wxc_topic_content {
	my ($board_en_name, $tid, $title, $author, $hot, $pub_time) = @_;
	my $html = get_url("http://bbs.wenxuecity.com/$board_en_name/$tid.html");
	my $content = '';
	$content = $1 if ($html =~ /<div id="msgbodyContent">\s*([\d\D]+?)\s*<\/div>/);
	$db->do("replace into topic(board_en_name, tid, title, author, hot, content, pub_time) values('$board_en_name', $tid, ".$db->quote($title).", ".$db->quote($author).", $hot, ".$db->quote($content).", '$pub_time')");
}



1;
