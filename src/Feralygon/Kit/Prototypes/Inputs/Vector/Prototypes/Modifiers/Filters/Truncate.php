<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
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
 * This filter prototype truncates a vector to a specific length.
 * 
 * @since 1.0.0
 * @property-write int $length [writeonce]
 * <p>The length to truncate a given vector to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector
 */
class Truncate extends Filter implements IName, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $length;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (is_object($value) && $value instanceof Primitive) {
			$value->truncate($this->length);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'filters.truncate';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Truncated length", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder length The truncated length.
			 * @tags end-user
			 * @example The list is truncated to 100 items.
			 */
			return UText::plocalize(
				"The list is truncated to {{length}} item.",
				"The list is truncated to {{length}} items.",
				$this->length, 'length', self::class, $text_options
			);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @placeholder length The truncated length.
			 * @tags technical
			 * @example The array is truncated to 100 values.
			 */
			return UText::plocalize(
				"The array is truncated to {{length}} value.",
				"The array is truncated to {{length}} values.",
				$this->length, 'length', self::class, $text_options
			);
		}
		
		//non-end-user and non-technical
		/**
		 * @placeholder length The truncated length.
		 * @tags non-end-user non-technical
		 * @example The vector is truncated to 100 values.
		 */
		return UText::plocalize(
			"The vector is truncated to {{length}} value.",
			"The vector is truncated to {{length}} values.",
			$this->length, 'length', self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::stringify($this->length, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'length' => $this->length
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('length');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'length':
				return $this->createProperty()->setMode('w-')->setAsInteger(true)->bind(self::class);
		}
		return null;
	}
}
