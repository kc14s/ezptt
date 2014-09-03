require('lib.pl');
use bigint;

sub id36_to_int {
	my $radix = 36;
	my $len = length($_[0]);
	my $ret = 0;
	for (my $i = 0; $i < $len; ++$i) {
		$ret *= $radix;
		my $ascii = ord(substr($_[0], $i, 1));
		if ($ascii >= 48 && $ascii <= 57) {
			$ret += $ascii - 48;
		}
		elsif ($ascii >= 97 && $ascii <= 122) {
			$ret += $ascii - 97 + 1;
		}
		elsif ($ascii >= 65 && $ascii <= 90) {
			$ret += $ascii - 65 + 1;
		}
		else {
			die("invalid id36 $_[0]\n");
		}
#		print substr($_[0], $i, 1)." ascii $ascii ret $ret add ".($ret % 36)."\n";
	}
	return $ret;
}

sub fetch_reddit {
	while (1) {
		sleep(1);
		print "fetching $_[0]\n";
		my $html = `curl -s -A 'wy_spider' '$_[0]'`;
		if (index($html, '[') == 0 || index($html, '{') == 0) {
			return $html;
		}
		else {
			print $html;
		}
	}
}

sub is_good {
	my ($domain, $ups) = @_;
	if (($domain eq 'imgur.com' || $domain eq 'i.imgur.com') && $ups >= 300) {
		return 1;
	}
	else {
		return 0;
	}
}

1;
