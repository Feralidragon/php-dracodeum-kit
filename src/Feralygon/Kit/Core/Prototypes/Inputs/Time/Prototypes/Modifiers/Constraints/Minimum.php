<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Information as IInformation,
	Stringification as IStringification
};
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Time as UTime,
	Type as UType
};

/**
 * Core time input minimum constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a time to a minimum value.
 * 
 * @since 1.0.0
 * @property int $value <p>The minimum allowed value to restrict to (inclusive).</p>
 * @property bool $exclusive [default = false] <p>Set the minimum allowed value as exclusive, restricting a given value to always be greater than the minimum allowed value, but never equal.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Time
 */
class Minimum extends Constraint implements IPrototypeProperties, IInformation, IStringification
{
	//Private properties
	/** @var int */
	private $value;
	
	/** @var bool */
	private $exclusive = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		return $this->exclusive ? $value > $this->value : $value >= $this->value;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'value':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UTime::evaluateTime($value);
					})
					->setGetter(function () : int {
						return $this->value;
					})
					->setSetter(function (int $value) : void {
						$this->value = $value;
					})
				;
			case 'exclusive':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->exclusive;
					})
					->setSetter(function (bool $exclusive) : void {
						$this->exclusive = $exclusive;
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		/**
		 * @description Core time input minimum constraint modifier prototype label.
		 * @tags core prototype input time modifier constraint minimum label
		 */
		return UText::localize("Minimum allowed time", 'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options) : string
	{
		$value_string = UTime::stringifyTime($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @description Core time input minimum constraint modifier prototype description (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input time modifier constraint minimum description
			 * @example Only times after 12:45:00 are allowed.
			 */
			return UText::localize(
				"Only times after {{value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @description Core time input minimum constraint modifier prototype description.
		 * @placeholder value The minimum allowed value.
		 * @tags core prototype input time modifier constraint minimum description
		 * @example Only times after or at 12:45:00 are allowed.
		 */
		return UText::localize(
			"Only times after or at {{value}} are allowed.", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
				'parameters' => ['value' => $value_string]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : ?string
	{
		$value_string = UTime::stringifyTime($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @description Core time input minimum constraint modifier prototype message (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input time modifier constraint minimum message
			 * @example The given time must be after 12:45:00.
			 */
			return UText::localize(
				"The given time must be after {{value}}.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @description Core time input minimum constraint modifier prototype message.
		 * @placeholder value The minimum allowed value.
		 * @tags core prototype input time modifier constraint minimum message
		 * @example The given time must be after or at 12:45:00.
		 */
		return UText::localize(
			"The given time must be after or at {{value}}.", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
				'parameters' => ['value' => $value_string]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$value_string = UTime::stringifyTime($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @description Core time input minimum constraint modifier prototype string (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input time modifier constraint minimum string
			 * @example 12:45:00 (exclusive)
			 */
			return UText::localize(
				"{{value}} (exclusive)", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		return $value_string;
	}
}
