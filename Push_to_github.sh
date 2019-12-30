#!/bin/bash
git add .
msg=`date`
git commit -a -m "$msg"
git push origin master
