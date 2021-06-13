<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Exceptions;

use Dracodeum\Kit\Components\Type\Exception;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * @property-read mixed $value
 * The value.
 * 
 * @property-read enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
 * The context.
 * 
 * @property-read \Dracodeum\Kit\Primitives\Error|null $error [default = null]  
 * The error instance.
 */
abstract class ProcessFailed extends Exception
{
	//Abstract protected methods
	/**
	 * Get label.
	 * 
	 * @return string
	 * The label.
	 */
	abstract protected function getLabel(): string;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return
			"{$this->getLabel()} failed with value {{value}} for context {{context}} using component {{component}} " . 
			"(with prototype {{component.getPrototypeClass()}})" . 
			($this->error !== null ? ", with the following error: {{error}}" : ".")
		;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
		$this->addProperty('context')->setAsEnumerationValue(EContext::class);
		$this->addProperty('error')->setAsStrictObject(Error::class, true)->setDefaultValue(null);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		return match ($placeholder) {
			'context' => EContext::getName($value),
			'error' => UText::formatMessage($value->getText()->toString(['info_level' => EInfoLevel::INTERNAL]), true),
			default => parent::getPlaceholderValueString($placeholder, $value)
		};
	}
}
