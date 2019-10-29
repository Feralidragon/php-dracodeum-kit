<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Components\Modifiers;

use Dracodeum\Kit\Components\Input\Components\Modifier;
use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint as Prototype;
use Dracodeum\Kit\Components\Input\Factories\Component as Factory;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This component represents a constraint modifier which checks an input value.
 * 
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint
 */
class Constraint extends Modifier
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getType(): string
	{
		return 'constraint';
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function handleValueEvaluation(&$value): bool
	{
		return $this->getPrototype()->checkValue($value);
	}
	
	/** {@inheritdoc} */
	protected function getDefaultErrorMessage(TextOptions $text_options): string
	{
		return UText::localize("The given value is not allowed.", self::class, $text_options);
	}
	
	
	
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function getBasePriority(): int
	{
		return 1000;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'constraint'];
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
}
