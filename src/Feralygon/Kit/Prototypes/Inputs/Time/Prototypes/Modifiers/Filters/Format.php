<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * This filter prototype converts a time, as an Unix timestamp, into a string using a specific format.
 * 
 * @since 1.0.0
 * @property string $format
 * <p>The format to convert into, as supported by the PHP <code>date</code> function.<br>
 * It cannot be empty.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see \Feralygon\Kit\Prototypes\Inputs\Time
 */
class Format extends Filter implements IPrototypeProperties
{
	//Private properties
	/** @var string */
	private $format;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value) : bool
	{
		if (is_int($value)) {
			$value = date($this->format, $value);
			return $value !== false;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototype\Interfaces\Properties)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'format':
				return $this->createProperty()->setAsString(true)->bind(self::class);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (Feralygon\Kit\Prototype\Interfaces\Properties)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['format'];
	}
}
