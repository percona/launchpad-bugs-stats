for i in percona-server percona-xtrabackup percona-xtradb-cluster percona-toolkit
do
python bug-stats.py $i >> $i.txt
done
