<?php

namespace Percona\LaunchpadBugsStats\EngineBundle\Services\LaunchpadApi;

# Http client API
use Guzzle\Service\Client as HttpClient;

# Services
use Symfony\Bridge\Monolog\Logger;


# ----


class LaunchpadApi
{

	/**
	 * Path for getting information about bugs.
	 */
	const BASE_URL_BUGS = 'https://bugs.launchpad.net/{project-name}/+bugs/';

	const BASE_URL_BUG  = 'https://api.launchpad.net/1.0/bugs/{bug-id}';


	# ----


	public function __construct()
	{

	}


	# ---- Public API


	/**
	 * Returns an array containing all the bugs of a project
	 *
	 * Example project names are:
	 * - percona-xtradb-cluster
	 * - percona-server
	 * - percona-toolkit
	 * - percona-xtrabackup
	 *
	 * @param $projectName
	 *
	 * Each bug is a collection like this:
	 * {
	 *   "status":  "Fix Committed",
	 *   "title":   "xtrabackup SST netcat \"-d\" option not available on Debian Squeeze",
	 *   "id":      959970,
	 *   "created": "2012-08-28T03:22:57.942174+00:00",
	 * }
	 */
	public function getBugsOfProject($projectName)
	{
		$this->debug(__METHOD__."($projectName)");
		$all_bugs = array();

		do
		{
			# Request a page of bugs
			$page = $this->getOnePageOfProjectBugs($projectName, \count($all_bugs));
			$this->debug("Got " . \count($page['bugs']) . " bugs of {$page['total']}");

			# Filter the data
			$page_bugs = $this->slashCollection($page['bugs'], array('status', 'title', 'id'));
			# $this->debug("Filtered Bugs:".\var_export($page_bugs, 1));

			# Merge with all result
			$all_bugs = array_merge($all_bugs, $page_bugs);

		} while ( \count($page_bugs) );

		$this->debug("Returning " . \count($all_bugs) . " bugs");
		return $all_bugs;
	}

	/**
	 * Returns an object which contains the bug information as returned by the launchpad REST API
	 * but filtered to contain only useful information
	 *
	 * @param $bugId
	 *
	 * @return StdClass
	 */
	public function getBugInformation($bugId)
	{
		$this->debug(__METHOD__."($bugId)");

		$url = str_replace('{bug-id}', $bugId, self::BASE_URL_BUG);
		$http = $this->get($url);

		if ( $http->request['wasSuccessful'] )
		{
			# $this->debug($http->response['body']);
			$data = json_decode($http->response['body']);

			if ( $data )
			{

				$result = $this->slash(
					$data,
					array ( 'id', 'title', 'description', 'date_created' )
				);
				$this->debug("Got bug #{$result->id}|{$result->date_created}|'".\substr($result->title,0,15)."...'");
				return $result;


			} else $this->fail("Invalid JSON");

		} else $this->fail("Request error: {$http->response->status_phrase}");
	}

	/**
	 * This method merges all the bugs information gathered from the two Launchpad APIs.
	 * The returned array, contains bugs with the following keys:
	 * - 'id', 'status', 'title', 'description', 'date_created'
	 *
	 * @param $projectName
	 *
	 * @return array
	 */
	public function getFullBugsOfProject($projectName)
	{
		$this->debug(__METHOD__."($projectName)");
		$bugs = $this->getBugsOfProject($projectName);

		# Iterate over all bugs to expand the information calling a second API
		foreach ($bugs as $k=>$bug)
		{
			$this->debug("Expanding bug#{$bug->id}");
			$detailedBugInfo = $this->getBugInformation($bug->id);
			$bugs[$k] = $this->mergeObjects($bug, $detailedBugInfo);

			# Sleep 1 second to prevent getting banned
			sleep(1);
		}
		return $bugs;
	}


	# ---- Private Helpers


	private function mergeObjects(\StdClass $o1, \StdClass $o2)
	{
		return (object) array_merge((array) $o1, (array) $o2);
	}

	private function slashCollection($collection, $keys)
	{
		$result = array();
		foreach ($collection as $k => $item)
		{
			$result[$k] = $this->slash($item, $keys);
		}
		return $result;
	}

	/**
	 * Returns an array|object that contains the same key->value pairs than $collection, but filtered to only contain the keys $keys
	 *
	 * @param array|\StdClass $collection
	 * @param array $keys
	 */
	private function slash($collection, array $keys)
	{
		if ( $collection instanceof \StdClass )
		{
			$result = new \StdClass;
			foreach ($keys as $k)
			{
				$result->$k = $collection->$k;
			}
			return $result;
		}

		if ( \is_array($collection) )
		{
			$result = array();
			foreach ($keys as $k)
			{
				$result[$k] = $collection[$k];
			}
			return $result;
		}

		throw new \RuntimeException("Bug", __LINE__);
	}

	/**
	 * Returns one page of the bugs collection, as returned by the JSON API, but filtered to contain only the relevan information
	 *
	 * Each bug is an item like the following, but we just care about status, title and id
	 * {
	 *   "bugtarget": "Percona XtraDB Cluster",
	 *   "status": "Fix Committed",
	 *   "last_updated": "on 2012-06-08",
	 *   "bugtarget_css": "sprite product",
	 *   "reporter": "Chris Boulton",
	 *   "importance": "High",
	 *   "title": "xtrabackup SST netcat \"-d\" option not available on Debian Squeeze",
	 *   "age": "171 days old",
	 *   "tags": [],
	 *   "bug_heat_html": "<span class=\"sprite flame\">6</span>",
	 *   "importance_class": "importanceHIGH",
	 *   "milestone_name": null,
	 *   "assignee": "Ignacio Nin",
	 *   "has_tags": false,
	 *   "information_type": "Public",
	 *   "status_class": "statusFIXCOMMITTED",
	 *   "id": 959970,
	 *   "badges": "",
	 *   "bug_url": "https://bugs.launchpad.net/percona-xtradb-cluster/+bug/959970"
	 * },
	 *
	 * @param $projectName
	 */
	public function getOnePageOfProjectBugs($projectName, $from='0')
	{
		$url = str_replace('{project-name}', $projectName, self::BASE_URL_BUGS);
		$url .= "++model++?orderby=id&start={$from}";

		$http = $this->get($url);

		if ( $http->request['wasSuccessful'] )
		{
			# $this->debug($http->response['body']);
			$data = json_decode($http->response['body']);

			if ( $data )
			{
				$this->debug("Total number of bugs: {$data->total}");

				$this_page_bugs = $data->mustache_model->items;
				$this->debug("This page number of bugs: ".\count($this_page_bugs));

				return array (
					'total' => $data->total,
					'bugs'  => $this_page_bugs
				);

			} else $this->fail("Invalid JSON");

		} else $this->fail("Request error: {$http->response->status_phrase}");

	}

	private function fail($message)
	{
		$this->error($message);
		throw new \RuntimeException($message, __LINE__);
	}

	/**
	 * Makes an HTTP GET request and returns an object with the following information:
	 * - request
	 *   - url
	 * - response
	 *   - status
	 *   - body
	 * @param $url
	 *
	 * @return \StdClass
	 */
	private function get($url)
	{
		$http = new HttpClient();

		/** @var $request \Guzzle\Http\Message\Request */
		$request = $http->get($url);

		$this->debug("HTTP Request: {$request->getUrl()}");
		$response = $request->send();
		$this->debug("Status: {$response->getStatusCode()}");

		$result = new \StdClass;
		$result->request = array (
			'url' => $request->getUrl(),
			'wasSuccessful' => (200 === $response->getStatusCode())
		);
		$result->response = array (
			'status' => $response->getStatusCode(),
			'body'   => $response->getBody(),
			'status_phrase' => $response->getReasonPhrase(),
		);
		return $result;
	}


	# ---- Logs


	private $Logger;

	public function setLogger(Logger $Logger)
	{
		$this->Logger = $Logger;
	}

	private function debug($txt)
	{
		$this->Logger->debug($txt);
	}

	private function error($txt)
	{
		$this->Logger->err($txt);
	}
}