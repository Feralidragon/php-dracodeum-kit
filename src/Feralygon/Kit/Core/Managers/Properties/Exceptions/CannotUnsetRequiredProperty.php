<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

/**
 * Core properties manager cannot unset required property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given required property with a given name 
 * is attempted to be unset.
 * 
 * @since 1.0.0
 */
class CannotUnsetRequiredProperty extends CannotUnsetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot unset required property {{name}} from properties manager with owner {{manager.getOwner()}}.";
	}
}
