<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a given input value to an exact length.
 * 
 * @property-write int $value [writeonce] [transient]
 * <p>The length value to restrict a given input value to.</p>
 * @property-write bool $unicode [writeonce] [transient] [default = false]
 * <p>Check a given input value as Unicode.</p>
 */
class Length extends Constraint implements IPriority, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $value;
	
	/** @var bool */
	protected $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'length';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateString($value) && UText::length($value, $this->unicode) === $this->value;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Priority)
	/** {@inheritdoc} */
	public function getPriority(): int
	{
		return 250;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Allowed length", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		/**
		 * @placeholder value The allowed length value.
		 * @example Only exactly 10 characters are allowed.
		 */
		return UText::plocalize(
			"Only exactly {{value}} character is allowed.",
			"Only exactly {{value}} characters are allowed.",
			$this->value, 'value', self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::stringify($this->value, $text_options);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'value' => $this->value,
			'unicode' => $this->unicode
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
				return $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class);
			case 'unicode':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
