<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This constraint prototype restricts a text or string to alphabetical characters.
 * 
 * @since 1.0.0
 * @property bool $unicode [default = false]
 * <p>Check a given text or string as Unicode.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Text
 */
class Alphabetical extends Constraint implements IName, IInformation, ISchemaData
{
	//Private properties
	/** @var bool */
	private $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return preg_match($this->unicode ? '/^\pL*$/u' : '/^[a-z]*$/i', $value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.alphabetical';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Alphabetical characters only", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		return UText::localize("Only alphabetical characters are allowed.", self::class, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\Properties)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'unicode':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
