launchpad-bugs-stats
====================

Gathers information of our projects bugs reports in launchpad and generates reports


Python Api
==========

Located at /launchpad-api/pull_bugs.py
It gathers bugs information from launchpad REST API and save the data to a JSON file.

Usage:

./pull_bugs.py -o bugs-{launchpad-project-name}.json {launchpad-project-name}
