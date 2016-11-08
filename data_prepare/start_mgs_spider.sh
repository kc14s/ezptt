#!/bin/bash

date=`date +%F`
./mgs_spider.pl >>log/mgs_spider.$date.log 2>&1;
