<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filters\Timestamp;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Time as UTime;

/**
 * This filter prototype converts a given input timestamp value into a string or object using a specific format.
 * 
 * @property-write string $value [writeonce] [transient]
 * <p>The format value to convert a given input timestamp value into, 
 * as supported by the PHP <code>date</code> function, 
 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.</p>
 * @property-write string|null $timezone [writeonce] [transient] [default = null]
 * <p>The timezone to convert a given input timestamp value into, 
 * as supported by the PHP <code>date_default_timezone_set</code> function.<br>
 * If not set, then the currently set default timezone is used.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see https://php.net/manual/en/function.date-default-timezone-set.php
 * @see https://php.net/manual/en/class.datetime.php
 * @see https://php.net/manual/en/class.datetimeimmutable.php
 */
class Format extends Filter implements ISubtype, ISchemaData
{
	//Protected properties
	/** @var string */
	protected $value;
	
	/** @var string|null */
	protected $timezone = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'format';
	}
	
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		$value = UTime::format($value, $this->value, $this->timezone, true);
		return isset($value);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'timestamp';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'value' => $this->value,
			'timezone' => $this->timezone
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('value');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'value':
				return $this->createProperty()->setMode('w--')->setAsString(true)->bind(self::class);
			case 'timezone':
				return $this->createProperty()->setMode('w--')->setAsString(true, true)->bind(self::class);
		}
		return null;
	}
}
