<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Uid\Exceptions;

/**
 * This exception is thrown from a UID whenever the coercion into a scope ID fails with a given value.
 * 
 * @property-read string $name [coercive]
 * <p>The scope name.</p>
 */
class ScopeIdCoercionFailed extends IdCoercionFailed
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->error_message !== null
			? "Scope ID coercion failed for {{name}} with value {{value}} using UID {{uid}}, " . 
				"with the following error: {{error_message}}"
			: "Scope ID coercion failed for {{name}} with value {{value}} using UID {{uid}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString();
	}
}
