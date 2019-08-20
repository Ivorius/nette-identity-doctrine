<?php declare(strict_types=1);

namespace Majkl578\NetteAddons\Doctrine2Identity\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Security\IUserStorage;
use Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage;

/**
 * @author Michael Moravec
 */
class IdentityExtension extends CompilerExtension
{
	const NAME = 'doctrine2identity';

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$userStorageDefinitionName = $builder->getByType(IUserStorage::class) ?: 'nette.userStorage';
		/** @var ServiceDefinition $definition */
		$definition = $builder->getDefinition($userStorageDefinitionName);
		$definition->setFactory(UserStorage::class);
	}


	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $sender, Compiler $compiler) {
			$compiler->addExtension(IdentityExtension::NAME, new IdentityExtension());
		};
	}
}
