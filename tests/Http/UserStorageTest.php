<?php
declare(strict_types=1);

namespace Majkl578\NetteAddons\Doctrine2Identity\Tests\Http;

use Doctrine\ORM\EntityManager;
use Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage;
use Majkl578\NetteAddons\Doctrine2Identity\Tests\ContainerFactory;
use Majkl578\NetteAddons\Doctrine2Identity\Tests\DatabaseLoader;
use Majkl578\NetteAddons\Doctrine2Identity\Tests\Entities\User;
use Nette\DI\Container;
use Nette\Security\Identity;
use Nette\Security\IUserStorage;
use PHPUnit_Framework_TestCase;

class UserStorageTest extends PHPUnit_Framework_TestCase
{
	public const ENTITY_IDENTITY = 'Majkl578\NetteAddons\Doctrine2Identity\Tests\Entities\User';

	/** @var Container */
	private $container;

	/** @var IUserStorage */
	private $userStorage;

	/** @var EntityManager */
	private $entityManager;

	/** @var DatabaseLoader */
	private $databaseLoader;


	public function __construct()
	{
		parent::__construct();
		$containerFactory = new ContainerFactory;
		$this->container = $containerFactory->create();
	}


	protected function setUp()
	{
		$this->userStorage = $this->container->getByType(IUserStorage::class) ?:
			$this->container->getService('nette.userStorage');
		$this->entityManager = $this->container->getByType(EntityManager::class);
		$this->databaseLoader = $this->container->getByType(DatabaseLoader::class);
	}


	public function testInstance()
	{
		$this->assertInstanceOf(IUserStorage::class, $this->userStorage);
		$this->assertInstanceOf(UserStorage::class, $this->userStorage);
	}


	public function testGetIdentity()
	{
		$this->assertNull($this->userStorage->getIdentity());
	}


	public function testSetIdentity()
	{
		$this->userStorage->setIdentity(new Identity(1));
	}


	public function testSetEntityProxyIdentity()
	{
		$this->databaseLoader->loadUserTableWithOneItem();
		$userRepository = $this->entityManager->getRepository(self::ENTITY_IDENTITY);
		$allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
		$this->entityManager->getProxyFactory()->generateProxyClasses($allMetadata);

		/** @var User|null $userProxy */
		$userProxy = $this->entityManager->getProxyFactory()->getProxy(self::ENTITY_IDENTITY, ['id' => 1]);

		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($userProxy);
		$this->assertInstanceOf(IUserStorage::class, $userStorage);
		$this->assertInstanceOf(UserStorage::class, $userStorage);

		$userIdentity = $userStorage->getIdentity();

		$this->assertSame($user, $userIdentity);
		$this->assertNotSame($userProxy, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame([], $userIdentity->getRoles());
	}


	public function testEntityIdentity()
	{
		$this->databaseLoader->loadUserTableWithOneItem();
		$userRepository = $this->entityManager->getRepository(self::ENTITY_IDENTITY);
		$user = $userRepository->find(1);

		$userStorage = $this->userStorage->setIdentity($user);
		$this->assertInstanceOf(IUserStorage::class, $userStorage);
		$this->assertInstanceOf(UserStorage::class, $userStorage);

		$userIdentity = $userStorage->getIdentity();
		$this->assertSame($user, $userIdentity);
		$this->assertSame(1, $userIdentity->getId());
		$this->assertSame([], $userIdentity->getRoles());
	}
}
