<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters\Timestamp;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\Time as UTime;

/**
 * This filter prototype converts a timestamp value into a string or object using a specific format.
 * 
 * @since 1.0.0
 * @property string $format
 * <p>The format to convert a given timestamp value into, as supported by the PHP <code>date</code> function, 
 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
 * It cannot be empty.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see https://php.net/manual/en/class.datetime.php
 * @see https://php.net/manual/en/class.datetimeimmutable.php
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
		$value = UTime::format($value, $this->format, true);
		return isset($value);
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
