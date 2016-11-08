#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my $url = 'http://www.dmm.co.jp/digital/videoa/-/genre/';
my $html = get_url($url);
my @items = split('class="d-area area-list">', $html);
foreach my $item (@items) {
	my $category = $1 if ($item =~ /<div class="d\-capt">([\d\D]+?)<\/div>/);
	next if (!defined($category) || $category eq 'おすすめジャンル');
	print "$category\n";
	while ($item =~ /<li><a href="http:\/\/www\.dmm\.co\.jp\/digital\/videoa\/\-\/list\/=\/article=keyword\/id=(\d+)\/">([\d\D]+?)<\/a><\/li>/g) {
		my ($id, $genre) = ($1, $2);
		next if (index($genre, '<img') >= 0);
		next if (execute_scalar("select count(*) from genre_list where id = $id"));
		$db_conn->do("insert into genre_list(id, category, genre, featured) values($id, '$category', '$genre', 0)");
	}
}

