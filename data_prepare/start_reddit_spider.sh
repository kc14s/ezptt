#!/bin/bash

while :
do
	date=`date +%F`
	./reddit_spider.pl >>log/reddit_spider.$date.log 2>>log/reddit_spider.$date.err;
done
