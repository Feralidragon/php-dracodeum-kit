<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Options;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core options exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Options|string $options <p>The options instance or class.</p>
 * @see \Feralygon\Kit\Core\Options
 */
abstract class Exception extends Core\Exception
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['options'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'options':
				return UType::evaluateObjectClass($value, Options::class);
		}
		return null;
	}
}
