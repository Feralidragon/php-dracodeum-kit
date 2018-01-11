<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core input range constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a value to a range of values.
 * 
 * @since 1.0.0
 * @property mixed $min_value <p>The minimum allowed value to restrict to (inclusive).</p>
 * @property mixed $max_value <p>The maximum allowed value to restrict to (inclusive).</p>
 * @property bool $min_exclusive [default = false] <p>Set the minimum allowed value as exclusive, restricting a given value to always be greater than the minimum allowed value, but never equal.</p>
 * @property bool $max_exclusive [default = false] <p>Set the maximum allowed value as exclusive, restricting a given value to always be lesser than the maximum allowed value, but never equal.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed range of values acts as a disallowed range of values instead.</p>
 */
class Range extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISchemaData
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
	public function checkValue($value) : bool
	{
		return (($this->min_exclusive ? $value > $this->min_value : $value >= $this->min_value) && ($this->max_exclusive ? $value < $this->max_value : $value <= $this->max_value)) !== $this->negate;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'min_value':
				return $this->createProperty()
					->setEvaluator(\Closure::fromCallable([$this, 'evaluateValue']))
					->setGetter(function () {
						return $this->min_value;
					})
					->setSetter(function ($min_value) : void {
						$this->min_value = $min_value;
					})
				;
			case 'max_value':
				return $this->createProperty()
					->setEvaluator(\Closure::fromCallable([$this, 'evaluateValue']))
					->setGetter(function () {
						return $this->max_value;
					})
					->setSetter(function ($max_value) : void {
						$this->max_value = $max_value;
					})
				;
			case 'min_exclusive':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->min_exclusive;
					})
					->setSetter(function (bool $min_exclusive) : void {
						$this->min_exclusive = $min_exclusive;
					})
				;
			case 'max_exclusive':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->max_exclusive;
					})
					->setSetter(function (bool $max_exclusive) : void {
						$this->max_exclusive = $max_exclusive;
					})
				;
			case 'negate':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->negate;
					})
					->setSetter(function (bool $negate) : void {
						$this->negate = $negate;
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['min_value', 'max_value'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.range';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core input range constraint modifier prototype label (negate).
			 * @tags core prototype input modifier constraint range label
			 */
			return UText::localize("Disallowed values range", 'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options);
		}
		/**
		 * @description Core input range constraint modifier prototype label.
		 * @tags core prototype input modifier constraint range label
		 */
		return UText::localize("Allowed values range", 'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @description Core input range constraint modifier prototype message (negate exclusive minimum and maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input modifier constraint range message
				 * @example Only values lesser than or equal to 100 or greater than or equal to 250 are allowed.
				 */
				return UText::localize(
					"Only values lesser than or equal to {{min_value}} or greater than or equal to {{max_value}} are allowed.", 
					'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @description Core input range constraint modifier prototype message (negate exclusive minimum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input modifier constraint range message
				 * @example Only values lesser than or equal to 100 or greater than 250 are allowed.
				 */
				return UText::localize(
					"Only values lesser than or equal to {{min_value}} or greater than {{max_value}} are allowed.", 
					'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @description Core input range constraint modifier prototype message (negate exclusive maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input modifier constraint range message
				 * @example Only values lesser than 100 or greater than or equal to 250 are allowed.
				 */
				return UText::localize(
					"Only values lesser than {{min_value}} or greater than or equal to {{max_value}} are allowed.", 
					'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			/**
			 * @description Core input range constraint modifier prototype message (negate).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range message
			 * @example Only values lesser than 100 or greater than 250 are allowed.
			 */
			return UText::localize(
				"Only values lesser than {{min_value}} or greater than {{max_value}} are allowed.", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core input range constraint modifier prototype message (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range message
			 * @example Only values greater than 100 and lesser than 250 are allowed.
			 */
			return UText::localize(
				"Only values greater than {{min_value}} and lesser than {{max_value}} are allowed.", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core input range constraint modifier prototype message (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range message
			 * @example Only values greater than 100 and lesser than or equal to 250 are allowed.
			 */
			return UText::localize(
				"Only values greater than {{min_value}} and lesser than or equal to {{max_value}} are allowed.", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core input range constraint modifier prototype message (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range message
			 * @example Only values greater than or equal to 100 and lesser than 250 are allowed.
			 */
			return UText::localize(
				"Only values greater than or equal to {{min_value}} and lesser than {{max_value}} are allowed.", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core input range constraint modifier prototype message.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input modifier constraint range message
		 * @example Only values greater than or equal to 100 and lesser than or equal to 250 are allowed.
		 */
		return UText::localize(
			"Only values greater than or equal to {{min_value}} and lesser than or equal to {{max_value}} are allowed.", 
			'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		if ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core input range constraint modifier prototype string (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range string
			 * @example 100 (exclusive) to 250 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}} (exclusive)", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core input range constraint modifier prototype string (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range string
			 * @example 100 (exclusive) to 250
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}}", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core input range constraint modifier prototype string (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input modifier constraint range string
			 * @example 100 to 250 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} to {{max_value}} (exclusive)", 
				'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core input range constraint modifier prototype string.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input modifier constraint range string
		 * @example 100 to 250
		 */
		return UText::localize(
			"{{min_value}} to {{max_value}}", 
			'core.prototypes.input.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'negate' => $this->negate,
			'minimum' => [
				'exclusive' => $this->min_exclusive,
				'value' => $this->min_value
			],
			'maximum' => [
				'exclusive' => $this->max_exclusive,
				'value' => $this->max_value
			]
		];
	}
	
	
	
	//Protected methods
	/**
	 * Evaluate a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given value is successfully evaluated.</p>
	 */
	protected function evaluateValue(&$value) : bool
	{
		return true;
	}
	
	/**
	 * Generate a string from a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to generate a string from.</p>
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string <p>The generated string from the given value.</p>
	 */
	protected function stringifyValue($value, TextOptions $text_options) : string
	{
		return UText::stringify($value, $text_options);
	}
}
