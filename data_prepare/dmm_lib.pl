my %forbidden_seed_sns = (
'186_08242' => 0,
'h_740dag00013' => 0,
'186_00704' => 0,
'h_186av00099' => 0,
'mi00024' => 0,
'303goku00061d' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0
);

my @channel_size_limitations = (
0,
500 * 1024 * 1024,
500 * 1024 * 1024,
100 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
100 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024,
500 * 1024 * 1024
);

my $title_length_min = 40;

sub get_seeds {
	my ($sn, $snn, $channel, $db_conn) = @_;
	return if (defined($forbidden_seed_sns{$sn}));
	my $query = $snn;
	my $num = $1 if ($snn =~ /(\d+)/);
	my $letters = $1 if ($snn =~ /([a-z]+)/);
	if ($channel == 10) {
		($letters, $num) = ($1, $2) if ($snn =~ /(\d+)_(\d+)/);
	}
	if ($channel == 1 || $channel == 5 || $channel == 4 || $channel == 8) {
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
			return;
		}
		if (length($letters) > 1 && $num >= 10) {
			request_btany($sn, $snn, "$letters $num", $channel, $db_conn);
			request_btany($sn, $snn, "$letters$num", $channel, $db_conn);
			request_btkitty($sn, $snn, "$letters $num", $channel, $db_conn);
			request_btkitty($sn, $snn, "$letters$num", $channel, $db_conn);
			request_pirate_bay($sn, $snn, "$letters $num", $channel, $db_conn);
			if (0 && index($num, '0') == 0) {
				$num = $1 if ($num =~ /0+(\d+)/);
				request_btany($sn, $snn, "$letters$num", $channel, $db_conn) if ($num >= 10);
			}
		}
	}
	elsif ($channel == 9) {
		request_btany($sn, $snn, $snn, $channel, $db_conn);
		request_btkitty($sn, $snn, $snn, $channel, $db_conn);
		request_pirate_bay($sn, $snn, $snn, $channel, $db_conn);
	}
	elsif ($channel == 10) {
		request_btany($sn, $snn, $sn, $channel, $db_conn);
		request_btkitty($sn, $snn, $sn, $channel, $db_conn);
		request_pirate_bay($sn, $snn, $sn, $channel, $db_conn);
	}
	if ($channel == 8) {
		request_btany($sn, $snn, $sn, $channel, $db_conn);
		request_btkitty($sn, $snn, $sn, $channel, $db_conn);
		request_pirate_bay($sn, $snn, $sn, $channel, $db_conn);
	}
	if ($channel != 1 && execute_scalar("select count(*) from seed where sn = '$sn'") == 0) {
		my $title = execute_scalar("select title from video where sn = '$sn'", $db_conn);
		my @words = split(' ', $title);
		my %words;
		foreach my $word (@words) {
			$words{$word} = 0 if (length($word) >= 2);
		}
		if (length($title) > $title_length_min || ($channel == 7 && scalar keys %words >= 3)) {	
#			request_btany($sn, $snn, $title, $channel, $db_conn);
			request_btkitty($sn, $snn, $title, $channel, $db_conn);
		}
		if ($channel == 2) {
#			$title_utf8 = decode('utf8', $title);
#			if ($title_utf8 =~ /([\p{Han}]+)/) {
			my $sn_prefix;
			if ($snn =~ /([a-z]+)/) {
				$sn_prefix = $1;
#				if (length($title_utf8) >= 3) {
#				request_btany($sn, $snn, "$sn_prefix $title", $channel, $db_conn);
				request_btkitty($sn, $snn, "$sn_prefix $title", $channel, $db_conn);
#				request_pirate_bay($sn, $snn, "$sn_prefix $title", $channel, $db_conn);
			}
		}
	}
}

sub get_emule {
	my ($sn, $snn, $channel, $db_conn) = @_;
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
		request_mldonkey($sn, $snn, "$letters $num", $channel, $db_conn);
		if (0 && index($num, '0') == 0) {
			$num = $1 if ($num =~ /0+(\d+)/);
			request_mldonkey($sn, $snn, "$letters$num", $channel, $db_conn) if ($num >= 10);
		}
	}
	else {
		$title = execute_scalar("select title from video where sn = '$sn'", $db_conn);
		request_mldonkey($sn, $snn, "$title", $channel, $db_conn) if (length($title) > 20);
	}
}

sub request_mldonkey {
	my ($sn, $snn, $query, $channel, $db_conn) = @_;
	print "requesting emule $query\n";
	my $query_original = $query;
	$query = uri_escape('"'.$query.'"');
	my $html = `curl -s 'http://mldonkey.ucptt.com/submit?custom=Complex+Search&keywords=$query&minsize=&minsize_unit=1048576&maxsize=&maxsize_unit=1048576&media=&media_propose=&format=&format_propose=&artist=&album=&title=&bitrate=&network='`;
	my $search_id = $1 if ($html =~ /Query (\d+) sent to/);
	if (!defined($search_id)) {
		print "invalid search_id\n$html\n";
	}
	sleep(60);
	$html = `curl -s 'http://mldonkey.ucptt.com/submit?q=vr+$search_id'`;
	while ($html =~ /<td class="sr"><a href="ed2k:\/\/\|file\|([\d\D]+?)\|(\d+)\|(\w+)\|\/">Donkey<\/a><\/td><td [\d\D]+?<\/td><td class="sr ar">[\w\.]+<\/td>\s*<td class="sr ar">(\d*)<\/td>\s*<td class="sr ar">(\d*)<\/td>/g) {
		my ($file_name, $file_size, $file_hash, $available_sources, $completed_sources) = ($1, $2, $3, $4, $5);
		$available_sources = 0 if (!defined($available_sources) || $available_sources eq '');
		$completed_sources = 0 if (!defined($completed_sources) || $completed_sources eq '');
		if ($size < $channel_size_limitations[$channel]) {
			print "skip seed too small $size_text $size $magnet\n";
			next;
		}
		$file_name = uri_unescape($file_name);
		if (validate_seed_name($sn, $snn, $query_original, $file_name, $channel) == 0) {
			print "seed name mismatch $snn $query_original vs $file_name\n";
			next;
		}
		print "$sn $file_name $file_size $file_hash $available_sources $completed_sources\n";
		$db_conn->do("replace into emule(sn, hash, name, size, available_sources, completed_sources) values('$sn', '$file_hash', ".$db_conn->quote($file_name).", $file_size, $available_sources, $completed_sources)");
	}
}

sub request_pirate_bay {
	return;
	my ($sn, $snn, $query, $channel, $db_conn) = @_;
	return if ($snn eq 'world2016' || $snn eq 'kub002');
	my $query_original = $query;
	$query = uri_escape($query); 
	my $list_html = get_url("https://thepiratebay.org/search/$query/0/99/0");
	my @seeds = parse_pb_list_html($list_html);
	foreach my $seed (@seeds) {
		my ($name, $magnet, $hash, $size, $seeder, $leecher) = @$seed;
		if (validate_seed_name($sn, $snn, $query_original, $name, $channel) == 0) {
			print "seed name mismatch $snn $query_original $name\n";
			next;
		}
		my $hot = $seeder * 3 + $leecher;
		$hot += 1;
		next if (execute_scalar("select count(*) from seed where sn = '$sn' and hash = '$hash'") > 0);
		print "seed2\t$sn\t$name\t$size\t$hot\t$hash\n";
		$db_conn->do("replace into seed(sn, magnet, name, size, hot, hash, source) values('$sn', 'magnet:?xt=urn:btih:$hash', ".$db_conn->quote($name).", $size, $hot, '$hash', 2)");
	}
}

sub request_btany {
	my ($sn, $snn, $query, $channel, $db_conn) = @_;
	return if ($snn eq 'world2016' || $snn eq 'kub002');
	my $query_original = $query;
	$query = uri_escape($query); 
	my $list_html = get_url("http://www.btany.com/search/$query-hot-desc-1");
	my @items = split('<div class="search-item">', $list_html);
	for (my $i = 1; $i < @items; ++$i) {
		my $item = $items[$i];
		my $name = $1 if ($item =~ /target="_blank">([\d\D]+?)<\/a>/);
		next if (!defined($name));
		$name =~ s/<.+?>//g;
		$name =~ s/\[email&#160;protected\]\/\*  \*\/@?//g;
		if (validate_seed_name($sn, $snn, $query_original, $name, $channel) == 0) {
			print "seed name mismatch $snn $query_original $name\n";
			next;
		}
		my $hot = 0;
		$hot = $1 if ($item =~ /<span>活跃热度： <b class="cpill yellow-pill">(\d+)<\/b> <\/span>/);
		my $size_text = $1 if ($item =~ /<span>文件大小： <b class="cpill yellow-pill">([\w\s\.]+)<\/b> <\/span>/);
		my $size = size_text_to_size($size_text);
		if ($size < $channel_size_limitations[$channel]) {
			print "skip seed too small $size_text $size\n";
			next;
		}
		my $hash = $1 if ($item =~ /<a href="magnet:\?xt=urn:btih:(\w+)"/);
		next if (execute_scalar("select count(*) from seed where sn = '$sn' and hash = '$hash'") > 0);
		print "seed1\t$sn\t$name\t$size\t$hot\t$hash\n";
		$db_conn->do("replace into seed(sn, magnet, name, size, hot, hash, source) values('$sn', 'magnet:?xt=urn:btih:$hash', ".$db_conn->quote($name).", $size, $hot, '$hash', 1)");
	}
}

sub request_btkitty {
	my ($sn, $snn, $query, $channel, $db_conn) = @_;
	return if ($snn eq 'world2016' || $snn eq 'kub002');
	my $query_original = $query;
	$query = uri_escape($query); 
	my $redirect_header = post_url('http://btkitty.bid/', {'keyword' => $query, 'hidden' => 'true'}, 1);
#	print "response header\n$redirect_header\n";
	my $list_html = '';
	if ($redirect_header =~ /btkitty\.(\w+)\/search\/([\w\-]+)\//) {
		#$list_html = `curl -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' -s http://btkitty.$1/search/$2/1/4/0.html`;
		$list_html = get_url("http://btkitty.$1/search/$2/1/4/0.html");
	}
	else {
		print "request btkitty $query failed\n";
		return;
	}
#	print $list_html;
	my @items = split('<dt><strong>', $list_html);
	foreach my $item (@items) {
		my $magnet = $1 if ($item =~ /'(magnet:\?xt=urn:btih:\w+&dn=[\d\D]+?)'/);
		next if (!defined($magnet));
		my $name = $1 if ($magnet =~ /&dn=([\d\D]+)/);
		my $snn_count = 0;
		while ($seed_name =~ /[a-zA-Z]+[\-\_ ]*\d{3}/g) {
			++$snn_count;
		}
		if ($snn_count >= 5) {
			if (index($seed_name, '合集') < 0) {
				print "snn count $snn_count\n";
				return 0;
			}
		}
		#if (substr_count($name, ',') >= 5 || substr_count($name, '-') >= 5 || substr_count($name, '\.') >= 5 || substr_count($name, '_') >= 5) {
		#	print "illegal seed name $name\n";
		#	next;
		#}
		$name = uri_unescape($name);
		if (validate_seed_name($sn, $snn, $query_original, $name, $channel) == 0) {
			print "seed name mismatch $snn $query_original $name\n";
			next;
		}
		my $created = $1 if ($item =~ /<b>([\d\-]{10})<\/b>/);
		my $recent_request = '2000-01-01';
		my $size_text = $1 if ($item =~ /<b>([\d\.]+ [A-Z]+)<\/b>/);
		my $file_num = $1 if ($item =~ /Files:<b>(\d+)<\/b>/);
		my $hot = $1 if ($item =~ /Popularity&nbsp;:&nbsp;<b>(\d+)<\/b>/);
		my $seed_url = '';
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
			if ($size < $channel_size_limitations[$channel]) {
				print "skip seed too small $size_text $size $magnet\n";
				next;
			}
		}
		print "seed0\t$name\t$size_text\t$size\t$file_num\t$created\t$hot\t$magnet\n";
		$db_conn->do("replace into seed(sn, magnet, name, size, file_num, created, recent_request, hot, seed_url) values('$sn', '$magnet', ".$db_conn->quote($name).", $size, $file_num, '$created', '$recent_request', $hot, '$seed_url')");
	}
}

sub normalize_sn {
	my $snn = $_[0];
	if ($snn =~ /([a-z]+)\-?0*(\d+?)$/) {
		$snn = sprintf("%s%03d", $1, $2);
	}
	elsif ($snn =~ /([a-z]+)\-?0*(\d+)[a-z]+$/) {
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
'goku61' => 0,
'bm11' => 0,
'dsd128' => 0,
'' => 0,
'' => 0,
'' => 0
);

sub validate_seed_name {
	my ($sn, $snn, $query, $seed_name, $channel) = @_;
#	return 0 if ($seed_name =~ /legalporno/i);
	return 0 if (defined($forbidden_snns{$snn}));
	return 0 if ($snn =~ /^rs\d+&/);
	return 0 if (index($seed_name, '.iso') > 0 && length($seed_name) > length($snn) + 1 + 4);
	if ($channel == 2) {
		if ($query =~ /([a-z]+) ([\d\D]+)/) {
			if (index($seed_name, $2) >= 0) {
				return 1;
			}
			else {
				return 0;
			}
		}
	}
	if ($channel == 10) {
		my @sn_arr = split('_', $sn);
		if ($seed_name =~ /$sn_arr[0]\D$sn_arr[1]/) {
			return 1;
		}
		else {
			return 0;
		}
	}
	if ($channel == 9) {
		if (index(lc($seed_name), $snn) >= 0) {
			return 1;
		}
		else {
			return 0;
		}
	}
	if ($channel == 8 && $sn eq $query) {
		if (index(lc($seed_name), lc($sn)) >= 0) {
			return 1;
		}
	}
	if (length($query) > $title_length_min || ($channel == 7 && scalar split(' ', $query) >= 3)) {
		if (1 || $channel == 3 || $channel >= 5) {
			return 1;
		}
		my $seed_name_trimed = $seed_name;
		$seed_name_trimed =~ s/ //g;
		$query_trimed = $query;
		$query_trimed =~ s/ //g;
		if (index($seed_name_trimed, $query_trimed) >= 0) {
			return 1;
		}
	}
	my ($prefix, $suffix);
	if ($snn =~ /([A-Za-z]+)(\d+)/) {
		$prefix = lc($1);
		$suffix = $2;
	}
	else {
#		print "illegal snn $snn\n";
		return 1;
	}#

	my $snn_count = 0;
	while ($seed_name =~ /[a-zA-Z]+[\-\_ ]*\d{3}/g) {
		++$snn_count;
	}
	if ($snn_count >= 5) {
		if (index($seed_name, '合集') < 0) {
			print "snn count $snn_count\n";
			return 0;
		}
	}
#	if (substr_count($seed_name, ',') >= 5 || substr_count($seed_name, '-') >= 5 || substr_count($seed_name, '\.') >= 5 || substr_count($seed_name, '_') >= 5) {
#		print "illegal seed name $seed_name\n";
#		return 0;
#	}
	my $index = 0;
	my %seg_index;
	my $alphabet = '';
	while ($seed_name =~ /([A-Za-z\d]+)/g) {
		my $match = lc($1);
		$alphabet .= $match;
		if (0 && index($match, $snn) >= 0) {
			return 1;
		}
		next if (defined($seg_index{$match}));
		$seg_index{$match} = $index;
		++$index;
	}
	if (0 || $seed_name =~ /[[:^ascii:]]/) {}
	else {
		my @forbidden_words = qw(legalporno bbc nba cocaine goldie ginger microsoft windows chrome google sql server debian freebsd linux 64bit win7 x86);
		foreach my $forbidden_word (@forbidden_words) {
			return 0 if (defined($seg_index{$forbidden_word}));
		}
	}
	my $seed_name_lc = lc($seed_name);
	return 1 if (index($seed_name_lc, "$prefix$suffix") >= 0);
	return 1 if (index($alphabet, "$prefix$suffix") >= 0);
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

sub size_text_to_size {
	my $size_text = shift;
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
		return $size;
	}
	return 0;
}

sub parse_pb_list_html {
	my $html = shift;
	my @arr = split('<div class="detName">', $html);
	my @ret;
	for (my $i = 1; $i < @arr; ++$i) {
		my $item = $arr[$i];
#		print $item;
#		last;
		my $title = $1 if ($item =~ /title="Details for ([\d\D]+?)">/);
		my $magnet = $1 if ($item =~ /<a href="(magnet:\?.+?)"/);
		my ($size, $unit) = ($1, $2) if ($item =~ /Size ([\d\.]+)&nbsp;(\w)/);
		if ($unit eq 'M') {
			$size *= 1024 * 1024;
		}
		elsif ($unit eq 'G') {
			$size *= 1024 * 1024 * 1024;
		}
		else {
			print "skip seed too small $size $unit\n";
			next;
		}
		$size = int($size);
		my ($seeder, $leecher) = ($1, $2) if ($item =~ /<td align="right">(\d+)<\/td>\s*<td align="right">(\d+)<\/td>/);
#print "$title $seeder $leecher $size $magnet\n";
		my $hash = $1 if ($magnet =~ /urn:btih:(\w+)/);
		push @ret, [$title, $magnet, $hash, $size, $seeder, $leecher];
	}
	return @ret;
}

1;
