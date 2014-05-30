#!/bin/bash

while :
do
	date=`date +%F`
	./spider.pl >>log/spider.$date.log 2>>log/spider.$date.err;
done
