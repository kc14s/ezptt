#!/bin/bash

#while :
#do
	date=`date +%F`
	#perl -d:DProf ./ptt_spider.pl >>log/ptt_spider.$date.log 2>>log/ptt_spider.$date.err;
	perl ./ptt_spider.pl >>log/ptt_spider.$date.log 2>>log/ptt_spider.$date.err;
#done
