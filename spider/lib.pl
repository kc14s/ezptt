use strict;
use warnings;
use LWP::UserAgent;
use Encode;
#use Encode::HanConvert;
use Digest::MD5;
#use Encode::HanConvert qw(trad simple);

my %config;
my @proxies;
my $db_conn;

sub load_config {
	open IN, $_[0] or die("open config from $_[0] failed\n");
	while (my $line = <IN>) {
		chop $line;
		my @arr = split("\t", $line);
		next if (scalar @arr != 2);
		$config{$arr[0]} = $arr[1];
	}
	close IN;
	return \%config;
}

sub load_proxy {
	open IN, $config{"pwd"}."/spider/data/base/proxy" or die("load proxy file failed\n");
	while (my $line = <IN>) {
		chop $line;
		my @arr = split("\t", $line);
		if (@arr == 2 && $arr[1] eq "1") {
			push @proxies, $arr[0];
		}
	}
	close IN;
	print STDERR "".(scalar @proxies)." proxies loaded\n";
	return @proxies;
}

sub init_db {
	my $db_server = $config{"db_server"};
	my $database = $config{"database"};
	my $user = $config{"user"};
	my $password = $config{"password"};
	$db_conn = DBI->connect("DBI:mysql:database=$database;host=$db_server", $user, $password);
	$db_conn->do("set names UTF8");
	$db_conn->do("SET time_zone = '+8:00'");
	$db_conn->do('SET LOW_PRIORITY_UPDATES=1');
	$ENV{'db_conn'} = $db_conn;
	return $db_conn;
}

sub get_url {
	my $url = $_[0];
	my $retry_count = 0;
	while (1) {
		sleep(1);
		print "fetching $url\n";
		my $ua = LWP::UserAgent->new;
		$ua->agent("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.4)Gecko/2008111217 Fedora/3.0.4-1.fc10 Firefox/3.0.5");
		my $proxy_idx = -1;
		if (index($url, "att.php") > 0) {
			$ua->timeout(60);
		}
		else {
			$ua->timeout(10);
		}
		if (0 && scalar @proxies <= 50) {
			load_proxy();
		}
		$proxy_idx = int(rand(scalar @proxies));
#		$ua->proxy('http', "http://".$proxies[$proxy_idx]);
		my $request = HTTP::Request->new(GET=>$url);
		$request->header('Accept-Encoding' => HTTP::Message::decodable);
		$request->header('Referer' => 'http://disp.cc/');
		my $response = $ua->request($request);
#               print $response->content."\n\n".$response->decoded_content(charset => 'none')."\n\n";
#               my $content = $response->content;
		my $content = $response->decoded_content(charset => 'none');
#               if (index($content, 'No further action is required on your part') >= 0 || index($content, 'We have limited resources') >= 0 || index($content, 'Access Denied') >= 0) {
		if (!defined($content)) {
			print STDERR "content is not defined\n";
		}
		elsif (index($content, 'No further action is required on your part') >= 0 || index($content, 'We have limited resources') >= 0) {
			print STDERR $proxies[$proxy_idx]." is banned\n";
		}
		elsif ($response->is_success) {
			if ($response->content_type( ) ne 'text/html') {       #二进制文件，验证大小
#				if (defined($response->content_length()) && $response->content_length() < 1024) {
#					print STDERR "file too small ".$response->content_length()." $url\n";
#                                       return "<tr><th>发生错误</th></tr>";
#				}
#				elsif ($response->content_length() <= length($content)) {        #"附件下载出错"图片的大小
					return $content;
#				}
#				else {
#					print STDERR "failure\tdownload $url incomplete\n";
#				}
			}
			elsif (1 || index($content, '<meta http-equiv="Content-Type" content="text/html; charset=gb2312"/>') > 0) {  #html文件，验证文本内容合法性
#				print $response->content_type( )."\n";
#				$content = encode('gbk', (decode('utf8', $content)));
#				$content =~ s/'/\'/g;
#				$content = $db_conn->quote($content);
#				$content = `echo '$content' | ./gbk_f2j`;
#				open OUT, '>/tmp/url';
#				print OUT $content;
#				close OUT;
#				$content = `cat /tmp/url | ./gbk_f2j`;
				return $content;
			}
		}
		print STDERR "failure\tget_url\t$url\t".$response->message."\t\n";
#               print STDERR "message: ".$response->message."\n";
		print STDERR "status: ".$response->status_line."\n";
		if (index($response->status_line, '404') == 0) {
			return '';
		}
		if ($response->status_line eq '200 OK' && index($url, "att.php") > 0) {
			print STDERR $content;
		}
		if (++$retry_count >= 3 && scalar @proxies < 200) {
			print STDERR "enough retries. give up $url\n";
			return "<tr><th>发生错误</th></tr>";
		}
	}
}

sub execute_scalar {    
	my ($sql, $conn) = @_;  
	$conn = $ENV{'db_conn'} if (!defined($conn));
	my $request = $conn->prepare($sql);
#	print "$sql\n";
	$request->execute();
	my ($result) = $request->fetchrow_array;
	if (defined($result)) {
		return $result;
	}                       
	return '0';     
} 

my %skipped_board_categories = (
'系统' => 0,
'站务' => 0,
'系統' => 0,
'站務' => 0,
'' => 0

);

sub get_all_boards {
	my @boards;
	for (my $i = 1; $i <= 300; $i += 20) {
		my $content = get_url($config{'board_list_url'}.$i);
		my @slices = split('<div class="row2">', $content);
		foreach my $slice (@slices) {
			if ($slice =~ /<span class="hide" id="bi\d+">(\d+)<\/span>[\d\D]+?<span class="L32">([\d\D]+?)<\/span>[\d\D]+?<a href="\.\.\/b\/(\w+)">([\d\D]+?)<\/a>/mg) {
				next if (defined($skipped_board_categories{$2}));
				push @boards, [$1, $2, $3, $4];	# bid, category, en_name, cn_name
				print "$1\t$2\t$3\t$4\n";
			}
		}
	}
	return @boards;
}

sub update_all_boards {
	foreach my $board (@_) {
		if (execute_scalar('select count(*) from board where id = '.$board->[0], $db_conn) == 0) {
			$db_conn->do("insert delayed into board(id, category, en_name, cn_name) values($board->[0], '$board->[1]', '$board->[2]', '$board->[3]')");
		}
		else {
			$db_conn->do("update board set category = '$board->[1]', en_name = '$board->[2]', cn_name = '$board->[3]' where id = $board->[0]");
		}
	}
}

sub get_datetime_str {
	my $ret = `date -d '$_[0] days' '+%F %T'`;
	chomp $ret;
	return $ret;
}

sub get_topics {
	my $board = $_[0];
#	my $url = "http://disp.cc/b/$board->[2]";
	my $url = "http://disp.cc/b/list.ajax.php?bi=$board->[0]&isPL=3&init=0";
	my $continue = 1;
	my @topics;
	my $seq_min = 0;
	my $yesterday = `date -d '1 day ago' '+%F %T'`;
	while ($continue) {
		my $content = get_url($url);
		my @slices = split('<div class="row2">', $content);
		foreach my $slice (@slices) {
			if ($slice =~ /<span class="L12" title="([\d\-: ]+)">[\d\D]+?onClick="return downLevel\((\d+)\);" href="\.\.\/b\/\d+\-([\w\-]+)" ><span class="titleColor">([\d\D]+?)<\/span>[\d\D]+?title="目前人氣">(\d*)<\/span>[\d\D]+? title="累積人氣: (\d+)">/) {
				print "$1\t$2\t$3\t$4\t$5\t$6\n";
				next if (index($4, '[公告]') >= 0);
				my $title = $4;
				$title = substr($4, 3) if (index($4, '■ ') == 0 || index($4, '□ ') == 0);
				$title =~ s/<[\w\s"=]+?>//g;
				my $current_popularity = ($5 eq '' ? 0 : $5);
				push @topics, [$1, $2, $3, $title, $current_popularity, $6];	# pub_time seq id title current_popularity  popularity
				if ($seq_min == 0 || $seq_min > $2) {
					$seq_min = $2;
				}
				if (1 && $1 lt $yesterday) {
					$continue = 0;
				}
				else {
				}

			}
		}
		$url = "http://disp.cc/b/list_list.ajax.php?bi=$board->[0]&pf=".($seq_min - 20)."&isPL=1";
		if ($seq_min < 20) {
			$continue = 0;
		}
	}
	return \@topics;
}

my $this_year = `date '+%Y'`;
chop $this_year;

sub download_topics {
	my ($board, $topics) = @_;
	foreach my $topic (@$topics) {
		my $url = "http://disp.cc/b/read.ajax.php?bi=$board->[0]&pi=-1&ti=$topic->[2]&pn=$topic->[1]&isPL=2";
#		$url = 'http://disp.cc/b/read.ajax.php?bi=62&pi=-1&ti=4ObI&pn=4023&isPL=2';
		my $content = get_url($url);
		my ($user, $nick, $body) = ('', '', '');
		my @attachments;
		#if ($content =~ /作者<\/span><span>\s*(\w+)\s*\(([\d\D]*?)\)<\/span>/) {
		if ($content =~ /<span itemprop="name">&nbsp;(\w+)\s*\(([\d\D]*?)\)<\/span>/) {
			$user = $1;
			$nick = $2;
			$db_conn->do("replace into `user`(user_id, nick) values('$user', '$nick')");
#			print "replace into `user`(user_id, nick) values('$user', '$nick')\n";
		}
		if ($content =~ /<hr color="#008080" \/>([\d\D]+?)<div style="clear:both">/) {
			my $html = $1;
			$body = $1;
			$body =~ s/<(?:[^>'"]*|(['"]).*?\1)*>//gs;
			while ($html =~ /data\-src="(http:\/\/[\w\.\/\-~!@#$%\^&\*\+\?:_=<>]+?)"/g) {
				my $img_url = $1;
				my $img_name = substr($img_url, rindex($img_url, '/') + 1);
				next if ($img_name =~ /images_all_250_ltn/);
				push @attachments, [$img_url, $img_name];
				print "att $img_name $img_url\n";
			}
			download_attachments($board, $topic, \@attachments);
		}
		if ($content eq '') {
			print STDERR "parse failed\t$url\n";
			next;
		}
		my $attachment = scalar @attachments == 0 ? 0 : 1;
		if (execute_scalar("select count(*) from topic where bid = $board->[0] and tid = '$topic->[2]'", $db_conn) == 0) {
			my $sql = "insert delayed into topic(bid, tid, author, title, content, pub_time, current_popularity, popularity, attachment) values ($board->[0], '$topic->[2]', '$user', ".$db_conn->quote($topic->[3]).", ".$db_conn->quote($body).", '$topic->[0]', $topic->[4], $topic->[5], ".(scalar @attachments == 0 ? 0 : 1).")";
			$db_conn->do($sql);
		}
		else {
			my $sql = "update topic set author = '$user', title = ".$db_conn->quote($topic->[3]).", content = ".$db_conn->quote($body).", current_popularity = $topic->[4], popularity = $topic->[5], attachment = $attachment where bid = $board->[0] and tid = '$topic->[2]'";
			$db_conn->do($sql);
		}
		print "topic\t$user\t$nick\t$topic->[3]\t$topic->[4]\t$topic->[5]\n";
		my @articles;
		my @replies = split('<div class="push_row">', $content);
		foreach my $reply (@replies) {
			if ($reply =~ /<span class="fg133">(\w+)<\/span><\/span><span class="fg033">:([\d\D]+?)\s*?<\/span> ([\d:\/ ]+
						)/) {
				my ($user, $reply_content, $reply_time) = ($1, $db_conn->quote($2), $3);
				$reply_content = substr($reply_content, 1) if (index($reply_content, ':') == 0);
				$reply_time =~ s/\//\-/;
				$reply_time = "$this_year-$reply_time";
				push @articles, [$user, $reply_time, $reply_content];
			}
		}
		@replies = split('<div class="pushLine"', $content);
		foreach my $reply (@replies) {
			my $reply_time = $1 if ($reply =~ /<span class="pushInfo">[\d\D]+?(2[\d\- :]+?)<\/span>/);
			my $user = $1 if ($reply =~ /<div class="pushAuthor"><a href="\.\.\/user\/(\w+)">/);
			my $reply_content = $1 if ($reply =~ /<div class="pushContent\s*"\s*>\s*<pre>([\d\D]*?)<\/pre>/);
			next if (!defined($reply_time) || !defined($user) || !defined($reply_content));
			$reply_content = $db_conn->quote($reply_content);
			push @articles, [$user, $reply_time, $reply_content];
		}
		@articles = sort {$a->[1] cmp $b->[1]} @articles;
		foreach my $article (@articles) {
			print "reply\t$article->[0]\t$article->[1]\t$article->[2]\n";
			save_reply($board, $topic, $article->[0], $article->[1], $article->[2]);
		}
	}
}

sub save_reply {
	my ($board, $topic, $author, $reply_time, $content) = @_;
	my ($bid, $tid) = ($board->[0], $topic->[2]);
	if (execute_scalar("select count(*) from reply where bid = $bid and tid = '$tid' and author = '$author' and reply_time = '$reply_time'", $db_conn) == 0) {
		my $sql = "insert delayed into reply(bid, tid, author, reply_time, content) values($bid, '$tid', '$author', '$reply_time', $content)";
		$db_conn->do($sql);
	}
}



sub download_attachments {
	my ($board, $topic, $attachments) = @_;
	foreach my $attachment (@$attachments) {
#		my $ext_name = substr($attachment->[1], rindex($attachment->[1], '.') + 1);
		my ($bid, $tid, $file_name) = ($board->[0], $topic->[2], $attachment->[1]);
		if (index($attachment->[0], 'http://www.youtube.com/') == 0) {
			next;
		}
		if (index($attachment->[1], '?') >= 0) {
			$attachment->[1] = Digest::MD5->new->add($attachment->[1])->hexdigest.'.jpg';
		}
		if (rindex($attachment->[1], '.') == -1 || length($attachment->[1]) - rindex($attachment->[1], '.') > 5) {
			$attachment->[1] .= '.jpg';
		}
		my $att_path = $config{"pwd"}."/spider/att_ori/$board->[0].$topic->[2].$attachment->[1]";
		next if (-e $att_path);
		my $att_content = get_url($attachment->[0]);
		if (`file $att_path | grep HTML | wc -l` > 0) {
			`rm $att_path`;
			next;
		}
		next if (length($att_content) < 1024 * 10 || length($att_content) == 48373 || length($att_content) == 17230);
		open OUT, ">$att_path";
		binmode(OUT);
		print OUT $att_content;
		close OUT;
#		system("convert -resize 200 $att_path ".$config{"pwd"}."/spider/att_thumb/$board->[0].$topic->[2].$attachment->[1] > /dev/null");
#		next if (! -e $att_path || ! -e $config{"pwd"}."/spider/att_thumb/$board->[0].$topic->[2].$attachment->[1]");
		my $sql = "insert delayed into attachment(bid, tid, file_name) values($board->[0], '$topic->[2]', '$attachment->[1]')";
		$db_conn->do($sql);
	}
}

sub gen_homepage {
	my $yesterday = get_datetime_str(-1);
	my (%boards, %titles, @boards);
	open OUT, ">../front/data/index";
	my $sql = "select bid, en_name, cn_name, tid, title, author, category, attachment from board, topic where board.id = topic.bid and pub_time > '$yesterday' order by popularity / (unix_timestamp(now()) - unix_timestamp(pub_time)) desc limit 150";
	print "$sql\n";
	my $request = $db_conn->prepare($sql);
	$request->execute;
	while (my ($bid, $en_name, $cn_name, $tid, $title, $author, $category, $attachment) = $request->fetchrow_array) {
		next if (defined($titles{$title}));
		$titles{$title} = 0;
		next if (++$boards{$bid} > 10);
		my @attachments = ();
		if ($attachment) {
			my $req = $db_conn->prepare("select file_name from attachment where bid = $bid and tid = '$tid'");
			$req->execute;
			while (my ($file_name) = $req->fetchrow_array) {
				if (-e "att_ori/$bid.$tid.$file_name") {
					push @attachments, $file_name;
					last if (@attachments > 2);
				}
			}
		}
		print OUT "$category\t$en_name\t$cn_name\t$bid\t$tid\t$title\t$author\t".join("\t",@attachments)."\n";
	}
	close OUT;
}

my %beauty_bids = (
62 => 1,		#Beauty
993 => 1,		#goodBeauty
774 => 1,		#JD
18 => 2,		#ott
0 => 0
);

sub gen_beauty {
	open OUT, ">../front/data/beauty.disp";
	my $sql = 'select topic.bid, topic.tid, title, file_name from topic, attachment where topic.bid in ('.join(',', keys(%beauty_bids)).') and attachment = 1 and topic.bid = attachment.bid and topic.tid = attachment.tid group by topic.bid, topic.tid';
	my $request = $db_conn->prepare($sql);
	$request->execute;
	my %file_names;
	while (my ($bid, $tid, $title, $file_name) = $request->fetchrow_array) {
		next if (defined($file_names{$file_name}));
		next if (! -e "att_ori/$bid.$tid.$file_name");
		next if ($beauty_bids{$bid} == 2 && index($title, '正妹') < 0);
		$file_names{$file_name} = 0;
		print OUT "$bid\t$tid\t$title\t$file_name\n";
	}
	close OUT;
}

1;
