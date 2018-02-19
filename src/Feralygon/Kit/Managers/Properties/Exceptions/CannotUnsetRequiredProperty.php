<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

/**
 * Properties manager cannot unset required property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given required property is attempted to be unset.
 * 
 * @since 1.0.0
 */
class CannotUnsetRequiredProperty extends CannotUnsetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot unset required property {{property.getName()}} from properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
