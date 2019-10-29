<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Vector\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Primitives\Vector as Primitive;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This constraint prototype restricts a given vector input value to a range of lengths.
 * 
 * @property-write int $min_value [writeonce] [transient] [coercive]
 * <p>The minimum length value to restrict a given vector input value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property-write int $max_value [writeonce] [transient] [coercive]
 * <p>The maximum length value to restrict a given vector input value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 */
class LengthRange extends Constraint implements ISubtype, IPriority, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $min_value;
	
	/** @var int */
	protected $max_value;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'length_range';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (is_object($value) && $value instanceof Primitive) {
			$length = $value->count();
			return $length >= $this->min_value && $length <= $this->max_value;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'vector';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Priority)
	/** {@inheritdoc} */
	public function getPriority(): int
	{
		return 250;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Allowed length range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder min_value The minimum allowed length value.
			 * @placeholder max_value The maximum allowed length value.
			 * @tags end-user
			 * @example Only between 5 and 10 items are allowed.
			 */
			return UText::plocalize(
				"Only between {{min_value}} and {{max_value}} item is allowed.",
				"Only between {{min_value}} and {{max_value}} items are allowed.",
				$this->max_value, 'max_value', self::class, $text_options, [
					'parameters' => ['min_value' => $this->min_value]
				]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder min_value The minimum allowed length value.
		 * @placeholder max_value The maximum allowed length value.
		 * @tags non-end-user
		 * @example Only between 5 and 10 values are allowed.
		 */
		return UText::plocalize(
			"Only between {{min_value}} and {{max_value}} value is allowed.",
			"Only between {{min_value}} and {{max_value}} values are allowed.",
			$this->max_value, 'max_value', self::class, $text_options, [
				'parameters' => ['min_value' => $this->min_value]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		/**
		 * @placeholder min_value The minimum allowed length value.
		 * @placeholder max_value The maximum allowed length value.
		 * @example 5 to 10
		 */
		return UText::localize(
			"{{min_value}} to {{max_value}}",
			self::class, $text_options, [
				'parameters' => ['min_value' => $this->min_value, 'max_value' => $this->max_value]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'values' => [
				'minimum' => $this->min_value,
				'maximum' => $this->max_value
			]
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyNames(['min_value', 'max_value']);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'min_value':
				//no break
			case 'max_value':
				return $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class);
		}
		return null;
	}
}
