<?php
declare(strict_types=1);

namespace Majkl578\NetteAddons\Doctrine2Identity\Http;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Majkl578\NetteAddons\Doctrine2Identity\Security\FakeIdentity;
use Nette\Http\Session;
use Nette\Http\UserStorage as NetteUserStorage;
use Nette\Security\IIdentity;

/**
 * @author Michael Moravec
 */
class UserStorage extends NetteUserStorage
{
	/** @var EntityManagerInterface */
	private $entityManager;


	public function __construct(Session $sessionHandler, EntityManagerInterface $entityManager)
	{
		parent::__construct($sessionHandler);

		$this->entityManager = $entityManager;
	}


	/**
	 * Sets the user identity.
	 * @param IIdentity|null $identity
	 * @return NetteUserStorage Provides a fluent interface
	 */
	public function setIdentity(?IIdentity $identity): NetteUserStorage
	{
		if ($identity !== null) {
			$class = ClassUtils::getClass($identity);

			// we want to convert identity entities into fake identity
			// so only the identifier fields are stored,
			// but we are only interested in identities which are correctly
			// mapped as doctrine entities
			if ($this->entityManager->getMetadataFactory()->hasMetadataFor($class)) {
				$cm = $this->entityManager->getClassMetadata($class);
				$identifier = $cm->getIdentifierValues($identity);
				$identity = new FakeIdentity($identifier, $class);
			}
		}

		return parent::setIdentity($identity);
	}


	/**
	 * Returns current user identity, if any.
	 */
	public function getIdentity(): ?IIdentity
	{
		$identity = parent::getIdentity();

		// if we have our fake identity, we now want to
		// convert it back into the real entity
		// returning reference provides potentially lazy behavior
		if ($identity instanceof FakeIdentity) {
			/** @var IIdentity|null $entity */
			$entity = $this->entityManager->getReference($identity->getClass(), $identity->getId());

			// Only return if we are sure that the target entity exists and implements the correct interface
			if ($entity && in_array(IIdentity::class, class_implements($entity, false), true)) {
				return $entity;
			}
		}

		return $identity;
	}
}
