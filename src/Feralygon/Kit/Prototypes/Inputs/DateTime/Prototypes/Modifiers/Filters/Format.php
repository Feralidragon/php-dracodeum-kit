<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * This filter prototype converts a date and time, as an Unix timestamp, into a string using a specific format.
 * 
 * @since 1.0.0
 * @property string $format
 * <p>The format to convert a given date and time into, as supported by the PHP <code>date</code> function.<br>
 * It cannot be empty.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime
 */
class Format extends Filter
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNames)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames() : void
	{
		$this->addRequiredPropertyNames(['format']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\Properties)
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'format':
				return $this->createProperty()->setAsString(true)->bind(self::class);
		}
		return null;
	}
}
