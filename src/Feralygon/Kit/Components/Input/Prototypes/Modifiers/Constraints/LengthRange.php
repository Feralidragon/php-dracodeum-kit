<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a value to a range of lengths.
 * 
 * @property-write int $min_length [writeonce] [transient] [coercive]
 * <p>The minimum length to restrict a given value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property-write int $max_length [writeonce] [transient] [coercive]
 * <p>The maximum length to restrict a given value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property-write bool $unicode [writeonce] [transient] [coercive] [default = false]
 * <p>Check a given value as Unicode.</p>
 */
class LengthRange extends Constraint implements IPriority, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $min_length;
	
	/** @var int */
	protected $max_length;
	
	/** @var bool */
	protected $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'length_range';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (UType::evaluateString($value)) {
			$length = UText::length($value, $this->unicode);
			return $length >= $this->min_length && $length <= $this->max_length;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Priority)
	/** {@inheritdoc} */
	public function getPriority(): int
	{
		return 250;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Allowed length range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		/**
		 * @placeholder min_length The minimum allowed length.
		 * @placeholder max_length The maximum allowed length.
		 * @example Only between 5 and 10 characters are allowed.
		 */
		return UText::plocalize(
			"Only between {{min_length}} and {{max_length}} character is allowed.",
			"Only between {{min_length}} and {{max_length}} characters are allowed.",
			$this->max_length, 'max_length', self::class, $text_options, [
				'parameters' => ['min_length' => $this->min_length]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		/**
		 * @placeholder min_length The minimum allowed length.
		 * @placeholder max_length The maximum allowed length.
		 * @example 5 to 10
		 */
		return UText::localize(
			"{{min_length}} to {{max_length}}",
			self::class, $text_options, [
				'parameters' => ['min_length' => $this->min_length, 'max_length' => $this->max_length]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'minimum' => [
				'length' => $this->min_length
			],
			'maximum' => [
				'length' => $this->max_length
			],
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyNames(['min_length', 'max_length']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'min_length':
				//no break
			case 'max_length':
				return $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class);
			case 'unicode':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
