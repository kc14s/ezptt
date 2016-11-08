#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my %names;
my $url = 'http://www.dmm.co.jp/digital/videoa/-/maker/';
my $html = get_url($url);
my @arr = split('人気独占配信AVメーカー', $html);
my @items = split('<div class="d-unit"><div class="d-boxpicdata d-smalltmb">', $arr[1]);
foreach my $item (@items) {
	my $id = $1 if ($item =~ /article=maker\/id=(\d+)\//);
	next if (!defined($id));
	my $logo = $1 if ($item =~ /maker_logo\/([\w\-]+)\.gif/);
	my $name = $1 if ($item =~ />([^>]+?)<\/span><\/a>/);
	print "0 $id $logo $name\n";
	$db_conn->do("replace into company(id, name, logo, source) values($id, '$name', '$logo', 0)");
	$names{$name} = 0
}

$url = 'http://www.mgstage.com/ppv/makers.php?id=osusume';
$html = get_url($url);
while ($html =~ /<img class="left" src="\/img\/pc\/(\w+)\.gif" alt="([\d\D]+?)">/g) {
	my ($logo, $name) = ($1, $2);
	next if (defined($names{$name}));
	next if ($name eq 'PRESTIGE' || $name eq 'real' || $name eq 'FULL SAIL' || $name eq 'ビックモーカル' || $name eq 'ロケット');
	next if (execute_scalar("select count(*) from company where name = '$name'") > 0);
	print "1 $logo $name\n";
	$db_conn->do("replace into company(name, logo, source) values('$name', '$logo', 1)");
	$names{$name} = 0
}

$url = 'http://www.aventertainments.com/studiolists.aspx?&Dept_ID=29&languageID=2';
$html = get_url($url);
@items = split('<table width="96%" border="0" cellspacing="0" cellpadding="0">', $html);
for (my $i = 1; $i < @items; ++$i) {
	my $item = $items[$i];
	my $logo = $1 if ($item =~ /img\/studio_ic\/([\w\-\.]+)"/);
	my ($id, $name) = ($1, $2) if ($item =~ /<a href="http:\/\/www\.aventertainments\.com\/studio_products\.aspx\?StudioID=(\d+)&Dept_ID=29&languageID=2">([\d\D]+?)<\/a>/);
	next if (!defined($id));
	next if (defined($names{$name}));
	if (!defined($logo)) {
		print "2 $id no logo $name\n";
	}
	print "2 $id $logo $name\n";
	$db_conn->do("replace into company(id, logo, name, source) values($id, '$logo', '$name', 2)");
	$names{$name} = 0
}
