#!/bin/bash

while :
do
	date=`date +%F`
	./ty_spider.pl >>log/tianya_spider.$date.log 2>>log/tianya_spider.$date.err;
done
