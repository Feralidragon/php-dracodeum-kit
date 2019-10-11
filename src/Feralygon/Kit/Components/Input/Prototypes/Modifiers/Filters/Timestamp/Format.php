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
 * @property-write string $format [writeonce] [transient] [coercive]
 * <p>The format to convert a given timestamp value into, as supported by the PHP <code>date</code> function, 
 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
 * It cannot be empty.</p>
 * @property-write string|null $timezone [writeonce] [transient] [coercive] [default = null]
 * <p>The timezone to convert a given timestamp value into, 
 * as supported by the PHP <code>date_default_timezone_set</code> function.<br>
 * If not set, then the currently set default timezone is used.<br>
 * If set, then it cannot be empty.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see https://php.net/manual/en/function.date-default-timezone-set.php
 * @see https://php.net/manual/en/class.datetime.php
 * @see https://php.net/manual/en/class.datetimeimmutable.php
 */
class Format extends Filter
{
	//Protected properties
	/** @var string */
	protected $format;
	
	/** @var string|null */
	protected $timezone = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		$value = UTime::format($value, $this->format, $this->timezone, true);
		return isset($value);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('format');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'format':
				return $this->createProperty()->setMode('w--')->setAsString(true)->bind(self::class);
			case 'timezone':
				return $this->createProperty()->setMode('w--')->setAsString(true, true)->bind(self::class);
		}
		return null;
	}
}
