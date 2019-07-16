#!/bin/bash
#
# Need optipng and jpegoptim installed

find $1 -type f -name "*.jpg" -exec jpegoptim --strip-all {} \;
find $1 -type f -name "*.jpeg" -exec jpegoptim --strip-all {} \;
find $1 -type f -name "*.png" -exec optipng -fix -o2 {} \;


