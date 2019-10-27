<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Vector\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
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
 * This constraint prototype restricts a given vector input value to a minimum length.
 * 
 * @property-write int $value [writeonce] [transient] [coercive]
 * <p>The minimum length value to restrict a given vector input value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 */
class MinLength extends Constraint implements ISubtype, IPriority, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $value;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'min_length';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return is_object($value) && $value instanceof Primitive ? $value->count() >= $this->value : false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'vector';
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
		return UText::localize("Minimum allowed length", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder value The minimum allowed length value.
			 * @tags end-user
			 * @example Only a minimum of 10 items are allowed.
			 */
			return UText::plocalize(
				"Only a minimum of {{value}} item is allowed.",
				"Only a minimum of {{value}} items are allowed.",
				$this->value, 'value', self::class, $text_options
			);
		}
		
		//non-end-user
		/**
		 * @placeholder value The minimum allowed length value.
		 * @tags non-end-user
		 * @example Only a minimum of 10 values are allowed.
		 */
		return UText::plocalize(
			"Only a minimum of {{value}} value is allowed.",
			"Only a minimum of {{value}} values are allowed.",
			$this->value, 'value', self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::stringify($this->value, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'value' => $this->value
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('value');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'value':
				return $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class);
		}
		return null;
	}
}
