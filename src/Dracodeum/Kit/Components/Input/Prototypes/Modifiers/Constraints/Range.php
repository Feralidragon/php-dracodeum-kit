<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
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
 * This constraint prototype restricts a given input value to a range of values.
 * 
 * @property-write mixed $min_value [writeonce] [transient]
 * <p>The minimum allowed value to restrict a given input value to (inclusive).</p>
 * @property-write mixed $max_value [writeonce] [transient]
 * <p>The maximum allowed value to restrict a given input value to (inclusive).</p>
 * @property-write bool $min_exclusive [writeonce] [transient] [default = false]
 * <p>Set the minimum allowed value as exclusive, 
 * restricting a given input value to always be greater than the minimum allowed value, but never equal.</p>
 * @property-write bool $max_exclusive [writeonce] [transient] [default = false]
 * <p>Set the maximum allowed value as exclusive, 
 * restricting a given input value to always be less than the maximum allowed value, but never equal.</p>
 * @property-write bool $negate [writeonce] [transient] [default = false]
 * <p>Negate the restriction condition, so the given allowed range of values behaves as a disallowed range of values 
 * instead.</p>
 */
class Range extends Constraint implements IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var mixed */
	protected $min_value;
	
	/** @var mixed */
	protected $max_value;
	
	/** @var bool */
	protected $min_exclusive = false;
	
	/** @var bool */
	protected $max_exclusive = false;
	
	/** @var bool */
	protected $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'range';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateNumber($value) && $this->negate !== (
			($this->min_exclusive ? $value > $this->min_value : $value >= $this->min_value) && 
			($this->max_exclusive ? $value < $this->max_value : $value <= $this->max_value)
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::localize("Disallowed value range", self::class, $text_options)
			: UText::localize("Allowed value range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		
		//negate
		if ($this->negate) {
			//min and max exclusive
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a value less than or equal to 100 or greater than or equal to 250 is allowed.
				 */
				return UText::localize(
					"Only a value less than or equal to {{min_value}} or " . 
						"greater than or equal to {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//min exclusive
			if ($this->min_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a value less than or equal to 100 or greater than 250 is allowed.
				 */
				return UText::localize(
					"Only a value less than or equal to {{min_value}} or " . 
						"greater than {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//max exclusive
			if ($this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a value less than 100 or greater than or equal to 250 is allowed.
				 */
				return UText::localize(
					"Only a value less than {{min_value}} or " . 
						"greater than or equal to {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//default
			/**
			 * @placeholder min_value The minimum disallowed value.
			 * @placeholder max_value The maximum disallowed value.
			 * @example Only a value less than 100 or greater than 250 is allowed.
			 */
			return UText::localize(
				"Only a value less than {{min_value}} or " . 
					"greater than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//min and max exclusive
		if ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a value greater than 100 and less than 250 is allowed.
			 */
			return UText::localize(
				"Only a value greater than {{min_value}} and " . 
					"less than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//min exclusive
		if ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a value greater than 100 and less than or equal to 250 is allowed.
			 */
			return UText::localize(
				"Only a value greater than {{min_value}} and " . 
					"less than or equal to {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//max exclusive
		if ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a value greater than or equal to 100 and less than 250 is allowed.
			 */
			return UText::localize(
				"Only a value greater than or equal to {{min_value}} and " . 
					"less than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//default
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only a value greater than or equal to 100 and less than or equal to 250 is allowed.
		 */
		return UText::localize(
			"Only a value greater than or equal to {{min_value}} and " . 
				"less than or equal to {{max_value}} is allowed.", 
			self::class, $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		//initialize
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		
		//min and max exclusive
		if ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example 100 (exclusive) to 250 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}} (exclusive)", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//min exclusive
		if ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example 100 (exclusive) to 250
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}}", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//max exclusive
		if ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example 100 to 250 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} to {{max_value}} (exclusive)", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//default
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example 100 to 250
		 */
		return UText::localize(
			"{{min_value}} to {{max_value}}", 
			self::class, $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'minimum' => [
				'value' => $this->min_value,
				'exclusive' => $this->min_exclusive
			],
			'maximum' => [
				'value' => $this->max_value,
				'exclusive' => $this->max_exclusive
			],
			'negate' => $this->negate
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyNames(['min_value', 'max_value']);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'min_value':
				//no break
			case 'max_value':
				return $this->createProperty()
					->setMode('w--')
					->addEvaluator(\Closure::fromCallable([$this, 'evaluateValue']))
					->bind(self::class)
				;
			case 'min_exclusive':
				//no break
			case 'max_exclusive':
				//no break
			case 'negate':
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
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The generated string from the given value.</p>
	 */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UText::stringify($value, $text_options, ['quote_strings' => true]);
	}
}
