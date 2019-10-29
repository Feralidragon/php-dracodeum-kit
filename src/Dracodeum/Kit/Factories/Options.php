<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories;

use Dracodeum\Kit\Factory;
use Dracodeum\Kit\Factory\Type;
use Dracodeum\Kit\Factories\Options\Builders;
use Dracodeum\Kit\Factories\Options\Builder\Interfaces as BuilderInterfaces;
use Dracodeum\Kit\Options\Text;

/**
 * This factory is used to build options instances.
 * 
 * @see \Dracodeum\Kit\Factories\Options\Builder\Interfaces\Text
 * [builder interface, type = 'text']
 * @see \Dracodeum\Kit\Factories\Options\Builders\Text
 * [builder, type = 'text']
 */
class Options extends Factory
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function buildType(string $name): ?Type
	{
		switch ($name) {
			case 'text':
				return static::createType(BuilderInterfaces\Text::class, Builders\Text::class);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Build text instance.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Options\Text
	 * <p>The built text instance.</p>
	 */
	public static function text(array $properties = []): Text
	{
		return static::build('text', $properties);
	}
}
