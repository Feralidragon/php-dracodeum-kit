<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;

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
	Data as UData,
	Text as UText,
	Type as UType
};

/**
 * Core number input multiples constraint modifier prototype class.
 * 
 * This input constraint modifier prototype restricts a number to a set of allowed multiples.
 * 
 * @since 1.0.0
 * @property int[]|float[] $multiples <p>The allowed multiples to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed multiples act as disallowed multiples instead.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number
 */
class Multiples extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var int[]|float[] */
	private $multiples;
	
	/** @var bool */
	private $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		foreach ($this->multiples as $multiple) {
			if (is_int($multiple) && is_int($value) && $value % $multiple === 0) {
				return !$this->negate;
			} elseif (is_float($multiple) || is_float($value)) {
				$f = (float)$value / (float)$multiple;
				if ($f === floor($f)) {
					return !$this->negate;
				}
			}
		}
		return $this->negate;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'multiples':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UData::evaluate($value, function (&$key, &$value) : bool {
							return UType::evaluateNumber($value) && !empty($value);
						}, true, true);
					})
					->setGetter(function () : array {
						return $this->multiples;
					})
					->setSetter(function (array $multiples) : void {
						$this->multiples = $multiples;
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
		return ['multiples'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.multiples';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::plocalize("Disallowed multiple", "Disallowed multiples", count($this->multiples), null, self::class, $text_options)
			: UText::plocalize("Allowed multiple", "Allowed multiples", count($this->multiples), null, self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @placeholder multiples The list of disallowed multiples.
			 * @example Multiples of 2, 3 and 5 are not allowed.
			 */
			return UText::localize(
				"Multiples of {{multiples}} are not allowed.",
				self::class, $text_options, [
					'parameters' => ['multiples' => UText::stringify($this->multiples, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND])]
				]
			);
		}
		/**
		 * @placeholder multiples The list of allowed multiples.
		 * @example Only multiples of 2, 3 or 5 are allowed.
		 */
		return UText::localize(
			"Only multiples of {{multiples}} are allowed.",
			self::class, $text_options, [
				'parameters' => ['multiples' => UText::stringify($this->multiples, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR])]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->multiples, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND]);
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'negate' => $this->negate,
			'multiples' => $this->multiples
		];
	}
}
