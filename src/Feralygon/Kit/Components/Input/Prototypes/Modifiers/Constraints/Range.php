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
 * This constraint prototype restricts a value to a range of values.
 * 
 * @since 1.0.0
 * @property mixed $min_value
 * <p>The minimum allowed value to restrict a given value to (inclusive).</p>
 * @property mixed $max_value
 * <p>The maximum allowed value to restrict a given value to (inclusive).</p>
 * @property bool $min_exclusive [default = false]
 * <p>Set the minimum allowed value as exclusive, 
 * restricting a given value to always be greater than the minimum allowed value, but never equal.</p>
 * @property bool $max_exclusive [default = false]
 * <p>Set the maximum allowed value as exclusive, 
 * restricting a given value to always be less than the maximum allowed value, but never equal.</p>
 * @property bool $negate [default = false]
 * <p>Negate the restriction, so the given allowed range of values acts as a disallowed range of values instead.</p>
 */
class Range extends Constraint implements IName, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var mixed */
	private $min_value;
	
	/** @var mixed */
	private $max_value;
	
	/** @var bool */
	private $min_exclusive = false;
	
	/** @var bool */
	private $max_exclusive = false;
	
	/** @var bool */
	private $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateNumber($value) && $this->negate !== (
			($this->min_exclusive ? $value > $this->min_value : $value >= $this->min_value) && 
			($this->max_exclusive ? $value < $this->max_value : $value <= $this->max_value)
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.range';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::localize("Disallowed values range", self::class, $text_options)
			: UText::localize("Allowed values range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		if ($this->negate) {
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
			} elseif ($this->min_exclusive) {
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
			} elseif ($this->max_exclusive) {
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
		} elseif ($this->min_exclusive && $this->max_exclusive) {
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
		} elseif ($this->min_exclusive) {
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
		} elseif ($this->max_exclusive) {
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
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
		} elseif ($this->min_exclusive) {
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
		} elseif ($this->max_exclusive) {
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyNames(['min_value', 'max_value']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'min_value':
				//no break
			case 'max_value':
				return $this->createProperty()
					->addEvaluator(\Closure::fromCallable([$this, 'evaluateValue']))
					->bind(self::class)
				;
			case 'min_exclusive':
				//no break
			case 'max_exclusive':
				//no break
			case 'negate':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Evaluate a given value.
	 * 
	 * @since 1.0.0
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
	 * @since 1.0.0
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
