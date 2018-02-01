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
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
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
 * @property bool $negate [default = false] <p>Negate the restriction, 
 * so the given allowed powers act as disallowed powers instead.</p>
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
					->bind($name, self::class)
					->setAsArray(function (&$key, &$value) : bool {
						return UType::evaluateNumber($value) && $value > 0;
					}, true, true)
				;
			case 'negate':
				return $this->createProperty()->bind($name, self::class)->setAsBoolean();
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
			? UText::plocalize(
				"Disallowed power", "Disallowed powers",
				count($this->powers), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed power", "Allowed powers",
				count($this->powers), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$powers_string = UText::stringify($this->powers, $text_options, [
			'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR
		]);
		if ($this->negate) {
			/**
			 * @placeholder powers The list of disallowed powers.
			 * @example A power of 2, 3 or 5 is not allowed.
			 */
			return UText::localize(
				"A power of {{powers}} is not allowed.",
				self::class, $text_options, [
					'parameters' => ['powers' => $powers_string]
				]
			);
		}
		/**
		 * @placeholder powers The list of allowed powers.
		 * @example Only a power of 2, 3 or 5 is allowed.
		 */
		return UText::localize(
			"Only a power of {{powers}} is allowed.",
			self::class, $text_options, [
				'parameters' => ['powers' => $powers_string]
			]
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->powers, $text_options, [
			'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
		]);
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'powers' => $this->powers,
			'negate' => $this->negate
		];
	}
}
