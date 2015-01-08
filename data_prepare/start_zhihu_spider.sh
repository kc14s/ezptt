#!/bin/bash

while :
do
	date=`date +%F`
	./zhihu_spider.pl >>log/zhihu_spider.$date.log 2>>log/zhihu_spider.$date.err;
done
