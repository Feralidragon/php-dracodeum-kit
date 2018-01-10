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
	SpecificationData as ISpecificationData
};
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core input minimum constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a value to a minimum value.
 * 
 * @since 1.0.0
 * @property mixed $value <p>The minimum allowed value to restrict to (inclusive).</p>
 * @property bool $exclusive [default = false] <p>Set the minimum allowed value as exclusive, restricting a given value to always be greater than the minimum allowed value, but never equal.</p>
 */
class Minimum extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISpecificationData
{
	//Private properties
	/** @var mixed */
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
					->setEvaluator(\Closure::fromCallable([$this, 'evaluateValue']))
					->setGetter(function () {
						return $this->value;
					})
					->setSetter(function ($value) : void {
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
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.minimum';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		/**
		 * @description Core input minimum constraint modifier prototype label.
		 * @tags core prototype input modifier constraint minimum label
		 */
		return UText::localize("Minimum allowed value", 'core.prototypes.input.prototypes.modifiers.constraints.minimum', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->exclusive) {
			/**
			 * @description Core input minimum constraint modifier prototype message (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input modifier constraint minimum message
			 * @example Only values greater than 250 are allowed.
			 */
			return UText::localize(
				"Only values greater than {{value}} are allowed.", 
				'core.prototypes.input.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $this->value]
				]
			);
		}
		/**
		 * @description Core input minimum constraint modifier prototype message.
		 * @placeholder value The minimum allowed value.
		 * @tags core prototype input modifier constraint minimum message
		 * @example Only values greater than or equal to 250 are allowed.
		 */
		return UText::localize(
			"Only values greater than or equal to {{value}} are allowed.", 
			'core.prototypes.input.prototypes.modifiers.constraints.minimum', $text_options, [
				'parameters' => ['value' => $this->value]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		if ($this->exclusive) {
			/**
			 * @description Core input minimum constraint modifier prototype string (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input modifier constraint minimum string
			 * @example 250 (exclusive)
			 */
			return UText::localize(
				"{{value}} (exclusive)", 
				'core.prototypes.input.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $this->value]
				]
			);
		}
		return UText::stringify($this->value, $text_options);
	}
	
	
	
	//Implemented public methods (core input modifier prototype specification data interface)
	/** {@inheritdoc} */
	public function getSpecificationData()
	{
		return [
			'exclusive' => $this->exclusive,
			'value' => $this->value
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
}
