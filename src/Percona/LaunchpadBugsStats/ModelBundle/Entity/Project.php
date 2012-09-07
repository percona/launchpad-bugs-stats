<?php

namespace Percona\LaunchpadBugsStats\ModelBundle\Entity;

use
	Doctrine\Common\Collections\ArrayCollection as DoctrineCollection,
	Doctrine\ORM\Mapping as ORM;


# ----


/**
 * Percona\LaunchpadBugsStats\ModelBundle\Entity\Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Percona\LaunchpadBugsStats\ModelBundle\Repository\Project")
 */
class Project
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
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @var string $code
	 *
	 * @ORM\Column(name="code", type="string", length=255)
	 */
	private $code;


	# ----


	public function __construct()
	{
		$this->bugs = new DoctrineCollection;
	}

	/**
	 * @var DoctrineCollection
	 * @ORM\OneToMany(targetEntity="Bug", mappedBy="project")
	 */
	private $bugs;


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
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Project
	 */
	public function setName ($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName ()
	{
		return $this->name;
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Project
	 */
	public function setCode ($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode ()
	{
		return $this->code;
	}
}
