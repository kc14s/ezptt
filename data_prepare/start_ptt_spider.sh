#!/bin/bash

#while :
#do
	date=`date +%F`
	./ptt_spider.pl >>log/ptt_spider.$date.log 2>>log/ptt_spider.$date.err;
#done
