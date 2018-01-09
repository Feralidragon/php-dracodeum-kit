<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SpecificationData as ISpecificationData
};
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Time as UTime,
	Type as UType
};

/**
 * Core time input range constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a time to a range of values.
 * 
 * @since 1.0.0
 * @property int $min_value <p>The minimum allowed value to restrict to (inclusive).</p>
 * @property int $max_value <p>The maximum allowed value to restrict to (inclusive).</p>
 * @property bool $min_exclusive [default = false] <p>Set the minimum allowed value as exclusive, restricting a given value to always be greater than the minimum allowed value, but never equal.</p>
 * @property bool $max_exclusive [default = false] <p>Set the maximum allowed value as exclusive, restricting a given value to always be lesser than the maximum allowed value, but never equal.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed range of values acts as a disallowed range of values instead.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Time
 */
class Range extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISpecificationData
{
	//Private properties
	/** @var int */
	private $min_value;
	
	/** @var int */
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
					->setEvaluator(function (&$value) : bool {
						return UTime::evaluateTime($value);
					})
					->setGetter(function () : int {
						return $this->min_value;
					})
					->setSetter(function (int $min_value) : void {
						$this->min_value = $min_value;
					})
				;
			case 'max_value':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UTime::evaluateTime($value);
					})
					->setGetter(function () : int {
						return $this->max_value;
					})
					->setSetter(function (int $max_value) : void {
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
			 * @description Core time input range constraint modifier prototype label (negate).
			 * @tags core prototype input time modifier constraint range label
			 */
			return UText::localize("Disallowed times range", 'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options);
		}
		/**
		 * @description Core time input range constraint modifier prototype label.
		 * @tags core prototype input time modifier constraint range label
		 */
		return UText::localize("Allowed times range", 'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$min_value_string = UTime::stringifyTime($this->min_value, $text_options);
		$max_value_string = UTime::stringifyTime($this->max_value, $text_options);
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @description Core time input range constraint modifier prototype message (negate exclusive minimum and maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input time modifier constraint range message
				 * @example Only times before or at 12:45:00 or after or at 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only times before or at {{min_value}} or after or at {{max_value}} are allowed.", 
					'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @description Core time input range constraint modifier prototype message (negate exclusive minimum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input time modifier constraint range message
				 * @example Only times before or at 12:45:00 or after 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only times before or at {{min_value}} or after {{max_value}} are allowed.", 
					'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @description Core time input range constraint modifier prototype message (negate exclusive maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input time modifier constraint range message
				 * @example Only times before 12:45:00 or after or at 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only times before {{min_value}} or after or at {{max_value}} are allowed.", 
					'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			/**
			 * @description Core time input range constraint modifier prototype message (negate).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times before 12:45:00 or after 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times before {{min_value}} or after {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype message (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times after 12:45:00 and before 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times after {{min_value}} and before {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype message (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times after 12:45:00 and before or at 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times after {{min_value}} and before or at {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype message (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times after or at 12:45:00 and before 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times after or at {{min_value}} and before {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core time input range constraint modifier prototype message.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input time modifier constraint range message
		 * @example Only times after or at 12:45:00 and before or at 17:20:00 are allowed.
		 */
		return UText::localize(
			"Only times after or at {{min_value}} and before or at {{max_value}} are allowed.", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$min_value_string = UTime::stringifyTime($this->min_value, $text_options);
		$max_value_string = UTime::stringifyTime($this->max_value, $text_options);
		if ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype string (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range string
			 * @example 12:45:00 (exclusive) to 17:20:00 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}} (exclusive)", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype string (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range string
			 * @example 12:45:00 (exclusive) to 17:20:00
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}}", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype string (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range string
			 * @example 12:45:00 to 17:20:00 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} to {{max_value}} (exclusive)", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core time input range constraint modifier prototype string.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input time modifier constraint range string
		 * @example 12:45:00 to 17:20:00
		 */
		return UText::localize(
			"{{min_value}} to {{max_value}}", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype specification data interface)
	/** {@inheritdoc} */
	public function getSpecificationData()
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
}
