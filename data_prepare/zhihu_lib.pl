#!/usr/bin/perl -w
use strict;
no warnings 'utf8';

our $db_conn;
sub get_groups {
        my $url = 'http://www.zhihu.com/topics';
        my $html = get_url($url);
        my @groups = ();
        while ($html =~ /<li data-id="(\d+)"><a href="#([\d\D]+?)">/g) {
                push @groups, [$1, $2];
		print "$1, $2\n";
                $db_conn->do("replace into board values($1, '$2')");
        }
        return \@groups;
}
my $json_parser = new JSON;

sub get_boards {
        my $group = $_[0];
        my ($group_id, $group_name) = @$group;
        my $url = 'http://www.zhihu.com/node/TopicsPlazzaListV2';
        my @boards;
        for (my $offset = 0; ; $offset += 20) {
                my %form = (
                                'method' => 'next',
                                'params' => '{"topic_id":'.$group_id.',"offset":'.$offset.',"hash_id":"9fc675d1e89601361f31576d9b2724dd"}',
                                '_xsrf' => '777e3e3c5616ac059706b4d409203647'
                           );
                my $html = post_url($url, \%form);
                my $json = $json_parser->decode($html);
                my $pa = $json->{'msg'};
                foreach my $item (@$pa) {
                        my $img = $1 if ($item =~ /\.zhimg\.com\/(\w+?)_xs\.jpg/);
                        my $board_name = $1 if ($item =~ /<strong>([\d\D]+?)<\/strong>/);
                        my $board_id = $1 if ($item =~ /\/topic\/(\d+)/);
#                       print "$board_id\t$board_name\t$img\n";
                        push @boards, [$group_id, $group_name, $board_id, $board_name];
                        $db_conn->do("replace into sub_board values($board_id, $group_id, ".$db_conn->quote($board_name).")");
                }
                last if (@$pa < 20);
#                last;
        }
        return \@boards;
}

sub get_zhihu_questions {
        my $board = $_[0];
        my ($group_id, $group_name, $board_id, $board_name) = @$board;
        my $url = "http://www.zhihu.com/topic/$board_id/newest";
        my $time = time();
        my %questions;
        while (1) {
                my %form = (
                                start => 0,
                                offset => $time.'.0',
                                _xsrf => '777e3e3c5616ac059706b4d409203647'
                           );
                my $html = post_url($url, \%form);
                my $json = $json_parser->decode($html);
                my @arr = split('http://schema.org/Question', $json->{'msg'}->[1]);
                foreach my $item (@arr) {
                        $time = $1 if ($item =~ /data-timestamp="(\d+?)000"/);
                        my ($sb_id, $sb_name) = (0, '');
                        ($sb_id, $sb_name) = ($1, $2) if ($item =~ /href="\/topic\/(\d+)">([\d\D]+?)<\/a>/);
                        my ($qid, $title) = ($1, $2) if ($item =~ / href="\/question\/(\d+)">([\d\D]+?)<\/a>/);
                        next if (!defined($qid));
                        print "question\t$time\t$sb_id\t$sb_name\t$qid\t$title\n";
                        $questions{$qid} = [$group_id, $group_name, $board_id, $board_name, $time, $sb_id, $sb_name, $qid, $title];
                }
                last if (@arr < 20);
                last;
        }
        return \%questions;
}

sub get_zhihu_question {
        my $question = $_[0];
        my ($group_id, $group_name, $board_id, $board_name, $time, $sb_id, $sb_name, $qid, $title) = @$question;
        my $url = "http://www.zhihu.com/question/$qid";
        my $html = get_url($url);
#       print $html;
#       return;
        my $q_title = $1 if ($html =~ /<h2 class="zm-item-title zm-editable-content">\s*([\d\D]+?)\s*<\/h2>/);
        my $q_content = $1 if ($html =~ /<div class="zm-editable-content">\s*([\d\D]*?)\s*<\/div>/);
        print "question $q_title $q_content\n";
	$db_conn->do("replace into question(qid, bid, sbid, title, content) values($qid, $board_id, $sb_id, ".$db_conn->quote($q_title).", ".$db_conn->quote($q_content).")");
        my @arr = split('class="zm-item-answer "', $html);
        foreach my $item (@arr) {
                my $ups = $1 if ($item =~ /<span class="count">([\-\d]+)<\/span>/);
                my $aid = $1 if ($item =~ /name="answer-(\d+)"/);
                my ($author, $nick) = ('', '');
#($author, $nick) = ($1, $2) if ($item =~ /href="\/people\/[\w\-]+?">([^<]+?)<\/a>，<strong title="([\d\D]+?)"/);
                $author = $1 if ($item =~ /href="\/people\/[\w\-]+?">([^<]+?)<\/a>/);
		$nick = $1 if ($item =~ /<strong title="([\d\D]+?)"/g);
                my $pub_time = $1 if ($item =~ /data-created="(\d+)"/);
                if (defined($pub_time) && $pub_time =~ /:/) {
                        print "pub_time malform\n";
                }
                else {
                        $pub_time = get_datetime_string($pub_time);
                }
                my $content = '';
                $content = $1 if ($item =~ /<div class="[\w\-\s]*zm\-editable\-content clearfix">\s*([\d\D]+?)\s*<\/div>/);
                my $comment_num = 0;
                $comment_num = $1 if ($item =~ /<i class="z-icon-comment"><\/i>(\d+)/);
                next if (!defined($aid));
                $db_conn->do("replace into answer(aid, bid, qid, ups, author, nick, pub_date, content) values($aid, $board_id, $qid, $ups, '$author', '$nick', '$pub_time', ".$db_conn->quote($content).")");
                print "answer\t$aid $ups $comment_num $author $nick $pub_time ".substr($content, 0, 20)."\n";
#               print "item $item\n";
#               exit;
                next if ($comment_num == 0);
		next if (execute_scalar("select count(*) from comment where aid = $aid") >= $comment_num);
                my $comment_url = "http://www.zhihu.com/node/AnswerCommentListV2?params=%7B%22answer_id%22%3A%22$aid%22%7D";
                my $comment_html = get_url($comment_url);
#               print $comment_html;
#               next;
                my @comments = split('zm-item-comment', $comment_html);
                foreach my $comment (@comments) {
                        my $comment_id = $1 if ($comment =~ /name="comment\-(\d+)"/);
                        my $commenter = '';
                        $commenter = $1 if ($comment =~ /class="zg\-link" title="([\d\D]+?)"/);
                        my $comment_content = $1 if ($comment =~ /<div class="zm-comment-content">\s*([\d\D]+?)\s*<\/div>/);
                        my $comment_ups = $1 if ($comment =~ /<em>(\d+)<\/em>/);
                        my $comment_date = '2000-01-01';
                        if ($comment =~ /<span class="date">([\d\-]+)/) {
                                $comment_date = $1;
                        }
                        elsif ($comment =~ /<span class="date">昨天\s*([\d:]+)/) {
                                $comment_date = get_date_str(-1);
                        }
                        next if (!defined($comment_id));
#                       next if (execute_scalar("select count(*) from comment where cid = $comment_id") > 0);
                        print "comment $comment_id $commenter $comment_ups $comment_date $comment_content\n";
#                       print $comment;
#                       exit;
                        $db_conn->do("replace into comment(cid, aid, author, ups, pub_date, content) values($comment_id, $aid, '$commenter', $comment_ups, '$comment_date', ".$db_conn->quote($comment_content).")");
                }
        }
}

1;
