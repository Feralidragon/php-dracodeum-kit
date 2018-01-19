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
 * Core number input powers constraint modifier prototype class.
 * 
 * This constraint prototype restricts a number to a set of allowed powers.
 * 
 * @since 1.0.0
 * @property int[]|float[] $powers <p>The allowed powers to restrict to.</p>
 * @property bool $negate [default = false] <p>Negate the restriction, so the given allowed powers act as disallowed powers instead.</p>
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number
 */
class Powers extends Constraint implements IPrototypeProperties, IName, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var int[]|float[] */
	private $powers;
	
	/** @var bool */
	private $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		foreach ($this->powers as $power) {
			$f = log($value, $power);
			if ($f === floor($f)) {
				return !$this->negate;
			}
		}
		return $this->negate;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'powers':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UData::evaluate($value, function (&$key, &$value) : bool {
							return UType::evaluateNumber($value) && $value > 0;
						}, true, true);
					})
					->setGetter(function () : array {
						return $this->powers;
					})
					->setSetter(function (array $powers) : void {
						$this->powers = $powers;
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
		return ['powers'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.powers';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::plocalize("Disallowed power", "Disallowed powers", count($this->powers), null, self::class, $text_options)
			: UText::plocalize("Allowed power", "Allowed powers", count($this->powers), null, self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @placeholder powers The list of disallowed powers.
			 * @example Powers of 2, 3 and 5 are not allowed.
			 */
			return UText::localize(
				"Powers of {{powers}} are not allowed.",
				self::class, $text_options, [
					'parameters' => ['powers' => UText::stringify($this->powers, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND])]
				]
			);
		}
		/**
		 * @placeholder powers The list of allowed powers.
		 * @example Only powers of 2, 3 or 5 are allowed.
		 */
		return UText::localize(
			"Only powers of {{powers}} are allowed.",
			self::class, $text_options, [
				'parameters' => ['powers' => UText::stringify($this->powers, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR])]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->powers, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND]);
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'negate' => $this->negate,
			'powers' => $this->powers
		];
	}
}
