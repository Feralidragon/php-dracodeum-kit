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
 * Core input values constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a value to a set of allowed values.
 * 
 * @since 1.0.0
 * @property array $values <p>The allowed values to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed values act as disallowed values instead.</p>
 */
class Values extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISpecificationData
{
	//Private properties
	/** @var array */
	private $values;
	
	/** @var bool */
	private $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		return in_array($value, $this->values, true) !== $this->negate;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'values':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						if (is_array($value) && !empty($value)) {
							foreach ($value as &$v) {
								if (!$this->evaluateValue($v)) {
									return false;
								}
							}
							unset($v);
							return true;
						}
						return false;
					})
					->setGetter(function () : array {
						return $this->values;
					})
					->setSetter(function (array $values) : void {
						$this->values = $values;
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
		return ['values'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.values';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core input values constraint modifier prototype label (negate).
			 * @tags core prototype input modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed value", "Disallowed values",
				count($this->values), null,
				'core.prototypes.input.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core input values constraint modifier prototype label.
		 * @tags core prototype input modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed value", "Allowed values",
			count($this->values), null,
			'core.prototypes.input.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed values.
			 * @tags core prototype input modifier constraint values message
			 * @example The following values are not allowed: "foo", "bar" and "abc".
			 */
			return UText::plocalize(
				"The following value is not allowed: {{values}}.",
				"The following values are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.input.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @description Core input values constraint modifier prototype message.
		 * @placeholder values The list of allowed values.
		 * @tags core prototype input modifier constraint values message
		 * @example Only the following values are allowed: "foo", "bar" and "abc".
		 */
		return UText::plocalize(
			"Only the following value is allowed: {{values}}.",
			"Only the following values are allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.input.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = $this->stringifyValue($value, $text_options);
		}
		return UText::stringify($strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES]);
	}
	
	
	
	//Implemented public methods (core input modifier prototype specification data interface)
	/** {@inheritdoc} */
	public function getSpecificationData()
	{
		return [
			'negate' => $this->negate,
			'values' => $this->values
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
