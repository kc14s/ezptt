#!/bin/bash

#while :
#do
	date=`date +%F`
	./db_spider.pl >>log/db_spider.$date.log 2>&1;
#done
