#!/usr/bin/python

from __future__ import print_function

from datetime import datetime
import json
from optparse import OptionParser
import pytz
import sys
import os

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


def get_bug_data(project, options):
    all_tasks = []
    information_types = public_information_types
    if options.private:
        information_types = information_types + private_information_types
    found_tasks = project.searchTasks(
        status=options.statuses, modified_since=options.since,
        importance=options.importance, milestone=options.milestone,
        tags=options.tags, tags_combinator='All')
    for task in found_tasks:
        json_data = dict(task._wadl_resource.representation)
        bug = task.bug
        json_data[u'bug_id'] = bug.id
        json_data[u'bug_title'] = bug.title
        json_data[u'bug_description'] = bug.description
        json_data[u'bug_information_type'] = bug.information_type
        json_data[u'bug_tags'] = bug.tags
        all_tasks.append(json_data)
    return all_tasks


def merge_bug_data(bug_data, options):
    with open(options.union_file, 'r') as in_file:
        previous_json = in_file.read()
    previous_data = json.loads(previous_json)
    previous_bugs = dict([(b['bug_id'], b) for b in previous_data['bugs']])
    for bug in bug_data:
        previous_bugs[bug['bug_id']] = bug
    return previous_bugs.values()


def get_json_data(project, options):
    project = lp.projects[project]
    bug_data = get_bug_data(project, options)
    print(len(bug_data))
    if options.union_file:
        bug_data = merge_bug_data(bug_data, options)
        print(len(bug_data))
    now = datetime.now(pytz.utc)
    json_source = {
        u'date_retrieved': now.isoformat(' '),
        u'bugs': bug_data,
        }
    json_data = json.dumps(json_source, indent=0)
    with open(options.outfile, 'w') as json_file:
        json_file.write(json_data)


def get_option_parser():
    """Return the option parser for this program."""
    example = "eg. %prog -t oops -t 404 -s Critical -s High -m 1.1 my-project"
    usage = "usage: %%prog [options] project\n%s" % example
    parser = OptionParser(usage=usage)
    parser.add_option(
        "-r", "--service-root", dest="root",
        help="The service to use.")
    parser.add_option(
        "-o", "--outfile", dest="outfile",
        help="The name of the file to write the json data to.")
    parser.add_option(
        "-u", "--union-file", dest="union_file",
        help="The name of the file to merge the new json data with.")
    parser.add_option(
        "-c", "--changed-since", dest="since",
        help="Match bugs reported changed after iso-date (2012-09-28).")
    parser.add_option(
        "-p", "--private", dest="private", action="store_true",
        help="Match bugs that are private.")
    parser.add_option(
        "-t", "--tags", dest="tags", action="append", type="string",
        help="Match bugs with all of the listed tags.")
    parser.add_option(
        "-m", "--milestone", dest="milestone",
        help="Match bugs targeted to a milestone.")
    parser.add_option(
        "-i", "--importance", dest="importance", action="append",
        type="string", help="Match bugs with all of the listed importances.")
    parser.add_option(
        "-s", "--statuses", dest="statuses", action="append",
        type="string", help="Match bugs with all of the listed statues.")
    parser.set_defaults(
        root='https://api.launchpad.net',
        outfile='bug-data.json',
        union_file=None,
        since=None,
        private=False,
        tags=None,
        milestone=None,
        importance=None,
        statuses=all_bug_statuses
        )
    return parser


if __name__ == '__main__':
    argv = sys.argv
    parser = get_option_parser()
    (options, args) = parser.parse_args(args=argv[1:])
    if len(args) < 1:
        print(parser.usage)
        sys.exit(1)
    project = args[0]
    cachedir = os.path.realpath(__file__) + "/../app/cache/launchpad-api"
    lp = Launchpad.login_anonymously(
        'just-me', 'production',
        version='devel')
    get_json_data(project, options)
