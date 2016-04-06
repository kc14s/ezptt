#!/bin/bash

#while :
#do
	date=`date +%F`
	./ptt_overall_spider.pl >>log/ptt_overall_spider.$date.log 2>>log/ptt_overall_spider.$date.err;
#done
