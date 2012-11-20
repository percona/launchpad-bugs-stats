#!/bin/bash

#
# Pull bugs for all projects and save the result in JSON files under 'cache' directory
#

# All the percona projects
ALL_PROJECTS="toolkit xtradb-cluster server xtrabackup"

for PROJECT in $ALL_PROJECTS
do
	echo "./pull-bugs.py -o $PWD/result/bugs-percona-$PROJECT.json percona-$PROJECT"
done
