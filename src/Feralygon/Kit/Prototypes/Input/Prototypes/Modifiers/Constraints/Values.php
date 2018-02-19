<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * Input values constraint modifier prototype class.
 * 
 * This constraint prototype restricts a value to a set of allowed values.
 * 
 * @since 1.0.0
 * @property array $values <p>The allowed values to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, 
 * so the given allowed values act as disallowed values instead.</p>
 */
class Values extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISchemaData
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
	
	
	
	//Implemented public methods (prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'values':
				return $this->createProperty()
					->setAsArray(function (&$key, &$value) : bool {
						return $this->evaluateValue($value);
					}, true, true)
					->bind(self::class)
				;
			case 'negate':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['values'];
	}
	
	
	
	//Implemented public methods (input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.values';
	}
	
	
	
	//Implemented public methods (input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed value", "Disallowed values",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed value", "Allowed values",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed values.
			 * @example The following values are not allowed: "foo", "bar" and "abc".
			 */
			return UText::plocalize(
				"The following value is not allowed: {{values}}.",
				"The following values are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @placeholder values The list of allowed values.
		 * @example Only the following values are allowed: "foo", "bar" and "abc".
		 */
		return UText::plocalize(
			"Only the following value is allowed: {{values}}.",
			"Only the following values are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Implemented public methods (input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = $this->stringifyValue($value, $text_options);
		}
		return UText::stringify($strings, $text_options, [
			'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
		]);
	}
	
	
	
	//Implemented public methods (input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'values' => $this->values,
			'negate' => $this->negate
		];
	}
	
	
	
	//Protected methods
	/**
	 * Evaluate a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated.</p>
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
	 * @param \Feralygon\Kit\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string <p>The generated string from the given value.</p>
	 */
	protected function stringifyValue($value, TextOptions $text_options) : string
	{
		return UText::stringify($value, $text_options, ['quote_strings' => true]);
	}
}
