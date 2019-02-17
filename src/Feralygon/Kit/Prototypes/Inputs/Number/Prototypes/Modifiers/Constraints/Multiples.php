<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;

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
 * This constraint prototype restricts a number to a set of allowed multiples.
 * 
 * @since 1.0.0
 * @property-write int[]|float[] $multiples [writeonce]
 * <p>The allowed multiples to restrict a given number to.<br>
 * They must all be different from <code>0</code>.</p>
 * @property-write bool $negate [writeonce] [default = false]
 * <p>Negate the restriction, so the given allowed multiples act as disallowed multiples instead.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Number
 */
class Multiples extends Constraint implements IName, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int[]|float[] */
	protected $multiples;
	
	/** @var bool */
	protected $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (UType::evaluateNumber($value)) {
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
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.multiples';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed multiple", "Disallowed multiples",
				count($this->multiples), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed multiple", "Allowed multiples",
				count($this->multiples), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$multiples_string = UText::stringify($this->multiples, $text_options, [
			'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR
		]);
		
		//negate
		if ($this->negate) {
			/**
			 * @placeholder multiples The list of disallowed multiples.
			 * @example A multiple of 2, 3 or 5 is not allowed.
			 */
			return UText::localize(
				"A multiple of {{multiples}} is not allowed.",
				self::class, $text_options, ['parameters' => ['multiples' => $multiples_string]]
			);
		}
		
		//default
		/**
		 * @placeholder multiples The list of allowed multiples.
		 * @example Only a multiple of 2, 3 or 5 is allowed.
		 */
		return UText::localize(
			"Only a multiple of {{multiples}} is allowed.",
			self::class, $text_options, ['parameters' => ['multiples' => $multiples_string]]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::stringify($this->multiples, $text_options, [
			'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
		]);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'multiples' => $this->multiples,
			'negate' => $this->negate
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyNames(['multiples']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'multiples':
				return $this->createProperty()
					->setMode('w-')
					->setAsArray(function (&$key, &$value): bool {
						return UType::evaluateNumber($value) && !empty($value);
					}, true, true)
					->bind(self::class)
				;
			case 'negate':
				return $this->createProperty()->setMode('w-')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
