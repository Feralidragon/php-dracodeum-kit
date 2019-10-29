<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Factories;

use Dracodeum\Kit\Factory;
use Dracodeum\Kit\Factory\Type;
use Dracodeum\Kit\Root\System\Factories\Component\Builders;
use Dracodeum\Kit\Root\System\Factories\Component\Builder\Interfaces as BuilderInterfaces;
use Dracodeum\Kit\Root\System\Components\Environment;

/**
 * This factory is used to build component instances.
 * 
 * @see \Dracodeum\Kit\Root\System\Factories\Component\Builder\Interfaces\Environment
 * [builder interface, type = 'environment']
 * @see \Dracodeum\Kit\Root\System\Factories\Component\Builders\Environment
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
	 * @param \Dracodeum\Kit\Root\System\Prototypes\Environment|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs, if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Root\System\Components\Environment
	 * <p>The built environment instance with the given prototype.</p>
	 */
	public static function environment($prototype, array $properties = []): Environment
	{
		return static::build('environment', $prototype, $properties);
	}
}
