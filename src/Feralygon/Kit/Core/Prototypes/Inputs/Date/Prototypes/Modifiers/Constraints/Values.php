<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Constraints;

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
 * Core date input values constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a date to a set of allowed values.
 * 
 * @since 1.0.0
 * @property int[] $values <p>The allowed values to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed values act as disallowed values instead.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date
 */
class Values extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISpecificationData
{
	//Private properties
	/** @var int[] */
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
								if (!UTime::evaluateDate($v)) {
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
			 * @description Core date input values constraint modifier prototype label (negate).
			 * @tags core prototype input date modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed date", "Disallowed dates",
				count($this->values), null,
				'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core date input values constraint modifier prototype label.
		 * @tags core prototype input date modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed date", "Allowed dates",
			count($this->values), null,
			'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$values_strings = $this->getValuesStrings($text_options);
		if ($this->negate) {
			/**
			 * @description Core date input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed date values.
			 * @tags core prototype input date modifier constraint values message
			 * @example The following dates are not allowed: 2017-01-15, 2017-01-17 and 2017-01-18.
			 */
			return UText::plocalize(
				"The following date is not allowed: {{values}}.",
				"The following dates are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($values_strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES])]
				]
			);
		}
		/**
		 * @description Core date input values constraint modifier prototype message.
		 * @placeholder values The list of allowed date values.
		 * @tags core prototype input date modifier constraint values message
		 * @example Only one of the following dates is allowed: 2017-01-15, 2017-01-17 or 2017-01-18.
		 */
		return UText::plocalize(
			"Only the following date is allowed: {{values}}.",
			"Only one of the following dates is allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => UText::stringify($values_strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR | UText::STRING_NO_QUOTES])]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->getValuesStrings($text_options), $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES]);
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
	 * Get values strings.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string[] <p>The values strings.</p>
	 */
	protected function getValuesStrings(TextOptions $text_options) : array
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = UTime::stringifyDate($value, $text_options);
		}
		return $strings;
	}
}
