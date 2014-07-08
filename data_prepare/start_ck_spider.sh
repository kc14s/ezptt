#!/bin/bash

while :
do
	date=`date +%F`
	./ck_spider.pl >>log/ck_spider.$date.log 2>>log/ck_spider.$date.err;
done
