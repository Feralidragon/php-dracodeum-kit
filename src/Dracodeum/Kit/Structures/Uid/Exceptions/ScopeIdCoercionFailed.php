<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Uid\Exceptions;

/**
 * @property-read string $name
 * <p>The scope name.</p>
 */
class ScopeIdCoercionFailed extends IdCoercionFailed
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Scope ID coercion failed for {{name}} with value {{value}} using UID {{uid}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
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
