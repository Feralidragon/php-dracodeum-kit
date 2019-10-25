<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
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
 * This constraint prototype restricts a value to a minimum value.
 * 
 * @property-write mixed $value [writeonce] [transient]
 * <p>The minimum allowed value to restrict a given value to (inclusive).</p>
 * @property-write bool $exclusive [writeonce] [transient] [coercive] [default = false]
 * <p>Set the minimum allowed value as exclusive, 
 * restricting a given value to always be greater than the minimum allowed value, but never equal.</p>
 */
class Minimum extends Constraint implements IName, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var mixed */
	protected $value;
	
	/** @var bool */
	protected $exclusive = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateNumber($value) && ($this->exclusive ? $value > $this->value : $value >= $this->value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'minimum';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Minimum allowed value", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$value_string = $this->stringifyValue($this->value, $text_options);
		
		//exclusive
		if ($this->exclusive) {
			/**
			 * @placeholder value The minimum allowed value.
			 * @example Only a value greater than 250 is allowed.
			 */
			return UText::localize(
				"Only a value greater than {{value}} is allowed.",
				self::class, $text_options, ['parameters' => ['value' => $value_string]]
			);
		}
		
		//default
		/**
		 * @placeholder value The minimum allowed value.
		 * @example Only a value greater than or equal to 250 is allowed.
		 */
		return UText::localize(
			"Only a value greater than or equal to {{value}} is allowed.",
			self::class, $text_options, ['parameters' => ['value' => $value_string]]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		$value_string = $this->stringifyValue($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @placeholder value The minimum allowed value.
			 * @example 250 (exclusive)
			 */
			return UText::localize(
				"{{value}} (exclusive)",
				self::class, $text_options, ['parameters' => ['value' => $value_string]]
			);
		}
		return $value_string;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'value' => $this->value,
			'exclusive' => $this->exclusive
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('value');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'value':
				return $this->createProperty()
					->setMode('w--')
					->addEvaluator(\Closure::fromCallable([$this, 'evaluateValue']))
					->bind(self::class)
				;
			case 'exclusive':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Evaluate a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	protected function evaluateValue(&$value): bool
	{
		return true;
	}
	
	/**
	 * Generate a string from a given value.
	 * 
	 * @param mixed $value
	 * <p>The value to generate a string from.</p>
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The generated string from the given value.</p>
	 */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UText::stringify($value, $text_options, ['quote_strings' => true]);
	}
}
