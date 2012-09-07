<?php

namespace Percona\LaunchpadBugsStats\EngineBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Percona\LaunchpadBugsStats\EngineBundle\Services\LaunchpadApi\LaunchpadApi;

class LaunchpadApiTest extends WebTestCase
{
	private $container;

	/**
	 * @var LaunchpadApi
	 */
	private $api;

	public function setUp()
	{
		$client = static::createClient();
		$this->container = $client->getContainer();
		$this->api = $this->container->get('percona.launchpad_api');
	}

	public function testServiceExists()
	{
		$this->assertInstanceOf(
			'\\Percona\\LaunchpadBugsStats\\EngineBundle\\Services\\LaunchpadApi\\LaunchpadApi',
			$this->api
		);
	}

	public function testReturnedInfo()
	{
		$projectName = 'percona-server';
		$bugs = $this->api->getBugsOfProject($projectName);
		$this->assertInternalType('array', $bugs);

		# At this moment percona-server has 290 bugs
		$minimumNumberOfBugs = 290;
		$this->assertGreaterThanOrEqual(
			$minimumNumberOfBugs,
			\count($bugs),
			"{$projectName} should return at least {$minimumNumberOfBugs} bugs, but got " . \count($bugs)
		);

		foreach ($bugs as $bug)
		{
			# $this->debug($bug);
			// ToDo: test for 'created' index
			$this->assertObjectHasAttributes(
				array('id', 'status', 'title'),
				$bug
			);
		}
	}


	# ---- Helpers


	private function assertObjectHasAttributes(array $attributes, $object)
	{
		foreach ($attributes as $attribute)
		{
			$this->assertObjectHasAttribute($attribute, $object);
		}
	}

	private function debug($anything)
	{
		\fwrite(STDOUT, PHP_EOL.var_export($anything,1));
	}
}
