<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Number\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a given number input value to a set of allowed powers.
 * 
 * @property-write int[]|float[] $values [writeonce] [transient]
 * <p>The allowed power values to restrict a given number input value to.</p>
 * @property-write bool $negate [writeonce] [transient] [default = false]
 * <p>Negate the restriction condition, so the given allowed power values behave as disallowed power values instead.</p>
 */
class Powers extends Constraint implements ISubtype, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int[]|float[] */
	protected $values;
	
	/** @var bool */
	protected $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'powers';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (UType::evaluateNumber($value)) {
			foreach ($this->values as $v) {
				$f = log($value, $v);
				if ($f === floor($f)) {
					return !$this->negate;
				}
			}
			return $this->negate;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'number';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed power", "Disallowed powers",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed power", "Allowed powers",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$values_string = UText::commify($this->values, $text_options, 'or');
		
		//negate
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed power values.
			 * @example A power of 2, 3 or 5 is not allowed.
			 */
			return UText::localize(
				"A power of {{values}} is not allowed.",
				self::class, $text_options, ['parameters' => ['values' => $values_string]]
			);
		}
		
		//default
		/**
		 * @placeholder values The list of allowed power values.
		 * @example Only a power of 2, 3 or 5 is allowed.
		 */
		return UText::localize(
			"Only a power of {{values}} is allowed.",
			self::class, $text_options, ['parameters' => ['values' => $values_string]]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::commify($this->values, $text_options, $this->negate ? 'and' : 'or');
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
