#!/usr/bin/perl -w
use strict;
use DBI;
use HTML::Element;
use HTML::TreeBuilder;

my $db_conn;

sub fetch_boards {
	my $url = shift;
	my @boards;
	my %en_names;
	my $html = get_url($url);
	while ($html =~ /<a desc="标题" href="http:\/\/bbs\.tianya\.cn\/list\-(\w+)\-1\.shtml" title="([\d\D]+?)" target=_blank id="cms_fragment_(\d+)_(\d+)_title"/g) {
		$en_names{$1} = 0;
		push @boards, [$3, $4, $1, $2];		#group id, board id, en name, cn name
		#print "$3, $4, $1, $2\n";
	}
	$url = 'http://bbs.tianya.cn/';
	$html = get_url($url);
	while ($html =~ /href="\/list-(\w+)-1\.shtml">([\d\D]+?)<\/a>/g) {
		if (!defined($en_names{$1})) {
			$en_names{$1} = 0;
			push @boards, [0, 0, $1, $2];
			#print "$1, $2\n";
		}
	}
	$db_conn = $ENV{'db_conn'};
	foreach (@boards) {
		my ($group_id, $board_id, $en_name, $cn_name) = @$_;
		$db_conn->do("replace into board(group_id, en_name, cn_name) values($group_id, '$en_name', '$cn_name')");
		print "$group_id, $en_name, $cn_name\n";
	}
	return @boards;
}

sub fetch_threads {
	my ($group_id, $board_id, $en_name, $cn_name) = @$_;
	return if ($en_name eq 'free');
	return if ($en_name eq '828');
	my @threads;
	my $url = "http://bbs.tianya.cn/list-$en_name-1.shtml";
	my $found_new = 0;
	my $page = 0;
	while (1) {
		my $html = get_url($url);
		sleep(1);
		my @items = split('<td class="td-title faceblue">', $html);
		foreach (@items) {
			my ($thread_id, $title, $user_id, $user_name, $click, $reply, $pub_time);
			if (/href="\/post-\w+-(\d+)-1\.shtml" title="[\d\D]*?" target="_blank">\s*([\d\D]+?)\s*</) {
				$thread_id = $1;
				$title = $2;
				$title =~ s/<.+>//g;
			}
			if (/<a href="http:\/\/www\.tianya\.cn\/(\d+)" target="_blank" class="author">([\d\D]+?)<\/a>/) {
				$user_id = $1;
				$user_name = $2;
			}
			if (/<td>(\d+)<\/td>\s*<td>(\d+)<\/td>/) {
				$click = $1;
				$reply = $2;
			}
			$pub_time = "$1:00" if (/<td title="([\d\-\s:]+)">[\d\-\s:]+<\/td>/);
			next if (!defined($thread_id) || !defined($title) || !defined($user_id) || !defined($user_name) || !defined($click) || !defined($reply) || !defined($pub_time));
			print "thread $thread_id, $title, $user_id, $user_name, $click, $reply, $pub_time\n";
			my $title_quoted = $db_conn->quote($title);
			my $user_name_quoted = $db_conn->quote($user_name);
			#if (execute_scalar("select count(*) from thread where tid = $thread_id and en_name = '$en_name'") == 0) {
			if (execute_scalar("select count(*) from thread where tid = $thread_id") == 0) {
				$db_conn->do("insert into thread(en_name, tid, title, uid, click, reply, pub_time) values('$en_name', $thread_id, $title_quoted, $user_id, $click, $reply, '$pub_time')");
				$found_new = 1;
			}
			else {
				$db_conn->do("update thread set title = $title_quoted, uid = $user_id, click = $click, reply = $reply, pub_time = '$pub_time' where tid = $thread_id and en_name = '$en_name'");
			}
			if (execute_scalar("select count(*) from reply where en_name = '$en_name' and tid = $thread_id") < 20) {
			#if (execute_scalar("select count(*) from reply where tid = $thread_id") < 20) {
				$found_new |= fetch_reply($en_name, $thread_id);
			}
		}
		if ($found_new == 0) {
			last;
		}
		if ($html =~ /href="\/list\.jsp\?item=\w+&nextid=(\d+)"/) {
			$url = "http://bbs.tianya.cn/list.jsp?item=$en_name&nextid=$1";
		}
		else {
			last;
		}
		if (++$page == 10) {
			last;
		}
	}
}

sub fetch_reply {
	my ($en_name, $tid) = @_;
	my $found_new = 0;
	my $html = get_url("http://bbs.tianya.cn/post-$en_name-$tid-1.shtml");
	my @items = split('<div class="atl-item"', $html);
	foreach (@items) {
		my ($uid, $uname, $pub_time, $content);
		if (/uid="(\d+)" uname="([\d\D]+?)"/) {
			$uid = $1;
			$uname = $2;
		}
		$pub_time = $1 if (/<span>时间：([\d\-:\s]+)<\/span>/);
		if (/<div class="bbs-content">\s*([\d\D]+?)\s<\/div>/) {
			$content = $1;
			$content =~ s/<br>/line_breaker/g;
			$content =~ s/<[\d\D]+?>//g;
			my @lines = split('line_breaker', $content);
			$content = '';
			foreach (@lines) {
				if (/^\s*$/) {}
				else {
					$content .= "$_<br>\n";
				}
			}
		}
		next if (!defined($uid) || !defined($uname) || !defined($pub_time) || !defined($content));
		print "reply $uid, $uname, $pub_time\n";
		if (execute_scalar("select count(*) from reply where en_name = '$en_name' and tid = $tid and uid = $uid and pub_time = '$pub_time'") == 0) {
			my $content_quoted = $db_conn->quote($content);
			$db_conn->do("insert into reply(en_name, tid, uid, pub_time, content) values('$en_name', $tid, $uid, '$pub_time', $content_quoted)");
			$db_conn->do("replace into user values($uid, '$uname')");
			$found_new = 1;
		}
	}
	return $found_new;
}

1;
