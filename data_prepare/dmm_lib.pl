
my %forbidden_seed_sns = (
'186_08242' => 0,
'h_740dag00013' => 0,
'186_00704' => 0,
'h_186av00099' => 0,
'mi00024' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0
);

sub get_seeds {
	my ($sn, $snn, $db_conn) = @_;
	return if (defined($forbidden_seed_sns{$sn}));
	my $query = $snn;
	my $num = $1 if ($snn =~ /(\d+)/);
	my $letters = $1 if ($snn =~ /([a-z]+)/);
	if (defined($num)) {
		if (length($num) == 1 || length($letters) == 1) {
#			my @stars = execute_column("select star from star where sn = '$sn'");
#			$query .= ' '.join(' ', @stars);
		}
		elsif (1 || length($num) >= 3) {
			$query = "$letters $num";
		}
	}
	else {
		print "illegal snn $snn\n";
	}
	if (length($letters) > 1 && $num >= 10) {
		request_btkitty($sn, $snn, "$letters $num", $db_conn);
		request_btkitty($sn, $snn, "$letters$num", $db_conn);
		if (0 && index($num, '0') == 0) {
			$num = $1 if ($num =~ /0+(\d+)/);
			request_btkitty($sn, $snn, "$letters$num", $db_conn) if ($num >= 10);
		}
	}
	else {
		$title = execute_scalar("select title from video where sn = '$sn'", $db_conn);
		request_btkitty($sn, $snn, '"$title"', $db_conn) if (length($title) > 20);
	}
}

sub request_btkitty {
	my ($sn, $snn, $query, $db_conn) = @_;
	$query = uri_escape($query); 
	#my $list_html = `curl -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' -s -L -d 'keyword=$query' http://btkitty.biz/`;
	my $redirect_header = `curl -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' -s -d 'keyword=$query' http://btkitty.biz/ -D -`;
	my $list_html = '';
	if ($redirect_header =~ /btkitty\.(\w+)\/search\/(\w+)\//) {
		$list_html = `curl -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' -s http://btkitty.$1/search/$2/1/4/0.html`;
	}
	else {
		print "request btkitty $query failed\n";
		return;
	}
#	print $list_html;
	while ($list_html =~ /(http:\/\/btkitty\.\w+\/torrent\/[\w\-]+\.html)/g) {
		print "$1\n";
		my $html = `curl -s -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' '$1'`;
		my $name = $1 if ($html =~ /<meta name="description" content="([\d\D]+?)"/);
		if (!defined($name)) {
			$name = $1 if ($html =~ /<span>Name<\/span><\/dt><dd>([\d\D]+?)\.torrent<\/dd>/);
		}
		if (!defined($name)) {
			$name = $1 if ($html =~ /<span>Name<\/span><\/dt><dd>([\d\D]+?)\s*</);
		}
		if (substr_count($name, ',') >= 5 || substr_count($name, '-') >= 5 || substr_count($name, '\.') >= 5 || substr_count($name, '_') >= 5) {
			print "illegal seed name $name\n";
			next;
		}
		next if ($snn eq 'world2016' || $snn eq 'kub002');
		if (validate_seed_name($snn, $name) == 0) {
			print "seed name mismatch $snn $query $name\n";
			next;
		}
		my $size_text = $1 if ($html =~ /<dt class='t1'><span>Size<\/span><\/dt>\s*<dd>([\w\.\s]+)<\/dd>/);
		my $file_num = $1 if ($html =~ /<dt class='t1'><span>Number of files<\/span><\/dt>\s*<dd>(\d+)<\/dd>/);
		my $created = $1 if ($html =~ /<dt class='t1'><span>Torrent added at<\/span><\/dt>\s*<dd>([\d\-\s:]+)<\/dd>/);
		my $recent_request = $1 if ($html =~ /<dt class='t1'><span>Recent download at<\/span><\/dt>\s*<dd>([\d\-\s:]+)<\/dd>/);
		my $hot = $1 if ($html =~ /<dt class='t1'><span>Popularity<\/span><\/dt>\s*<dd>(\d+)<\/dd>/);
		my $seed_url = $1 if ($html =~ /<dd><a href='(.+?)' target='_blank'>\[Download the torrent file\]<\/a>/);
		if (!defined($seed_url)) {
			$seed_url = $1 if ($html =~ /'(http:\/\/storebt\.com\/torrent\/.+?)'/);
		}
		my $magnet = $1 if ($html =~ /<a href='(magnet:.+?)'>/);
		my $size = 0;
		if ($size_text =~ /([\d\.]+)\s*([A-Za-z]+)/) {
			$size = $1;
			if (uc($2) eq 'GB') {
				$size *= 1024 * 1024 * 1024;
			}
			elsif (uc($2) eq 'MB') {
				$size *= 1024 * 1024;
			}
			elsif (uc($2) eq 'KB') {
				$size *= 1024;
			}
			$size = int($size + 0.5);
			if ($size < 500 * 1024 * 1024) {
				print "skip seed too small $size_text $size $magnet\n";
				next;
			}
		}
		print "$name\t$size_text\t$size\t$file_num\t$created\t$recent_request\t$hot\t$seed_url\t$magnet\n";
		$db_conn->do("replace into seed(sn, magnet, name, size_text, size, file_num, created, recent_request, hot, seed_url) values('$sn', '$magnet', ".$db_conn->quote($name).", '$size_text', $size, $file_num, '$created', '$recent_request', $hot, '$seed_url')");
	}
}

sub normalize_sn {
	my $snn = $_[0];
	if ($snn =~ /([a-z]+)0*(\d+?)$/) {
		$snn = sprintf("%s%03d", $1, $2);
	}
	elsif ($snn =~ /([a-z]+)0*(\d+)[a-z]+$/) {
		$snn = sprintf("%s%03d", $1, $2);
	}
	return $snn;
}

my %forbidden_snns = (
'd480' => 0,
'tk007' => 0,
'world2009' => 0,
'world2014' => 0,
'rs091' => 0,
'dag013' => 0,
'av099' => 0,
'mi024' => 0,
'' => 0,
'' => 0
);

sub validate_seed_name {
	my ($snn, $seed_name) = @_;
#	return 0 if ($seed_name =~ /legalporno/i);
	return 0 if (defined($forbidden_snns{$snn}));
	return 0 if ($snn =~ /^rs\d+&/);
	my ($prefix, $suffix);
	if ($snn =~ /([A-Za-z]+)(\d+)/) {
		$prefix = lc($1);
		$suffix = $2;
	}
	else {
#		print "illegal snn $snn\n";
		return 1;
	}
	my $index = 0;
	my %seg_index;
	while ($seed_name =~ /([A-Za-z\d]+)/g) {
		my $match = lc($1);
		if (0 && index($match, $snn) >= 0) {
			return 1;
		}
		next if (defined($seg_index{$match}));
		$seg_index{$match} = $index;
		++$index;
	}
	if (0 || $seed_name =~ /[[:^ascii:]]/) {}
	else {
		my @forbidden_words = qw(legalporno bbc nba cocaine goldie ginger microsoft windows chrome google);
		foreach my $forbidden_word (@forbidden_words) {
			return 0 if (defined($seg_index{$forbidden_word}));
		}
	}
	my $seed_name_lc = lc($seed_name);
	return 1 if (index($seed_name_lc, "$prefix$suffix") >= 0);
	if (defined($seg_index{$prefix}) && defined($seg_index{$suffix}) && $seg_index{$prefix} + 1 == $seg_index{$suffix}) {
		return 1;
	}
	if (1 && index($suffix, '0') == 0) {
		$suffix = $1 if ($suffix =~ /0+(\d+)/);
		return 1 if (index($seed_name_lc, "$prefix$suffix") >= 0);
		if (defined($seg_index{$prefix}) && defined($seg_index{$suffix}) && $seg_index{$prefix} + 1 == $seg_index{$suffix}) {
			return 1;
		}
	}
	return 0;
}

1;
