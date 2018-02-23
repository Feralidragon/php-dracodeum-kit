<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories;

use Feralygon\Kit\Factory;
use Feralygon\Kit\Factory\Objects\Type;
use Feralygon\Kit\Factories\Component\Builders;
use Feralygon\Kit\Components;

/**
 * Component factory class.
 * 
 * This factory is used to build component instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factories\Component\Builders\Input [type = 'input']
 */
class Component extends Factory
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function buildType(string $name) : ?Type
	{
		switch ($name) {
			case 'input':
				return static::createType(Builders\Input::class, Components\Input::class);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Build input instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input|string $prototype <p>The prototype instance, class or name.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input <p>The built input instance.</p>
	 */
	public static function input($prototype, array $prototype_properties = [], array $properties = []) : Components\Input
	{
		return static::build('input', '', $prototype, $prototype_properties, $properties);
	}
}
