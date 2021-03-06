use strict;
use warnings;
use LWP::UserAgent;
use HTTP::Cookies;
use Data::Dumper;
use Encode;
use JSON;
use URI::Escape;
#use Encode::HanConvert;
use Digest::MD5;
#use Encode::HanConvert qw(trad simple);

my @proxies;
my $db_conn;
my $ptt_site = 'www.ptt.cc';
my $json = new JSON;

sub load_proxy {
	open IN, $ENV{"pwd"}."/data_prepare/data/proxy" or die("load proxy file failed\n");
	while (<IN>) {
		chomp;
		push @proxies, $_;
	}
	close IN;
	print "".(scalar @proxies)." proxies loaded\n";
	return @proxies;
}

sub init_db {
	my $db_server = $ENV{"db_server"};
	my $database = $ENV{"database"};
	if (@_ == 1) {
		$database = shift;
	}
	my $user = $ENV{"user"};
	my $password = $ENV{"password"};
	$db_conn = DBI->connect("DBI:mysql:database=$database;host=$db_server", $user, $password, {RaiseError => 0, AutoCommit =>1, mysql_auto_reconnect=>1, mysql_enable_utf8=>1}) or die("init db failed");
	$db_conn->do("set names UTF8");
	$db_conn->do("SET time_zone = '+8:00'");
	$db_conn->do('SET LOW_PRIORITY_UPDATES=1');
	$db_conn->{mysql_auto_reconnect} = 1;
	return $db_conn;
}

sub get_datetime_string {
	my $timestamp = $_[0];
	$timestamp = time() if (!defined($timestamp));
	my ($sec,$min,$hour,$day,$mon,$year,$wday,$yday,$isdst)=localtime($timestamp);
	$year += 1900;
	$mon++;
	$mon = "0$mon" if ($mon < 10);
	$day = "0$day" if ($day < 10);
	$hour = "0$hour" if ($hour< 10);
	$min = "0$min" if ($min < 10);
	$sec = "0$sec" if ($sec < 10);
	return "$year-$mon-$day $hour:$min:$sec";
}

sub get_https {
	my $url = $_[0];
	my $cookie = '';
	print "fetching $url\n";
	my $proxy_idx = -1;
	if (1 && scalar @proxies <= 10) {
		load_proxy();
	}
	if (index($url, 'www.zhihu.com') > 0) {
		$proxy_idx = int(rand(scalar @proxies));
		my $proxy = $proxies[$proxy_idx];
		return `curl -s -S "$url" -x $proxy -H "Host: www.zhihu.com" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0" -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" -H "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3" --compressed -H "Referer: https://www.zhihu.com/people/wang-ye-80" -H "Cookie: q_c1=f1497e92e7fd4f75b25f144bb2026e50|1463626449000|1458300724000; d_c0=""AGBA2nNzogmPTqWus3-2tPHI1nChmajAoHY=|1458300723""; __utma=155987696.1510435070.1464852944.1464852944.1464852944.1; __utmz=155987696.1464852944.1.1.utmcsr=zhihu.com|utmccn=(referral)|utmcmd=referral|utmcct=/people/wang-ye-80/about; _za=8a7641ba-086c-48ad-b84e-807114e90274; _zap=13ef5654-944a-4ff4-b226-624324edf4f6; _xsrf=cbd73748e7beec5149abbf2d928cfad5; l_n_c=1; l_cap_id=""OThkYjQzNTBiNGFiNGUxYmExYzcxOTYzOTdmNjNmNTI=|1464852098|9d9e40da9002a02acf28a58c4fe9039ce9aec5ba""; cap_id=""MWZlNmRlZmM2ZjczNGU0ZTlmZDdjNDBhMDYzMWRjMzA=|1464852098|e8a7037e69ec5b2f6f232b51ab8feaa572d78516""; n_c=1; __utmb=155987696.5.8.1464853004882; __utmc=155987696; a_t=""2.0AACA6F0sAAAXAAAAb2x3VwAAgOhdLAAAAGBA2nNzogkXAAAAYQJVTZprd1cAJ42MpjkKyZH8DaAbNDgxnemIJsOhs8_TM7cebnyAyz4FpVfeTFcitw==""; z_c0=Mi4wQUFDQTZGMHNBQUFBWUVEYWMzT2lDUmNBQUFCaEFsVk5tbXQzVndBbmpZeW1PUXJKa2Z3Tm9CczBPREdkNllnbXd3|1464852122|f0d0d77ad36834164527ace46ff272b073e86cca; __utmt=1" -H "Connection: keep-alive" -H "Cache-Control: max-age=0" --connect-timeout 6 -m 10`;
		return `curl -s -S '$url' -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' --compressed --connect-timeout 3 -m 10 $cookie`;
	}
	elsif (index($url, 'ptt.cc') > 0) {
		$cookie = "-H 'Cookie: over18=1'";
	}
	my $proxy = '';
	if (index($url, 'zhihu.com') > 0 || index($url, 'btkitty') > 0 || index($url, 'douban.com') > 0) {
		$proxy_idx = int(rand(scalar @proxies));
		$proxy = "-x $proxies[$proxy_idx]";
	}
	return `curl -s -S '$url' -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' --compressed --connect-timeout 3 -m 10 $cookie $proxy`;
}

sub get_url {
	my $url = $_[0];
	return get_https($url) if (index($url, 'https') == 0);
	return `curl -s '$url' -A "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" --compressed --connect-timeout 3 -m 10` if (index($url, 'diggbt') > 0);
	my $retry_count = 0;
	while (1) {
		if (index($url, 'ptt.cc') >= 0 || index($url, 'ck101.com') >= 0 || index($url, 'tianya.cn') >= 0) {
		#if (index($url, 'ptt.cc') >= 0) {
			sleep(1);
		}
		print "fetching $url\n";
		my $ua = LWP::UserAgent->new;
		$ua->agent("Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
		my $proxy_idx = -1;
		if (index($url, "att.php") > 0) {
			$ua->timeout(60);
		}
		elsif (index($url, 'www.5x') >= 0) {
			$ua->timeout(60);
		}
		else {
			$ua->timeout(10);
		}
		$proxy_idx = -1;
		if (1 && scalar @proxies <= 10) {
			load_proxy();
		}
		my $request = HTTP::Request->new(GET=>$url);
#		$request->header('Accept-Encoding' => HTTP::Message::decodable);
		$request->header('Accept-Encoding' => 'utf8');
#		$request->header('Referer' => 'https://www.ptt.cc/ask/over18?from=%2Fbbs%2FSex%2Findex.html');
		if (index($url, 'zhihu.com') > 0 || index($url, 'btkitty') > 0 || index($url, 'douban.com') > 0) {
			$proxy_idx = int(rand(scalar @proxies));
			$ua->no_proxy();
			$ua->proxy(['http', 'https'], "http://".$proxies[$proxy_idx].'/');
#			print "use proxy $proxies[$proxy_idx]\n";
			$request->header('Cookie' => 'over18=1');
		}
		elsif (index($url, 'www.5x') >= 0) {
			$request->header('Cookie' => 'AVS=0v75qrll8dplss411po0bfcd54; splash=1; kt_tcookie=1; __atuvc=7%7C50; __atuvs=58531fbb7d8cea45006; _ga=GA1.2.1732567665.1481842085; _gat=1; style=white');
		}
		elsif (index($url, 'playno1') >= 1) {
			$request->header('Cookie' => 'playno1=playno1Cookie; HZ1J_2846_secqaaSzYFEqg0=619fSFO2YgwfWcqsLQ1SWCP3j7GCwBtEPR3PEqM2uldVsuKvH5QqBrdOkAoKipsD3ec2nt2BulXNtTxsCXu5%2FwwN4TPwmJ60HT2mvm63Y0OhaQ%2FhA6nbL1IA; HZ1J_2846_secqaaSnM98B70=e5f6oAtKN63d7eCziEO%2FHyf8mQ5i1v6cPLXQngp5r3205v7hHcobVt4Erm8IVu90g2EDWEEhtRt0Y7Y3XdmZXw%2Fqzx%2BeihQdwxCM%2Feo85HlWQ7YOHIGv2cYJ; HZ1J_2846_secqaaSrrT4w80=ef6fHI5HpdeeGLupBMRGAhVLYpkqHQdHXh7CfSVbcMQ0F%2B1%2BaXWTg6QaaOZI1%2F0HjU1eVaq2sFCupOxG1Fhip%2FGl%2BqcSU3weLd%2B6fzpp8Nn%2FS5TjNiNkGZ0O; HZ1J_2846_secqaaSG9GD970=8f6eDnyA5CGieuppVZPgwXvWNjw6TGm%2BV1syrKsqjzGkci4mKYl2mTRlJM6w5HINONQcAxUgSmY2ZzB7PjSRsysvUkIbL1R7oOdRCvv5hFgprH0FAfU03JVc; HZ1J_2846_secqaaSkfQXFz0=9f9c%2F2SNshKNKmMXK1MGf9lQaAi%2FX77YJAYtNcoJratpOQjn26RF%2F221vnez03RHC8x5YEWkBEsygQegwY%2Bmy%2FTj9mTA1Qo0lfPSRwQS57dUQMVCSAZ4L5wH; HZ1J_2846_secqaaS44h95W0=68c2bXDAWRl0w8mjhVcl9zN%2F5SOGnzjqL1xOM7U6v8WBfYD8%2FVr0ig1LWH5A8ptGlV5uG%2B3tXHDIGIVryY5EJbx%2FT6%2BTZzPt8kopzIvC2Pp7pZYVbmhGTyFT; HZ1J_2846_secqaaS2DtkdZ0=1c77BTJQgKYyDmJnc6Ii95ZtddHoGQiydOeN3abMV1%2BS82PxLa0DQ5O27ALKj6tjir%2FDr596UIQQA%2FRdD68STazKqnT2hNHj%2BwawXkDyK0RkB9bt82v7rFw1; HZ1J_2846_secqaaSbv16P40=f5ebX4esyrxug39OWwK2cRcyzeEGk40adC8%2FKwe%2FDvh%2BONc2nYwYZ%2BQhipAfDw4IvtBJv5pOq4FJ7t5J9DjzPVvnuRDrsqJwUOX9jmN3Ip6bBF8Z4wgeEp2u; HZ1J_2846_saltkey=yG2imG1I; HZ1J_2846_lastvisit=1480828430; HZ1J_2846_secqaaSyKm7wm0=8545%2BIJQZL9PduViROmcHspysNJVvZYwFBjA9pNyz4txGQvLJ2s1YtekQCbSyWIpp0V1Y7IkiCPcOBD5Ib8bKJItKdz7irC9kVfC%2BwPMty3PKWqgire1Vb%2Fo; HZ1J_2846_secqaaSPhHKzH0=c1470VJ0caQcc73Nt0s3vi%2BDcoIfcQf823veW04Hq7c61%2BLL25Czwbz9jKvIoa%2FY%2Bhcgmgu%2FuDhiyvQwJLsLQZ5Y4XQGJzDbKuVhlzLyzWocHyRx9UxoUiYZ; playno1_referer=%2Farticle-21836-1.html; HZ1J_2846_sid=swWZhx; _ga=GA1.2.1682830128.1477023919; __atuvc=8%7C45%2C8%7C46%2C0%7C47%2C2%7C48%2C4%7C49; __atuvs=5844c41239e279ca003; HZ1J_2846_lastact=1480903659%09misc.php%09secqaa; HZ1J_2846_secqaaSswWZhx0=8fa8ho5j50Tamp6AF6bBtioqhXF3od5mYApoiwbgLsZetLoEzyJ2fHPEG4KlBfZQWp5btW2x00tgFI1nLDxekl5TCFkMCF3Z4cyhSsQJhmsHY4GEhtkYCjKc');
		}
		elsif (index($url, 'ck101.com') >= 0) {
			$request->header('Cookie' => '__cfduid=d3e6e839e15b2747f62b0d0767c43a1901479364555; Lre7_9bf0_saltkey=bI1EIyoO; Lre7_9bf0_lastvisit=1479360955; datetime=1116; __gads=ID=7d9cc57552d4449b:T=1479364558:S=ALNI_Mbl5z3G2vYkzTQ6WuRK1Y3DdIFb2w; Lre7_9bf0_av_a=1; Lre7_9bf0_viewid=tid_3703163; OX_plg=swf|shk|pm; _td=75955ed4-f679-4d79-8e3e-07efee9b6d7e; GED_PLAYLIST_ACTIVITY=W3sidSI6Im5FY1UiLCJ0c2wiOjE0NzkzNjUzMjIsIm52IjoxLCJ1cHQiOjE0NzkzNjUzMTgsImx0IjoxNDc5MzY1MzIyfV0.; times=111; Lre7_9bf0_agree18_2015=1; OX_ssn=9751816861; OX_sd=1; _gat=1; _gat_n_ga=1; _gat_ckad=1; _gat_all_ga=1; _gat_allvip_ga=1; fbm_455878464472095=base_domain=.ck101.com; PHPSESSID=vsi35hpsdg919gkig6rq5l2oc3; Lre7_9bf0_auth=dbf40vO%2Ff0NcVLjicsC6S7WMh4953U2mtTrSJ68QBwl70rtUAcg%2BPdhv1XTfdkQgUFMlFXbZXMcZw5W1sSIcglY%2B5tKM; _gat_UA-622529-34=1; Lre7_9bf0_nofavfid=1; Lre7_9bf0_seccodeS0=f93asa4PjS8diIvfOimd3rO6w3XSZBkBPpP%2BLMzqjyCZhcVFyC5q4sJaxhfQ%2FtqqQ2jfQzqyRng; Lre7_9bf0_forum_lastvisit=D_1288_1479364613D_3612_1479365300D_806_1479365606D_82_1479365650D_358_1479365664D_70_1479368030D_3565_1479368132; Lre7_9bf0_visitedfid=3565D70D358D82D3612; Lre7_9bf0_ulastactivity=1479368132%7C0; Lre7_9bf0_checkpm=1; Lre7_9bf0_lastact=1479368133%09home.php%09misc; Lre7_9bf0_sendmail=1; __asc=f0c0e3a015871317144afbdcd72; __auc=22ee629c15871000b5775ae5bb5; crtg_rta=; Lre7_9bf0_noticeTitle=1; _ga=GA1.2.2073038172.1479364578; _ceg.s=ogrzry; _ceg.u=ogrzry; fbsr_455878464472095=n_o3xLzEaxIa90Kqil0G7x8QvVoGH2f5h3qJtXpOPyo.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImNvZGUiOiJBUUFXOVc4UTN0aVZDNlVOMmsyYlNFQmxqSUc1ZEVjSTgtM3lsVTNOYnlkazF6b09WcHA3ODVpS1R4bTA3Z1hRQW9tZnFVVWp5NkNZbFNiUmM2d1EwWHJzSHc0T0YyaC1UeGhSVWNYVGhIcG9ZajFqaWNvT3lTX0xmYUFoZlBXOEVSRndrM05xcWlfLTVXcTQxcXVtSWFvUjc0SGk2NjNOTFVnaGVDcjNoc0tZLWMwTE1ubS1YSTJQRUdqNGlfdUgxTTRzV19uRTBobVgyV3c0dnJnaFFEOXJFczQwcDI5c2g1NDh3U3ZESmdxTDdiZ2s0SlZ6TWhyLVVQSjJ4MGRISjdsMjRmVW83QzgwS2lMRnQxbTJacVZ2S3dHd05WVF9ndGFDclFEczVIak8xQ1VTdEZFdVlsRFFWVk0xbTJVMzItSy1BWFY1ZTgwWXYyb1JIT0dONzByRyIsImlzc3VlZF9hdCI6MTQ3OTM2ODEzNywidXNlcl9pZCI6IjE4MDExOTY5NDM0ODI3MjQifQ; _gat_list=1');
		}
		elsif (index($url, 'mgstage.com') > 0) {
			$request->header('Cookie' => 'PHPSESSID=v1eune0m1dnmo3p37r7l1jai92; uuid=7df6f701b55868ba9ccac4f2abe8e49d; coc=1; __ulfpc=201609051947438992; _ga=GA1.2.2022183794.1473076063; adc=1; _gat=1');
		}
		my $response = $ua->request($request);
#               print $response->content."\n\n".$response->decoded_content(charset => 'none')."\n\n";
#		my $content = $response->content;
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
		if (++$retry_count >= 50) {
			print STDERR "enough retries. give up $url\n";
			return "<tr><th>发生错误</th></tr>";
		}
		if (scalar @proxies > 0 && $proxy_idx >= 0) {
			print STDERR "remove proxy ".$proxies[$proxy_idx]."\n";
			my @new_proxies;
			foreach my $proxy (@proxies) {
				push @new_proxies, $proxy if ($proxy ne $proxies[$proxy_idx]);
			}
			@proxies = @new_proxies;
			print STDERR "".(scalar @proxies)." proxies left\n";
		}
	}
}

sub post_url {
	my ($url, $form, $response_headers_only) = @_;
	my $retry_count = 0;
	while (1) {
		print "posting $url ".$json->encode($form)."\n";
		my $proxy_idx = -1;
		if (scalar @proxies <= 5) {
			load_proxy();
		}
		if (index($url, 'https://www.zhihu.com') == 0) {
			my $parameters = form_to_url($form);
			$proxy_idx = int(rand(scalar @proxies));
			my $proxy = $proxies[$proxy_idx];
			return `curl -s -S "$url" -d "$parameters" -x $proxy -H "Host: www.zhihu.com" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0" -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8" -H "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3" --compressed -H "Referer: https://www.zhihu.com/people/wang-ye-80" -H "Cookie: q_c1=f1497e92e7fd4f75b25f144bb2026e50|1463626449000|1458300724000; d_c0=""AGBA2nNzogmPTqWus3-2tPHI1nChmajAoHY=|1458300723""; __utma=155987696.1510435070.1464852944.1464852944.1464852944.1; __utmz=155987696.1464852944.1.1.utmcsr=zhihu.com|utmccn=(referral)|utmcmd=referral|utmcct=/people/wang-ye-80/about; _za=8a7641ba-086c-48ad-b84e-807114e90274; _zap=13ef5654-944a-4ff4-b226-624324edf4f6; _xsrf=cbd73748e7beec5149abbf2d928cfad5; l_n_c=1; l_cap_id=""OThkYjQzNTBiNGFiNGUxYmExYzcxOTYzOTdmNjNmNTI=|1464852098|9d9e40da9002a02acf28a58c4fe9039ce9aec5ba""; cap_id=""MWZlNmRlZmM2ZjczNGU0ZTlmZDdjNDBhMDYzMWRjMzA=|1464852098|e8a7037e69ec5b2f6f232b51ab8feaa572d78516""; n_c=1; __utmb=155987696.5.8.1464853004882; __utmc=155987696; a_t=""2.0AACA6F0sAAAXAAAAb2x3VwAAgOhdLAAAAGBA2nNzogkXAAAAYQJVTZprd1cAJ42MpjkKyZH8DaAbNDgxnemIJsOhs8_TM7cebnyAyz4FpVfeTFcitw==""; z_c0=Mi4wQUFDQTZGMHNBQUFBWUVEYWMzT2lDUmNBQUFCaEFsVk5tbXQzVndBbmpZeW1PUXJKa2Z3Tm9CczBPREdkNllnbXd3|1464852122|f0d0d77ad36834164527ace46ff272b073e86cca; __utmt=1" -H "Connection: keep-alive" -H "Cache-Control: max-age=0" --connect-timeout 6 -m 10`;
		}
		elsif (index($url, 'diggbt') > 0) {
			my $parameters = form_to_url($form);
			return `curl -s -o /dev/null -D - -S "$url" -d "$parameters" -H "Host: diggbt.pw" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0" --connect-timeout 6 -m 10`;
		}
		my $ua = LWP::UserAgent->new;
		$ua->agent("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.4)Gecko/2008111217 Fedora/3.0.4-1.fc10 Firefox/3.0.5");
		if (index($url, 'zhihu.com') > 0 || index($url, 'btkitty') > 0) {
			$proxy_idx = int(rand(scalar @proxies));
			$ua->proxy(['http', 'https'], "http://".$proxies[$proxy_idx].'/');
		}
		my $response = $ua->post($url, $form);
		if ($response->is_success) {
#			return $response->content;
			if (defined($response_headers_only) && $response_headers_only == 1) {
				print "succeed\tpost_url\t$url\t".$response->status_line."\n";
				#print $response->decoded_content;
				return $response->headers->as_string;
			}
			else {
				return $response->decoded_content;
			}
		}
		else {
			if (index($response->status_line, '302') == 0 || index($response->status_line, '301') == 0) {
				print "succeed\tpost_url\t$url\t".$response->status_line."\n";
				return $response->headers->as_string;
			}
			print "failure\tpost_url\t$url\t".$response->status_line."\n";
			if (index($response->status_line, '404') == 0) {
				return '';
			}
			if (++$retry_count >= 50) {
				print "enough retries. give up $url\n";
				return '';
			}
			if (scalar @proxies > 0 && $proxy_idx >= 0) {
				print "remove proxy ".$proxies[$proxy_idx]."\n";
				my @new_proxies;
				foreach my $proxy (@proxies) {
					push @new_proxies, $proxy if ($proxy ne $proxies[$proxy_idx]);
				}
				@proxies = @new_proxies;
				print "".(scalar @proxies)." proxies left\n";
			}
			return $response->status_line;
		}
	}
}

sub execute_scalar {    
	my ($sql, $conn) = @_;  
	$conn = $db_conn if (!defined($conn));
	my $request = $conn->prepare($sql);
	$request->execute();
	my ($result) = $request->fetchrow_array;
	if (defined($result)) {
		return $result;
	}                       
	return '0';     
} 

sub execute_vector {    
	my ($sql, $conn) = @_;  
	$conn = $db_conn if (!defined($conn));
	my $request = $conn->prepare($sql);
	$request->execute();
	return $request->fetchrow_array;
} 

sub execute_column {
	my ($sql, $conn) = @_;  
	$conn = $db_conn if (!defined($conn));
	my $request = $conn->prepare($sql);
	$request->execute();
	my @ret;
	while (my ($result) = $request->fetchrow_array) {
		if (defined($result)) {
			push @ret, $result;
		}
	}                       
	return @ret;     
}

sub execute_dataset {
	my ($sql, $conn) = @_;  
	$conn = $db_conn if (!defined($conn));
	my $request = $conn->prepare($sql);
	$request->execute();
	my @ret;
	while (my @result = $request->fetchrow_array) {
		if (@result) {
			push @ret, \@result;
		}
	}                       
	return \@ret;     
}

sub get_all_boards {
	my @boards;
	my $sql = 'select id, en_name, cn_name from board';
	my $request = $db_conn->prepare($sql);
	$request->execute;
	while (my ($bid, $en_name, $cn_name) = $request->fetchrow_array) {
		push @boards, [$en_name, $cn_name, $bid];
	}
	return @boards;
}

sub get_hot_boards {
	my %boards;
	#my @boards = get_all_boards();
	my @boards = ();
	foreach my $board (@boards) {
		$boards{$board->[0]} = 0;
	}
	my $content = encode('utf-8', decode('big5', get_url($ENV{'board_list_url'})));
	my @slices = split('<table><tr>', $content);
	foreach my $slice (@slices) {
		my $en_name = $1 if ($slice =~ /<td width="120"><a href="\/bbs\/([\w\-]+?)\/index\.html">/);
		my $cn_name = $1 if ($slice =~ /<td width="400"><a href="\/bbs\/[\w\-]+?\/index\.html">\s*([\d\D]+?)\s*<\/a><\/td>/);
		next if (!defined($en_name) || !defined($cn_name));
		next if (defined($boards{$en_name}));
		push @boards, [$en_name, $cn_name];
		print "$en_name\t$cn_name\n";
	}
	push @boards, ['sex', '[西斯]'];
	return @boards;
}

sub update_all_boards {
	foreach my $board (@_) {
		if (execute_scalar("select count(*) from board where en_name = '$board->[0]'") == 0) {
			$db_conn->do("insert into board(en_name, cn_name) values('$board->[0]', '$board->[1]')");
		}
		$board->[2] = execute_scalar("select id from board where en_name = '$board->[0]'");
	}
}

sub get_datetime_str {
	my $ret = `date -d '$_[0] days' '+%F %T'`;
	chomp $ret;
	return $ret;
}

sub get_date_str {
	my $ret = `date -d '$_[0] days' '+%F'`;
	chomp $ret;
	return $ret;
}

sub get_topics {
	my $en_name = $_[0]->[0];
	my $bid = $_[0]->[2];
	my $url = "https://www.ptt.cc/bbs/$en_name/index.html";
	my $continue = 1;
	my $page_count = 0;
	while ($continue) {
		$continue = 0;
		my $content = get_url($url);
		my @slices = split('<div class="r-ent">', $content);
		foreach my $slice (@slices) {
			if ($slice =~ /<a href="\/bbs\/$en_name\/M\.(\d+)\.A\.(\w+)\.html">([\d\D]+?)<\/a>/) {
				$continue |= download_topic($bid, $en_name, $1, $2, $3);
#				$continue = 1;
			}
		}
		if (++$page_count > 10) {
			$continue = 0;
		}
		if ($continue) {
			$url = "https://www.ptt.cc$1" if ($content =~ /href="(\/bbs\/$en_name\/index\d+\.html)">&lsaquo;/);
		}
#		$continue = 0;	# remember to comment out
	}
}

my %en_months = (
'Jan'=>'01',
'Feb'=>'02',
'Mar'=>'03',
'Apr'=>'04',
'May'=>'05',
'Jun'=>'06',
'Jul'=>'07',
'Aug'=>'08',
'Sep'=>'09',
'Oct'=>'10',
'Nov'=>'11',
'Dec'=>'12'
);

my %blocked_users = (
'minekuo' => 0,
'eqer' => 0,
'ioiocala' => 0,
'shibachan' => 0,
'Andersan524' => 0,
'vivi303030' => 0,
'yt1122' => 0,
'a9wh61ks' => 0,
'mmcat1991' => 0,
'jkbull' => 0,
'RAYZY' => 0,
'cat1234f' => 0,
'jwutnpo' => 0,
'tyus' => 0,
'c314333' => 0,
'bella5267' => 0,
'botany' => 0,
'joseph0318' => 0,
'kakoisme' => 0,
'whoam' => 0,
'sunrise1202' => 0,
'hink2003' => 0,
'BOIAN05' => 0,
'XuXin' => 0,
'deathhead' => 0,
'cyijiun' => 0,
'j511042000' => 0,
'Acutie' => 0,
'Ababy' => 0,
'Anmilus' => 0,
'kacey' => 0,
'im014' => 0,
'bomakoto' => 0,
'yungting1989' => 0,
'pierrere' => 0,
'bear15328' => 0,
'OWer' => 0,
'iphonegirl' => 0,
'thisisme' => 0,
'theleo' => 0,
'tinidot' => 0,
'Leverager' => 0,
'aries0419' => 0,
'hjfreehappy' => 0,
'twoice' => 0,
'rrcmjp' => 0,
'sanajp' => 0,
'xjp' => 0,
'aries0419' => 0,
'hollowkiki' => 0,
'weibabe' => 0,
'zu0110' => 0,
'muhsin' => 0,
'miaum6' => 0,
'Iriszhu' => 0,
'FiOnAjAnE' => 0,
'ilovespace' => 0,
'henryhs' => 0,
'temprr' => 0,
'ribaby' => 0,
'snowmom' => 0,
'otscs' => 0,
'iamgali' => 0,
'emc2fma365' => 0,
'kalikali123' => 0,
'holynight123' => 0,
'taco0124' => 0,
'Mykons803' => 0,
'siegee' => 0,
'ted94' => 0,
'a7526746' => 0,
'linlin24' => 0,
'biostat02' => 0,
'uuuc1223' => 0,
'ione123' => 0,
'stu85162' => 0,
'xxxiiixxx' => 0,
'lot' => 0,
'w0919n' => 0,
'openfor75' => 0,
'JhihChao' => 0,
'JSON' => 0,
'Minusheart' => 0,
'keyboard22k' => 0,
'zhanren' => 0,
'viviru' => 0,
'AVIDITY' => 0,
'katy0507' => 0,
'wish15150507' => 0,
'gn00363899' => 0,
'ysl325' => 0,
'JIE8' => 0,
'pc010710' => 0,
'shwpdbg' => 0,
'fantasychiu' => 0,
'samolin' => 0,
'pain99' => 0,
'wewe2152155' => 0,
'zixer' => 0,
'Autherape' => 0,
'angelatim' => 0,
'k40711abc' => 0,
'chachaer' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0,
'' => 0
);

sub month_en_to_number {
	return $en_months{$_[0]};
}
sub download_topic {
	my ($bid, $en_name, $tid1, $tid2, $title) = @_;
	my $ret = 0;
	my $url = "https://www.ptt.cc/bbs/$en_name/M.$tid1.A.$tid2.html";
	my $content = get_url($url);
	my ($user, $nick, $body, $topic_pub_time);
	my @attachments;
	#if ($content =~ /作者<\/span><span>\s*(\w+)\s*\(([\d\D]*?)\)<\/span>/) {
	if ($content =~ /<span class="article-meta-value">(\w+) \(([\d\D]*?)\)<\/span>/) {
		$user = $1;
		$nick = $2;
		$nick = $db_conn->quote($nick);
		if (execute_scalar("select count(*) from `user` where user_id = '$user'") == 0) {
			$db_conn->do("replace into `user`(user_id, nick) values('$user', $nick)");
		}
	}
	if (!defined($user) || !defined($nick)) {
		$user = 'unknown';
		$nick = '';
		print STDERR "parse user failed\t$url\n";
	}
	if (execute_scalar("select count(*) from blocked_user where user_id = '$user'") > 0) {
#	if (defined($blocked_users{$user})) {
		print "blocked user $user\n";
		return 1;
	}
	if ($content =~ /<span class="article-meta-value">\w+ (\w+)\s+(\d+) ([\d:]+) (\d+)<\/span><\/div>\s*([\d\D]+?)\s*\-\-[\d\D]*(<\/span>)?<span class="f2">/) {}
	elsif ($content =~ /<span class="article-meta-value">\w+ (\w+)\s+(\d+) ([\d:]+) (\d+)<\/span><\/div>\s*([\d\D]+?)\s*<div class="push"/) {}
	else {
		print STDERR "parse failed $url $1 $2 $3 $4\n";
		return 1;
	}
	$topic_pub_time = sprintf("%d-%s-%02d %s", $4, month_en_to_number($1), $2, $3);
	my $html = $5;
	$body = $5;
	$body =~ s/<(?:[^>'"]*|(['"]).*?\1)*>//gs;
	while ($html =~ /<img src="(http:\/\/[\w\.\/\-~!@#$%\^&\*\+\?:_=<>]+?)"/g) {
			my $img_url = $1;
			my $img_name = substr($img_url, rindex($img_url, '/') + 1);
			push @attachments, [$img_url, $img_name];
			print "att $img_name $img_url\n";
	}
	download_attachments($bid, $tid1, $tid2, \@attachments);
	if (!defined($user) || !defined($body) || !defined($topic_pub_time) || !defined($title)) {
		print STDERR "parse failed\t$url\t$user\t$topic_pub_time\t$title\n";
		return $ret;
	}
	my $attachment = scalar @attachments == 0 ? 0 : 1;
	if (execute_scalar("select count(*) from topic where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'", $db_conn) == 0) {
		my $sql = "insert delayed into topic(bid, tid1, tid2, author, title, content, pub_time, attachment) values ($bid, $tid1, '$tid2', '$user', ".$db_conn->quote($title).", ".$db_conn->quote($body).", '$topic_pub_time', $attachment)";
		$db_conn->do($sql);
		$ret = 1;
	}
	else {
		my $sql = "update topic set author = '$user', title = ".$db_conn->quote($title).", content = ".$db_conn->quote($body).", attachment = $attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'";
		$db_conn->do($sql);
	}
	print "topic\t$user\t$nick\t$tid1\t$tid2\t$title\n";
	my @articles;
	my @replies = split('<div class="push">', $content);
	foreach my $reply (@replies) {
		if ($reply =~ /<span class="f3 hl push-userid">(\w+)<\/span><span class="f3 push-content">([\d\D]+?)<\/span><span class="push-ipdatetime">\s*(\d+)\/(\d+) ([\d:]+)\s*<\/span>/) {
			my ($user, $reply_content, $month, $day, $time) = ($1, $2, $3, $4, $5);
			$reply_content = substr($reply_content, 1) if (index($reply_content, ':') == 0);
			$reply_content =~ s/^\s+//;
			$reply_content =~ s/\s+$//;
			next if (length($reply_content) == 0);
			my $reply_time = substr($topic_pub_time, 0, 4)."-$month-$day $time:00";
			$reply_time = (substr($topic_pub_time, 0, 4) - 1)."-$month-$day $time:00" if ($topic_pub_time gt $reply_time);
			#if (@articles > 0 && $articles[@articles - 1]->[0] eq $user && $articles[@articles - 1]->[1] eq $reply_time) {
			if (@articles > 0 && $articles[@articles - 1]->[0] eq $user) {
				$articles[@articles - 1]->[2] .= $reply_content;
			}
			else {
				push @articles, [$user, $reply_time, $reply_content];
			}
		}
	}
	return $ret if (execute_scalar("select count(*) from reply where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'", $db_conn) == @articles);
	$ret = 1;
	@articles = sort {$a->[1] cmp $b->[1]} @articles;
	foreach my $article (@articles) {
		print "reply\t$article->[0]\t$article->[1]\t$article->[2]\n";
		save_reply($bid, $tid1, $tid2, $article->[0], $article->[1], $article->[2]);
	}
	return $ret;
}

sub save_reply {
	my ($bid, $tid1, $tid2, $author, $reply_time, $content) = @_;
	if (execute_scalar("select count(*) from reply where bid = $bid and tid1 = $tid1 and tid2 = '$tid2' and author = '$author' and reply_time = '$reply_time'", $db_conn) == 0) {
		my $sql = "insert delayed into reply(bid, tid1, tid2, author, reply_time, content) values($bid, $tid1, '$tid2', '$author', '$reply_time', ".$db_conn->quote($content).")";
		$db_conn->do($sql);
	}
}



sub download_attachments {
	my ($bid, $tid1, $tid2, $attachments) = @_;
	foreach my $attachment (@$attachments) {
		my ($url, $file_name) = @$attachment;
#		next if (execute_scalar("select count(*) from attachment where url = '$url'") > 0);
		next if (execute_scalar("select count(*) from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'") > 0);
		if (index($url, 'http://www.youtube.com/') == 0) {
			next;
		}
		my $ext_name = substr($file_name, rindex($file_name, '.') + 1);
		if (!defined($ext_name) || length($ext_name) < 3 || length($ext_name) > 4) {
			$ext_name = 'jpg';
		}
		#my $md5 = execute_scalar("select md5 from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
		my $md5 = execute_scalar("select md5 from attachment where url = '$url'");
		if (defined($md5) && length($md5) > 5) {
			$db_conn->do("insert delayed into attachment(bid, tid1, tid2, md5, url, ext_name) values($bid, $tid1, '$tid2', '$md5', '$url', '$ext_name')");
			return;
		}
		if (index($attachment->[1], '?') >= 0) {
			$attachment->[1] = Digest::MD5->new->add($attachment->[1])->hexdigest.'.jpg';
		}
		if (rindex($attachment->[1], '.') == -1 || length($attachment->[1]) - rindex($attachment->[1], '.') > 5) {
			$attachment->[1] .= '.jpg';
		}
		my $att_path = $ENV{"pwd"}."/data/att_temp/$bid.$tid1.$tid2";
		my $att_content = get_url($attachment->[0]);
		next if (length($att_content) < 1024 * 10 || length($att_content) == 48373);
		open OUT, ">$att_path";
		binmode(OUT);
		print OUT $att_content;
		close OUT;
		if (`file $att_path | grep HTML | wc -l` > 0) {
			`rm -f $att_path`;
			next;
		}
		$md5 = Digest::MD5->new->add($att_content)->hexdigest;
#		next if (execute_scalar("select count(*) from attachment where md5 = '$md5'") > 0);
		my $target_path = $ENV{"pwd"}."/data/att/$md5.$ext_name";
		system("mv -f $att_path $target_path");
		my $sql = "insert delayed into attachment(bid, tid1, tid2, md5, url, ext_name) values($bid, $tid1, '$tid2', '$md5', '$url', '$ext_name')";
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
		next if (++$boards{$bid} > 3);
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
		print OUT "$category\t$en_name\t$cn_name\t$bid\t$tid\t$title\t$author".join("\t",@attachments)."\n";
	}
	close OUT;
}

my %beauty_bids = (
1735 => 1,		#Beauty
0 => 0
);

sub gen_beauty {
	open OUT, ">../front/data/beauty";
	my $sql = 'select topic.bid, en_name, topic.tid1, topic.tid2, title, concat(md5, ".", ext_name) from board, topic, attachment where topic.bid in ('.join(',', keys(%beauty_bids)).') and board.id = topic.bid and attachment = 1 and topic.bid = attachment.bid and topic.tid1 = attachment.tid1 and topic.tid2 = attachment.tid2 group by topic.bid, topic.tid1, topic.tid2';
	my $request = $db_conn->prepare($sql);
	$request->execute;
	my %file_names;
	while (my ($bid, $en_name, $tid1, $tid2, $title, $file_name) = $request->fetchrow_array) {
		next if (defined($file_names{$file_name}));
		next if (! -e "../data/att/$file_name");
		next if ($beauty_bids{$bid} == 2 && index($title, '正妹') < 0);
		$file_names{$file_name} = 0;
		print OUT "$en_name\t$tid1\t$tid2\t$title\t$file_name\n";
	}
	close OUT;
}

sub update_board_category {
	my $url = 'https://www.ptt.cc/bbs/index.html';
	my $html = get_url($url);
	$html = encode('utf-8', decode('big5', $html));
	while ($html =~ /<a href="\/bbs\/(\d+)\.html">\w_Group \- ([\d\D]+?) /g) {
		my $category = $2;
		$url = "https://www.ptt.cc/bbs/$1.html";
		my $sub_index_html = get_url($url);
		$sub_index_html = encode('utf-8', decode('big5', $sub_index_html));
		while ($sub_index_html =~ /<a href="\/bbs\/(\d+)\.html">\w+/g) {
			$url = "https://www.ptt.cc/bbs/$1.html";
			my $sub_sub_index_html = get_url($url);
			$sub_sub_index_html = encode('utf-8', decode('big5', $sub_sub_index_html));
			while ($sub_sub_index_html =~ /<a href="\/bbs\/([\w\-]+)\/index\.html">([\w\-]+) \- ([\d\D]+?)<\/a>/g) {
				my ($en_name, $cn_name) = ($1, $3);
				$cn_name = $db_conn->quote($cn_name);
				if (execute_scalar("select count(*) from board where en_name = '$en_name'") > 0) {
					$db_conn->do("update board set cn_name = $cn_name, category = '$category' where en_name = '$en_name'");
				}
				else {
					$db_conn->do("insert into board(en_name, cn_name, category) values('$en_name', $cn_name, '$category')");
				}
				print "$category\t$en_name\t$cn_name\n";
			}
		}
	}
}

my %en_name_to_bid = (
'Beauty' => 1735
);

sub gen_ptt_index {
	my $time = `date -d '-12 hours' '+%F %T'`;
	chomp $time;
	my $now = `date '+%F %T'`;
	chomp $now;
	my $sql = "select bid, tid1, tid2 from topic where pub_time between '$time' and '$now'";
	$sql = "select bid, tid1, tid2 from topic order by pub_time desc limit 100";
	my $request = $db_conn->prepare($sql);
	$request->execute;
	while (my ($bid, $tid1, $tid2) = $request->fetchrow_array) {
		$db_conn->do("update topic set rank = (select count(*) from reply where bid = $bid and tid1 = $tid1 and tid2 = '$tid2') where  bid = $bid and tid1 = $tid1 and tid2 = '$tid2'");
	}
	$sql = "select en_name, category, bid, tid1, tid2, title, attachment, author from board, topic where pub_time between '$time' and '$now' and bid = board.id order by rank desc limit 250";
	$request = $db_conn->prepare($sql);
	$request->execute;
	my %categories;
	my %bids;
	while (my ($en_name, $category, $bid, $tid1, $tid2, $title, $attachment, $author) = $request->fetchrow_array) {
#		next if ($en_name eq 'sex');
		next if (++$bids{$bid} > 3);
		my $pa = $categories{$category};
		if (!defined($pa)) {
			$pa = [];
			$categories{$category} = $pa;
		}
		my @attachments;
		if ($attachment) {
				my $req = $db_conn->prepare("select md5, ext_name from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2' limit 2");
				$req->execute;
				while (my ($md5, $ext_name) = $req->fetchrow_array) {
					if (-e "../data/att/$md5.$ext_name") {
						push @attachments, "$md5.$ext_name";
						last if (@attachments >= 2);
					}
				}
		}
		push @$pa, [$en_name, $bid, $tid1, $tid2, $title, $author, join("\t", @attachments)];
	}
	if (!defined($bids{$en_name_to_bid{'Beauty'}})) {
		$sql = "select en_name, category, bid, tid1, tid2, title, attachment, author from board, topic where pub_time > '".get_datetime_str(-1)."' and topic.bid = ".$en_name_to_bid{'Beauty'}." and attachment = 1 and topic.bid = board.id order by rank desc limit 3";
		$request = $db_conn->prepare($sql);
		$request->execute;
		while (my ($en_name, $category, $bid, $tid1, $tid2, $title, $attachment, $author) = $request->fetchrow_array) {
			next if (++$bids{$bid} > 3);
			my $pa = $categories{$category};
			if (!defined($pa)) {
				$pa = [];
				$categories{$category} = $pa;
			}
			my @attachments;
			if ($attachment) {
				my $req = $db_conn->prepare("select md5, ext_name from attachment where bid = $bid and tid1 = $tid1 and tid2 = '$tid2' limit 2");
				$req->execute;
				while (my ($md5, $ext_name) = $req->fetchrow_array) {
					if (-e "../data/att/$md5.$ext_name") {
						push @attachments, "$md5.$ext_name";
						last if (@attachments >= 2);
					}
				}
			}
			push @$pa, [$en_name, $bid, $tid1, $tid2, $title, $author, join("\t", @attachments)];
		}
	}
	open OUT, '>../front/data/ptt_index';
	print OUT $json->encode(\%categories);
	close OUT;
}

sub gen_tianya_index {
	my %threads;
	my $time = `date -d '-12 hours' '+%F %T'`;
	chomp $time;
	$time = '2014-10-03 12:00:00';
	#my $sql = "select en_name, tid, title, user_name from thread, user where thread.uid = user.uid and pub_time > '$time' order by click desc limit 100";
	my $sql = "select en_name, tid, title, uid from thread where pub_time > '$time' order by click desc limit 100";
	my $request = $db_conn->prepare($sql);
	$request->execute;
	while (my ($en_name, $tid, $title, $user_name) = $request->fetchrow_array) {
		my $pa = $threads{$en_name};
		if (!defined($pa)) {
			$pa = [];
			$threads{$en_name} = $pa;
		}
		next if (@$pa > 3);
		push @$pa, [$tid, $title, $user_name];
	}
	open OUT, '>../tianya/data/index';
	print OUT $json->encode(\%threads);
	close OUT;
}

sub add_slashes {
	my $text = shift;
	$text =~ s/\\/\\\\/g;
	$text =~ s/'/\\'/g;
	$text =~ s/"/\\"/g;
	return "'$text'";
}

sub substr_count {
	my ($haystack, $needle) = @_;
	my @matches = $haystack =~ /$needle/g;
	return scalar @matches;
}

sub form_to_url {
	my $form = shift;
	my @arr = ();
	while (my ($key, $value) = each %$form) {
		push @arr, "$key=".uri_escape($value);
	}
	return join('&', @arr);
}

sub to_seconds {
	my $text = shift;
	my $seconds = 0;
	my @arr = split(':', $text);
	for my $arr (@arr) {
		$seconds *= 60;
		$seconds += $arr;
	}
	return $seconds;
}

1;
