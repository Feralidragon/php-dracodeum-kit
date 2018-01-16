<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Components\Modifiers;

use Feralygon\Kit\Core\Components\Input\Components\Modifier;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint as Prototype;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core input constraint modifier component class.
 * 
 * This component represents a constraint modifier which checks an input value.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint
 */
class Constraint extends Modifier
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function handleValueEvaluation(&$value) : bool
	{
		return $this->getPrototype()->checkValue($value);
	}
	
	/** {@inheritdoc} */
	protected function getDefaultErrorMessage(TextOptions $text_options) : string
	{
		return UText::localize("The given value is not allowed.", self::class, $text_options);
	}
	
	
	
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function getBasePriority() : int
	{
		return 1000;
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass() : string
	{
		return Prototype::class;
	}
}
