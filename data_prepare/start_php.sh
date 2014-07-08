#!/bin/bash

#while :
#do
#	php-cgi -b 3344
#done

spawn-fcgi -f /usr/bin/php-cgi -a 127.0.0.1 -p 3344 -C 10
