<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories;

use Feralygon\Kit\Factory;
use Feralygon\Kit\Factory\Objects\Type;
use Feralygon\Kit\Factories\Component\Builders;
use Feralygon\Kit\Factories\Component\Builder\Interfaces as BuilderInterfaces;
use Feralygon\Kit\Components\Input;

/**
 * This factory is used to build component instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factories\Component\Builder\Interfaces\Input [builder interface, type = 'input']
 * @see \Feralygon\Kit\Factories\Component\Builders\Input [builder, type = 'input']
 */
class Component extends Factory
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function buildType(string $name) : ?Type
	{
		switch ($name) {
			case 'input':
				return static::createType(BuilderInterfaces\Input::class, Builders\Input::class);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Build input instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input|string $prototype <p>The input prototype instance, 
	 * class or name to build with.</p>
	 * @param array $properties [default = []] <p>The input properties to build with, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param array $prototype_properties [default = []] <p>The input prototype properties to build with, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input <p>The built input instance with the given prototype.</p>
	 */
	public static function input($prototype, array $properties = [], array $prototype_properties = []) : Input
	{
		return static::build('input', $prototype, $properties, $prototype_properties);
	}
}
