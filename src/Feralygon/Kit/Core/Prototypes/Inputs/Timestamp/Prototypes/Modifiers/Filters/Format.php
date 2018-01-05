<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core timestamp input format filter modifier prototype class.
 * 
 * This input filter modifier prototype converts a timestamp, as an Unix timestamp, into a string using a specific format.
 * 
 * @since 1.0.0
 * @property string $format <p>The format to convert into, as supported by the PHP core <code>date</code> function.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp
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
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateString($value);
					})
					->setGetter(function () : string {
						return $this->format;
					})
					->setSetter(function (string $format) : void {
						$this->format = $format;
					})
				;
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
