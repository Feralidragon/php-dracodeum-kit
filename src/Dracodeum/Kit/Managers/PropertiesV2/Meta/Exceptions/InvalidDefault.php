<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Meta\Exception;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * @property-read string $name
 * The name.
 * 
 * @property-read mixed $value
 * The value.
 * 
 * @property-read \Dracodeum\Kit\Primitives\Error $error
 * The error instance.
 */
class InvalidDefault extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid default value {{value}} given for property meta entry with name {{name}} in class {{class}}, " . 
			"with the following error: {{error}}";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString();
		$this->addProperty('value');
		$this->addProperty('error')->setAsStrictObject(Error::class);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if ($placeholder === 'error') {
			return UText::formatMessage(
				$value->getText()->toString(['info_level' => EInfoLevel::INTERNAL]),
				indentation_expression: "   "
			);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
