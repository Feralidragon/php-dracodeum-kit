<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
 * 
 * @property-read string|null $scope_class
 * The scope class.
 */
class Unwriteable extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = count($this->names) === 1
			? "The property {{names}} from {{manager.getOwner()}} is not writeable"
			: "The properties {{names}} from {{manager.getOwner()}} are not writeable";
		
		//scope class
		$message .= $this->scope_class !== null ? " from the {{scope_class}} class scope." : ".";
		
		//return
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('names')->setAsArray(
			fn (&$key, &$value): bool => UType::evaluateString($value, true), true, true
		);
		$this->addProperty('scope_class')->setAsString(true, true);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		return $placeholder === 'names'
			? UText::commify($value, conjunction: 'and', quote: true)
			: parent::getPlaceholderValueString($placeholder, $value);
	}
}
