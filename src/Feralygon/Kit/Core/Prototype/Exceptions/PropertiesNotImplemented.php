<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototype\Exceptions;

use Feralygon\Kit\Core\Prototype;
use Feralygon\Kit\Core\Prototype\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core prototype properties not implemented exception class.
 * 
 * This exception is thrown from a prototype whenever properties are given but there is nothing implemented to handle them.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Prototype $prototype <p>The prototype instance.</p>
 */
class PropertiesNotImplemented extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties not implemented in prototype {{prototype}}.\n" . 
			"HINT: In order to use properties, the properties interface must be implemented by this prototype.";
	}
	
	
	
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
