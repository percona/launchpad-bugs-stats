launchpad-bugs-stats
====================

Gathers information of our projects bugs reports in launchpad and generates reports


Python Api
==========

Located at **/launchpad-api/pull_bugs.py**
It gathers bugs information from launchpad REST API and save the data to a JSON file.

Usage:

    ./pull_bugs.py -o {output-file}.json {launchpad-project-name}

Or for more convenience, you can use the pull-all-bugs.sh script to pull all the projects bugs at once.

    ./pull-all-bugs.sh