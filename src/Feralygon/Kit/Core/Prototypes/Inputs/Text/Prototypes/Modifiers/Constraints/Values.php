<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

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
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core text input values constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a text or string to a set of allowed values.
 * 
 * @since 1.0.0
 * @property string[] $values <p>The allowed values to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed values act as disallowed values instead.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text
 */
class Values extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISpecificationData
{
	//Private properties
	/** @var string[] */
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
								if (!UType::evaluateString($v)) {
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
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @description Core text input values constraint modifier prototype label (negate, technical).
				 * @tags core prototype input text modifier constraint values label technical
				 */
				return UText::plocalize(
					"Disallowed string", "Disallowed strings",
					count($this->values), null,
					'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options
				);
			}
			/**
			 * @description Core text input values constraint modifier prototype label (negate).
			 * @tags core prototype input text modifier constraint values label non-technical
			 */
			return UText::plocalize(
				"Disallowed text", "Disallowed texts",
				count($this->values), null,
				'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options
			);
		} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @description Core text input values constraint modifier prototype label (technical).
			 * @tags core prototype input text modifier constraint values label technical
			 */
			return UText::plocalize(
				"Allowed string", "Allowed strings",
				count($this->values), null,
				'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core text input values constraint modifier prototype label.
		 * @tags core prototype input text modifier constraint values label non-technical
		 */
		return UText::plocalize(
			"Allowed text", "Allowed texts",
			count($this->values), null,
			'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options) : string
	{
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @description Core text input values constraint modifier prototype description (negate, technical).
				 * @placeholder values The list of disallowed text values.
				 * @tags core prototype input text modifier constraint values description technical
				 * @example The following strings are not allowed: "foo", "bar" and "abc".
				 */
				return UText::plocalize(
					"The following string is not allowed: {{values}}.",
					"The following strings are not allowed: {{values}}.",
					count($this->values), null,
					'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
						'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND])]
					]
				);
			}
			/**
			 * @description Core text input values constraint modifier prototype description (negate).
			 * @placeholder values The list of disallowed text values.
			 * @tags core prototype input text modifier constraint values description non-technical
			 * @example The following texts are not allowed: "foo", "bar" and "abc".
			 */
			return UText::plocalize(
				"The following text is not allowed: {{values}}.",
				"The following texts are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND])]
				]
			);
		} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @description Core text input values constraint modifier prototype description (technical).
			 * @placeholder values The list of allowed text values.
			 * @tags core prototype input text modifier constraint values description technical
			 * @example Only one of the following strings is allowed: "foo", "bar" or "abc".
			 */
			return UText::plocalize(
				"Only the following string is allowed: {{values}}.",
				"Only one of the following strings is allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR])]
				]
			);
		}
		/**
		 * @description Core text input values constraint modifier prototype description.
		 * @placeholder values The list of allowed text values.
		 * @tags core prototype input text modifier constraint values description non-technical
		 * @example Only one of the following texts is allowed: "foo", "bar" or "abc".
		 */
		return UText::plocalize(
			"Only the following text is allowed: {{values}}.",
			"Only one of the following texts is allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR])]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : ?string
	{
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @description Core text input values constraint modifier prototype message (negate, technical).
				 * @placeholder values The list of disallowed text values.
				 * @tags core prototype input text modifier constraint values message technical
				 * @example The given string cannot be "foo", "bar" nor "abc".
				 */
				return UText::localize(
					"The given string cannot be {{values}}.",
					'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
						'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_NOR])]
					]
				);
			}
			/**
			 * @description Core text input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed text values.
			 * @tags core prototype input text modifier constraint values message non-technical
			 * @example The given text cannot be "foo", "bar" nor "abc".
			 */
			return UText::localize(
				"The given text cannot be {{values}}.",
				'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_NOR])]
				]
			);
		} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @description Core text input values constraint modifier prototype message (technical).
			 * @placeholder values The list of allowed text values.
			 * @tags core prototype input text modifier constraint values message technical
			 * @example The given string must be "foo", "bar" or "abc".
			 */
			return UText::localize(
				"The given string must be {{values}}.",
				'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR])]
				]
			);
		}
		/**
		 * @description Core text input values constraint modifier prototype message.
		 * @placeholder values The list of allowed text values.
		 * @tags core prototype input text modifier constraint values message non-technical
		 * @example The given text must be "foo", "bar" or "abc".
		 */
		return UText::localize(
			"The given text must be {{values}}.",
			'core.prototypes.inputs.text.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR])]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->values, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND]);
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
}
