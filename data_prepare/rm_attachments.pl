#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');
require('config.pl');

my $db_conn = init_db();

my %beauties;
open IN, '../front/data/beauty';
while (<IN>) {
	my @arr = split("\t");
	next if (@arr < 5);
	my ($en_name, $tid1, $tid2) = @arr;
	$beauties{"$en_name\t$tid1\t$tid2"} = 0;
}
close IN;

my $files = `ls $ENV{'pwd'}/data/att/`;
my @files = split("\n", $files);
foreach my $file (@files) {
	if ($file =~ /(\w+)\.(\w+)/) {
		my ($md5, $ext_name) = ($1, $2);
		my ($en_name, $bid, $tid1, $tid2) = execute_vector("select en_name, bid, tid1, tid2 from board, attachment where md5 = '$md5' and id = bid");
		if (defined($tid1)) {
			next if (defined($beauties{"$en_name\t$tid1\t$tid2"}));
			next if (execute_scalar("select count(*) from topic where bid = $bid and tid1 = $tid1 and tid2 = '$tid2' and unix_timestamp(now()) - unix_timestamp(pub_time) < 3600 * 24 * 3") > 0);
		}
#		`mv $ENV{'pwd'}/data/att/$md5.$ext_name $ENV{'pwd'}/data/to_be_deleted_ptt/`;
		`rm $ENV{'pwd'}/data/att/$md5.$ext_name`;
		print "rm $ENV{'pwd'}/data/att/$md5.$ext_name\n";
		$db_conn->do("delete from attachment where md5 = '$md5'");
	}
}

$db_conn->do('use ptt');
$files = `ls $ENV{'pwd'}/spider/att_ori/`;
@files = split("\n", $files);
foreach my $file (@files) {
	chomp $file;
	if ($file =~ /(\d+)\.(\w+)\.(.+)/) {
		my ($bid, $tid, $file_name) = ($1, $2, $3);
		next if (execute_scalar("select count(*) from topic where bid = $bid and tid = '$tid' and unix_timestamp(now()) - unix_timestamp(pub_time) < 3600 * 24 * 3") > 0);
		print "rm $ENV{'pwd'}/spider/att_ori/$bid.$tid.$file_name\n";
#		`mv $ENV{'pwd'}/spider/att_ori/$bid.$tid.$file_name $ENV{'pwd'}/data/to_be_deleted_disp/`;
		`rm $ENV{'pwd'}/spider/att_ori/$bid.$tid.$file_name`;
		$db_conn->do("delete from attachment where bid = $bid and tid = '$tid'");
	}
}
