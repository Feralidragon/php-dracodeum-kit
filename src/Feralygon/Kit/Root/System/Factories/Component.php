<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Factories;

use Feralygon\Kit\Factory;
use Feralygon\Kit\Factory\Type;
use Feralygon\Kit\Root\System\Factories\Component\Builders;
use Feralygon\Kit\Root\System\Factories\Component\Builder\Interfaces as BuilderInterfaces;
use Feralygon\Kit\Root\System\Components\Environment;

/**
 * This factory is used to build component instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System\Factories\Component\Builder\Interfaces\Environment
 * [builder interface, type = 'environment']
 * @see \Feralygon\Kit\Root\System\Factories\Component\Builders\Environment
 * [builder, type = 'environment']
 */
class Component extends Factory
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function buildType(string $name): ?Type
	{
		switch ($name) {
			case 'environment':
				return static::createType(BuilderInterfaces\Environment::class, Builders\Environment::class);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Build environment instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Root\System\Prototypes\Environment|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Root\System\Components\Environment
	 * <p>The built environment instance with the given prototype.</p>
	 */
	public static function environment($prototype, array $properties = []): Environment
	{
		return static::build('environment', $prototype, $properties);
	}
}
