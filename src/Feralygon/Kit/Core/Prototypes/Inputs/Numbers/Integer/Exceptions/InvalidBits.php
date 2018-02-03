<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer\Exceptions;

use Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer\Exception;
use Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core integer number input prototype invalid bits exception class.
 * 
 * This exception is thrown from an integer number input whenever a given number of bits is invalid.
 * 
 * @since 1.0.0
 * @property-read int $bits <p>The number of bits.</p>
 * @property-read int $max_bits <p>The maximum allowed number of bits.</p>
 * @property-read \Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer $prototype 
 * <p>The integer number input prototype instance.</p>
 * @property-read bool $unsigned [default = false] <p>Handle as an unsigned integer.</p>
 */
class InvalidBits extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid bits {{bits}} in integer number input {{prototype}}.\n" . (
			$this->is('unsigned')
				? "HINT: Only up to {{max_bits}} bits are allowed for unsigned integers."
				: "HINT: Only up to {{max_bits}} bits are allowed for signed integers."
		);
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['bits', 'max_bits', 'prototype'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'bits':
				//no break
			case 'max_bits':
				return UType::evaluateInteger($value);
			case 'prototype':
				return is_object($value) && UType::isA($value, Integer::class);
			case 'unsigned':
				return UType::evaluateBoolean($value);
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getDefaultPropertyValue(string $name)
	{
		switch ($name) {
			case 'unsigned':
				return false;
		}
		return parent::getDefaultPropertyValue($name);
	}
}
