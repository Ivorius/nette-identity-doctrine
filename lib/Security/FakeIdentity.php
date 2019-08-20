<?php declare(strict_types=1);

namespace Majkl578\NetteAddons\Doctrine2Identity\Security;

/**
 * @author Michael Moravec
 */
class FakeIdentity implements \Nette\Security\IIdentity
{
	/** @var mixed */
	private $id;

	/** @var string */
	private $class;

	public function __construct($id, string $class)
	{
		$this->id = $id;
		$this->class = $class;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getClass(): string
	{
		return $this->class;
	}

	public function getRoles(): array
	{
		return [];
	}
}
