<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;

/**
 * Core date and time input format filter modifier prototype class.
 * 
 * This filter prototype converts a date and time, as an Unix timestamp, into a string using a specific format.
 * 
 * @since 1.0.0
 * @property string $format <p>The format to convert into, as supported by the PHP core <code>date</code> function.<br>
 * It cannot be empty.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\DateTime
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
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'format':
				return $this->createProperty()->bind($name, self::class)->setAsString(true);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['format'];
	}
}
