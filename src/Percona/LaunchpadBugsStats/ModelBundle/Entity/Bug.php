<?php

namespace Percona\LaunchpadBugsStats\ModelBundle\Entity;

use
	Doctrine\Common\Collections\ArrayCollection as DoctrineCollection,
	Doctrine\ORM\Mapping as ORM;

/**
 * Percona\LaunchpadBugsStats\ModelBundle\Entity\Bug
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Percona\LaunchpadBugsStats\ModelBundle\Repository\Bug")
 */
class Bug
{
	/**
	 * We don't use an AUTO_INCREMENT for this field, since the ID will be the same as the Launchpad ID
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 */
	private $id;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(name="title", type="string", length=255)
	 */
	private $title;

	/**
	 * @var string $description
	 *
	 * @ORM\Column(name="description", type="text")
	 */
	private $description;

	/**
	 * @var \DateTime $ts
	 *
	 * @ORM\Column(name="ts", type="datetime")
	 */
	private $ts;


	# ---- Project association


	/**
	 * @var Project $project
	 *
	 * @ORM\ManyToOne(targetEntity="Project", inversedBy="bugs")
	 * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
	 */
	private $project;


	# ---- BugStatusChange association


	/**
	 * @var DoctrineCollection
	 * @ORM\OneToMany(targetEntity="BugStatusChange", mappedBy="bug")
	 */
	private $bugStatusChanges;


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
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Bug
	 */
	public function setTitle ($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle ()
	{
		return $this->title;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Bug
	 */
	public function setDescription ($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription ()
	{
		return $this->description;
	}

	/**
	 * Set ts
	 *
	 * @param \DateTime $ts
	 *
	 * @return Bug
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
	 * Set project
	 *
	 * @param \stdClass $project
	 *
	 * @return Bug
	 */
	public function setProject ($project)
	{
		$this->project = $project;

		return $this;
	}

	/**
	 * Get project
	 *
	 * @return \stdClass
	 */
	public function getProject ()
	{
		return $this->project;
	}
}
