#!/bin/bash

if [[ `ps aux | grep php-cgi | grep -v grep | wc -l` -eq 0 ]]; then
	nohup php-cgi -b 3344 &
	date '+%F %T'
fi
