<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Primitives\Vector as Primitive;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This constraint prototype restricts a vector to a range of lengths.
 * 
 * @since 1.0.0
 * @property-write int $min_length [writeonce] [coercive]
 * <p>The minimum length to restrict a given vector to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property-write int $max_length [writeonce] [coercive]
 * <p>The maximum length to restrict a given vector to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector
 */
class LengthRange extends Constraint implements IName, IPriority, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $min_length;
	
	/** @var int */
	protected $max_length;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (is_object($value) && $value instanceof Primitive) {
			$length = $value->count();
			return $length >= $this->min_length && $length <= $this->max_length;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.length_range';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Priority)
	/** {@inheritdoc} */
	public function getPriority(): int
	{
		return 250;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Allowed lengths range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder min_length The minimum allowed length.
			 * @placeholder max_length The maximum allowed length.
			 * @tags end-user
			 * @example Only between 5 and 10 items are allowed.
			 */
			return UText::plocalize(
				"Only between {{min_length}} and {{max_length}} item is allowed.",
				"Only between {{min_length}} and {{max_length}} items are allowed.",
				$this->max_length, 'max_length', self::class, $text_options, [
					'parameters' => ['min_length' => $this->min_length]
				]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder min_length The minimum allowed length.
		 * @placeholder max_length The maximum allowed length.
		 * @tags non-end-user
		 * @example Only between 5 and 10 values are allowed.
		 */
		return UText::plocalize(
			"Only between {{min_length}} and {{max_length}} value is allowed.",
			"Only between {{min_length}} and {{max_length}} values are allowed.",
			$this->max_length, 'max_length', self::class, $text_options, [
				'parameters' => ['min_length' => $this->min_length]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		/**
		 * @placeholder min_length The minimum allowed length.
		 * @placeholder max_length The maximum allowed length.
		 * @example 5 to 10
		 */
		return UText::localize(
			"{{min_length}} to {{max_length}}",
			self::class, $text_options, [
				'parameters' => ['min_length' => $this->min_length, 'max_length' => $this->max_length]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'minimum' => [
				'length' => $this->min_length
			],
			'maximum' => [
				'length' => $this->max_length
			]
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyNames(['min_length', 'max_length']);
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'min_length':
				//no break
			case 'max_length':
				return $this->createProperty()->setMode('w-')->setAsInteger(true)->bind(self::class);
		}
		return null;
	}
}
