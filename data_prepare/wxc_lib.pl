use DBI;
my $db;

sub get_wxc_boards {
	my %boards;
	$db = shift;
#	my $db = $ENV{'db_conn'};
	my $url = 'http://bbs.wenxuecity.com/catalog/';
	my $html = get_url($url);
	my @arr = split('<h2', $html);
	for (my $i = 2; $i < @arr; ++$i) {
		my $item = $arr[$i];
		my $group_name = $1 if ($item =~ />([\d\D]+?)<\/h2>/);
		while ($item =~ /<li><a href="\/(\w+)\/">([\d\D]+?)<\/a><\/li>/g) {
			$boards{$1} = [$1, $2, $group_name];
			$db->do("replace into board(en_name, cn_name, group_name) values('$1', ".$db->quote($2).", '$group_name')");
			print "$group_name\t$1\t$2\n";
		}
	}
	my @boards = values %boards;
	return \@boards;
}

sub get_wxc_topic {
	my $board = shift;
	my ($board_en_name, $board_cn_name, $board_group_name) = @$board;
	for (my $page = 1; ; ++$page) {
		my $continue = 0;
		my $url = "http://bbs.wenxuecity.com/$board_en_name/?page=$page";
		my $list_html = get_url($url);
		my @arr = split(/<div class="(odd|even)">/, $list_html);
		foreach my $item (@arr) {
			next if (length($item) < 20);
			my @articles;
		#	while ($item =~ /<a href="\.\/(\d+)\.html" class="post" title="[\d\D]+?">\s*([\d\D]+?)\s*<\/a>.+?act=profile&cid=[^"]+">([\d\D]+?)<\/a>/g) {
			while ($item =~ /<a href="\.\/(\d+)\.html" class="post" title="[\d\D]+?">\s*([\d\D]+?)\s*<\/a>[\d\D]+?act=profile&cid=[^"]+">([\d\D]+?)<\/a>/g) {
				my ($aid, $title, $author) = ($1, $2, $3);
				push @articles, [$aid, $title, $author];
			}
			next if (@articles <= 0);
			my $tid = $articles[0]->[0];
			if (execute_scalar("select count(*) from topic where tid = $tid") == 0) {
				get_wxc_topic_content($board_en_name, $tid, $articles[0]->[1], $articles[0]->[2], scalar @articles - 1);
				$continue = 1;
			}
			print "topic $en_name $tid $$articles[0]->[1] $articles[0]->[2]\n";
			for (my $i = 1; $i < @articles; ++$i) {
				my ($rid, $title, $author) = @$articles[$i];
				$db->do("replace into reply(board_en_name, tid, rid, title, author) values('$board_en_name', $tid, $rid, ".$db->quote($title).", '$author')");
				print "reply $rid $title $author\n";
			}
		}
		last if (!$continue);

	}
}

sub get_wxc_topic_content {
	my ($board_en_name, $tid, $title, $author, $hot) = @_;
	my $html = get_url("http://bbs.wenxuecity.com/$board_en_name/$tid.html");
	my $content = $1 if ($html =~ /<div id="msgbodyContent">\s*([\d\D]+?)\s*<\/div>/);
	$db->do("replace into topic(board_en_name, tid, title, author, hot, content) values('$board_en_name', $tid, ".$db->quote($title).", '$author', $hot, ".$db->quote($content).")");
}



1;
