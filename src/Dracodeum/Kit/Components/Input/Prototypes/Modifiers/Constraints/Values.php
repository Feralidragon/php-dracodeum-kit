<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This constraint prototype restricts a given input value to a set of allowed values.
 * 
 * @property-write array $values [writeonce] [transient] [coercive]
 * <p>The allowed values to restrict a given input value to.</p>
 * @property-write bool $negate [writeonce] [transient] [coercive] [default = false]
 * <p>Negate the restriction condition, so the given allowed values behave as disallowed values instead.</p>
 */
class Values extends Constraint implements IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var array */
	protected $values;
	
	/** @var bool */
	protected $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'values';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return $this->evaluateValue($value) && $this->isValueAllowed($value) !== $this->negate;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
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
	public function getMessage(TextOptions $text_options): string
	{
		//negate
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
		
		//default
		/**
		 * @placeholder values The list of allowed values.
		 * @example Only the following values are allowed: "foo", "bar" or "abc".
		 */
		return UText::plocalize(
			"Only the following value is allowed: {{values}}.",
			"Only the following values are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = $this->stringifyValue($value, $text_options);
		}
		return UText::commify($strings, $text_options, $this->negate ? 'and' : 'or');
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'values' => $this->values,
			'negate' => $this->negate
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('values');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'values':
				return $this->createProperty()
					->setMode('w--')
					->setAsArray(function (&$key, &$value): bool {
						return $this->evaluateValue($value);
					}, true, true)
					->bind(self::class)
				;
			case 'negate':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Check if a given value is allowed.
	 * 
	 * @param mixed $value
	 * <p>The value to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is allowed.</p>
	 */
	protected function isValueAllowed($value): bool
	{
		return in_array($value, $this->values, true);
	}
	
	/**
	 * Evaluate a given value.
	 * 
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
	 * @param mixed $value
	 * <p>The value to generate a string from.</p>
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The generated string from the given value.</p>
	 */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UText::stringify($value, $text_options, ['quote_strings' => true]);
	}
}
