<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototype;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Prototype;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core prototype exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Prototype $prototype <p>The prototype instance.</p>
 * @see \Feralygon\Kit\Core\Prototype
 */
abstract class Exception extends Core\Exception
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['prototype'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'prototype':
				return is_object($value) && UType::isA($value, Prototype::class);
		}
		return null;
	}
}
