<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Number\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a number to a set of allowed powers.
 * 
 * @property-write int[]|float[] $powers [writeonce] [transient] [coercive]
 * <p>The allowed powers to restrict a given number to.<br>
 * They must all be greater than <code>0</code>.</p>
 * @property-write bool $negate [writeonce] [transient] [coercive] [default = false]
 * <p>Negate the restriction, so the given allowed powers act as disallowed powers instead.</p>
 */
class Powers extends Constraint implements IName, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int[]|float[] */
	protected $powers;
	
	/** @var bool */
	protected $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (UType::evaluateNumber($value)) {
			foreach ($this->powers as $power) {
				$f = log($value, $power);
				if ($f === floor($f)) {
					return !$this->negate;
				}
			}
			return $this->negate;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'powers';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
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
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$powers_string = UText::commify($this->powers, $text_options, 'or');
		
		//negate
		if ($this->negate) {
			/**
			 * @placeholder powers The list of disallowed powers.
			 * @example A power of 2, 3 or 5 is not allowed.
			 */
			return UText::localize(
				"A power of {{powers}} is not allowed.",
				self::class, $text_options, ['parameters' => ['powers' => $powers_string]]
			);
		}
		
		//default
		/**
		 * @placeholder powers The list of allowed powers.
		 * @example Only a power of 2, 3 or 5 is allowed.
		 */
		return UText::localize(
			"Only a power of {{powers}} is allowed.",
			self::class, $text_options, ['parameters' => ['powers' => $powers_string]]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::commify($this->powers, $text_options, 'and');
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'powers' => $this->powers,
			'negate' => $this->negate
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('powers');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'powers':
				return $this->createProperty()
					->setMode('w--')
					->setAsArray(function (&$key, &$value): bool {
						return UType::evaluateNumber($value) && $value > 0;
					}, true, true)
					->bind(self::class)
				;
			case 'negate':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
