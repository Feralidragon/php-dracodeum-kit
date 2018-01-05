<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Constraints;

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
 * Core timestamp input values constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a timestamp to a set of allowed values.
 * 
 * @since 1.0.0
 * @property int[] $values <p>The allowed values to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed values act as disallowed values instead.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp
 */
class Values extends Constraint implements IPrototypeProperties, IInformation, IStringification
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
								if (!UTime::evaluateTimestamp($v)) {
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
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core timestamp input values constraint modifier prototype label (negate).
			 * @tags core prototype input timestamp modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed timestamp", "Disallowed timestamps",
				count($this->values), null,
				'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core timestamp input values constraint modifier prototype label.
		 * @tags core prototype input timestamp modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed timestamp", "Allowed timestamps",
			count($this->values), null,
			'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options) : string
	{
		$values_strings = $this->getValuesStrings($text_options);
		if ($this->negate) {
			/**
			 * @description Core timestamp input values constraint modifier prototype description (negate).
			 * @placeholder values The list of disallowed timestamp values.
			 * @tags core prototype input timestamp modifier constraint values description
			 * @example The following timestamps are not allowed: 2017-01-15 12:45:00, 2017-01-17 17:20:00 and 2017-01-18 03:00:00.
			 */
			return UText::plocalize(
				"The following timestamp is not allowed: {{values}}.",
				"The following timestamps are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($values_strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES])]
				]
			);
		}
		/**
		 * @description Core timestamp input values constraint modifier prototype description.
		 * @placeholder values The list of allowed timestamp values.
		 * @tags core prototype input timestamp modifier constraint values description
		 * @example Only one of the following timestamps is allowed: 2017-01-15 12:45:00, 2017-01-17 17:20:00 or 2017-01-18 03:00:00.
		 */
		return UText::plocalize(
			"Only the following timestamp is allowed: {{values}}.",
			"Only one of the following timestamps is allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => UText::stringify($values_strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR | UText::STRING_NO_QUOTES])]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : ?string
	{
		$values_strings = $this->getValuesStrings($text_options);
		if ($this->negate) {
			/**
			 * @description Core timestamp input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed timestamp values.
			 * @tags core prototype input timestamp modifier constraint values message
			 * @example The given timestamp cannot be 2017-01-15 12:45:00, 2017-01-17 17:20:00 nor 2017-01-18 03:00:00.
			 */
			return UText::localize(
				"The given timestamp cannot be {{values}}.",
				'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($values_strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_NOR | UText::STRING_NO_QUOTES])]
				]
			);
		}
		/**
		 * @description Core timestamp input values constraint modifier prototype message.
		 * @placeholder values The list of allowed timestamp values.
		 * @tags core prototype input timestamp modifier constraint values message
		 * @example The given timestamp must be 2017-01-15 12:45:00, 2017-01-17 17:20:00 or 2017-01-18 03:00:00.
		 */
		return UText::localize(
			"The given timestamp must be {{values}}.",
			'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options, [
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
			$strings[] = UTime::stringifyTimestamp($value, $text_options);
		}
		return $strings;
	}
}
