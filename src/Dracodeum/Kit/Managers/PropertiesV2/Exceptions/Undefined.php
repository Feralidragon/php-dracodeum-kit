<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Exception;
use Dracodeum\Kit\Utilities\{
	Type as UType,
	Text as UText
};

/**
 * @property-read string[] $names
 * The names.
 */
class Undefined extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return count($this->names) === 1
			? "No property is defined with the name {{names}} in {{manager.getOwner()}}."
			: "No properties are defined with the names {{names}} in {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('names')->setAsArray(fn (&$key, &$value): bool => UType::evaluateString($value), true, true);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		return $placeholder === 'names'
			? UText::commify($value, conjunction: 'and', quote: true)
			: parent::getPlaceholderValueString($placeholder, $value);
	}
}
