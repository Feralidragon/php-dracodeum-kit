<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Priority as IPriority,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Primitives\Dictionary as Primitive;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This constraint prototype restricts a dictionary to a minimum length.
 * 
 * @property-write int $length [writeonce] [transient] [coercive]
 * <p>The minimum length to restrict a given dictionary to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 */
class MinLength extends Constraint implements IName, IPriority, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $length;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return is_object($value) && $value instanceof Primitive ? $value->count() >= $this->length : false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'min_length';
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
		/**
		 * @placeholder length The minimum allowed length.
		 * @example Only a minimum of 10 key-value pairs are allowed.
		 */
		return UText::plocalize(
			"Only a minimum of {{length}} key-value pair is allowed.",
			"Only a minimum of {{length}} key-value pairs are allowed.",
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
				return $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class);
		}
		return null;
	}
}
