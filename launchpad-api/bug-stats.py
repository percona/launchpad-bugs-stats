#!/usr/bin/python

from __future__ import print_function

from datetime import datetime
import sys
import os
import time

from launchpadlib.launchpad import Launchpad


all_bug_statuses = [
    'Invalid',
    "Won't Fix",
    'Incomplete (with response)',
    'Incomplete (without response)',
    'Incomplete',
    'Opinion',
    'Expired',
    'New',
    'Confirmed',
    'Triaged',
    'In Progress',
    'Fix Committed',
    'Fix Released',
    ]
my_bug_statuses = [
    'New',
    'Confirmed',
    'Triaged',
    'In Progress',
    'Fix Committed',
    'Fix Released',
    ]

public_information_types = [
    'Public',
    'Public Security',
    ]
private_information_types = [
    'Private',
    'Private Secruity',
    'Proprietary',
    'Embargoed',
    ]

if __name__ == '__main__':
    argv = sys.argv
    args=argv[1:]
    if len(args) < 1:
        sys.exit(1)
    project = args[0]
    cachedir = os.path.realpath(__file__) + "/../app/cache/launchpad-api"
    lp = Launchpad.login_anonymously(
        'just-me', 'production',
        version='devel')
    pillar = lp.projects[project]
    sys.stdout.write(time.strftime("%m/%d/%Y %H:%M")+",")
    for i in my_bug_statuses:
    	fixed_bugtasks = pillar.searchTasks( status=[i])
    	sys.stdout.write(str(len(fixed_bugtasks))+",")
    print("")
