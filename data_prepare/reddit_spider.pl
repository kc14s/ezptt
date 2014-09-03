#!/usr/bin/perl -w
use strict;
use DBI;
use JSON;
require('config.pl');
require('reddit_lib.pl');

my $now = `date +'%F %T'`;
chomp $now;
print "spider starts at $now\n";
my $db_conn = init_db();
$db_conn->do("use $ENV{'database_reddit'}");

#my $after = execute_scalar("select ");
my $after;
my @replies;
my $page = 0;
while (1) {
	my $topic_list_url = "http://www.reddit.com/new.json?limit=100";
	$topic_list_url .= "&after=$after" if (defined($after));
	my $topic_list_json = fetch_reddit($topic_list_url);
	my $json_parser = new JSON;
	my $json = $json_parser->decode($topic_list_json);
	my $var_name = '$list_json';
#	print Data::Dumper->Dump([$json], [$var_name]);
	$after = $json->{'data'}->{'after'};
	foreach my $topic (@{$json->{'data'}->{'children'}}) {
		my $id36 = $topic->{'data'}->{'id'};
		my $id = id36_to_int($id36);
		my $subreddit = $topic->{'data'}->{'subreddit'};
		my $domain = $topic->{'data'}->{'domain'};
		my $ups = $topic->{'data'}->{'ups'};
		my $downs = $topic->{'data'}->{'downs'};
		my $author = $topic->{'data'}->{'author'};
		my $created = $topic->{'data'}->{'created'};
		my $url = $topic->{'data'}->{'url'};
		my $title = $topic->{'data'}->{'title'};
		my $selftext = $topic->{'data'}->{'selftext'};
		my $good = is_good($domain, $ups);
		$created = get_datetime_string($created);
		print "topic $id $subreddit $domain $ups $good $author $created $title\n";
		my $reply_url = "http://www.reddit.com/comments/$id36.json?sort=hot";
		my $reply_html = fetch_reddit($reply_url);
		my $reply = $json_parser->decode($reply_html);
		@replies = ();
#		print Data::Dumper->Dump([$reply], [$var_name]);
		if (defined($reply->[1])) {
			parse_reply($reply->[1]->{'data'}->{'children'});
		}
		$url = $db_conn->quote($url);
		$title = $db_conn->quote($title);
		$selftext = $db_conn->quote($selftext);
		$reply_html = $db_conn->quote($reply_html);
		$db_conn->do("replace into topic(id, subreddit, domain, title, ups, downs, author, url, selftext, created, good, json) values($id, '$subreddit', '$domain', $title, $ups, $downs, '$author', $url, $selftext, '$created', $good, $reply_html)");
		foreach my $reply (@replies) {
			my ($reply_id, $author, $ups, $downs, $body, $created) = @$reply;
			$body = $db_conn->quote($body);
			$db_conn->do("replace into reply(tid, id, author, ups, downs, body, created) values($id, '$reply_id', '$author', $ups, $downs, $body, '$created')");
		}
	}
	if (++$page >= 100) {
		last;
	}
}

sub parse_reply {
	my $pa = $_[0];
	foreach my $reply (@$pa) {
		next if ($reply->{'kind'} ne 't1');
		my $id = $reply->{'data'}->{'id'};
		my $author = $reply->{'data'}->{'author'};
		my $ups = $reply->{'data'}->{'ups'};
		my $downs = $reply->{'data'}->{'downs'};
		my $body = $reply->{'data'}->{'body'};
		my $created = $reply->{'data'}->{'created'};
		$created = get_datetime_string($created);
		print "reply $id $author $ups $downs $created\n";
		push @replies, [$id, $author, $ups, $downs, $body, $created];
	}
}
