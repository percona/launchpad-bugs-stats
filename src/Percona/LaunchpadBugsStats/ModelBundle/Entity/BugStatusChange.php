<?php

namespace Percona\LaunchpadBugsStats\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Percona\LaunchpadBugsStats\ModelBundle\Entity\BugStatusChange
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Percona\LaunchpadBugsStats\ModelBundle\Repository\BugStatusChange")
 */
class BugStatusChange
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $status
	 *
	 * @ORM\Column(name="status", type="string", length=20)
	 */
	private $status;

	/**
	 * @var \DateTime $ts
	 *
	 * @ORM\Column(name="ts", type="datetime")
	 */
	private $ts;


	# ---- Bug association


	/**
	 * @var Bug $bug
	 *
	 * @ORM\ManyToOne(targetEntity="Bug", inversedBy="bugStatusChanges")
	 * @ORM\JoinColumn(name="bug_id", referencedColumnName="id")
	 */
	private $bug;


	# ----


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId ()
	{
		return $this->id;
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 *
	 * @return BugStatusChange
	 */
	public function setStatus ($status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus ()
	{
		return $this->status;
	}

	/**
	 * Set ts
	 *
	 * @param \DateTime $ts
	 *
	 * @return BugStatusChange
	 */
	public function setTs ($ts)
	{
		$this->ts = $ts;

		return $this;
	}

	/**
	 * Get ts
	 *
	 * @return \DateTime
	 */
	public function getTs ()
	{
		return $this->ts;
	}

	/**
	 * Set bug
	 *
	 * @param \stdClass $bug
	 *
	 * @return BugStatusChange
	 */
	public function setBug ($bug)
	{
		$this->bug = $bug;

		return $this;
	}

	/**
	 * Get bug
	 *
	 * @return \stdClass
	 */
	public function getBug ()
	{
		return $this->bug;
	}
}
