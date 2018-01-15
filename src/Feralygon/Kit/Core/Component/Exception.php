<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Component;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core component exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Component|string $component <p>The component instance or class.</p>
 * @see \Feralygon\Kit\Core\Component
 */
abstract class Exception extends Core\Exception
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['component'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'component':
				return UType::evaluateObjectClass($value, Component::class);
		}
		return null;
	}
}
