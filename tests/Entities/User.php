<?php declare(strict_types=1);

namespace Majkl578\NetteAddons\Doctrine2Identity\Tests\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\IIdentity;

/**
 * @ORM\Entity
 */
class User implements IIdentity
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column
	 * @var string
	 */
	private $name;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/* implementation of IIdentity */

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	public function getRoles(): array
	{
		return [];
	}
}
