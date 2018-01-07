<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input\Exception;

/**
 * Core input component invalid filter exception class.
 * 
 * This exception is thrown from an input whenever a given filter is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $filter <p>The filter.</p>
 */
class InvalidFilter extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid filter {{filter}} in input {{component}} (with prototype {{prototype}}).\n" . 
			"HINT: Only a filter component instance, prototype instance, class or name is allowed.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['filter']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'filter':
				return true;
		}
		return parent::evaluateProperty($name, $value);
	}
}
