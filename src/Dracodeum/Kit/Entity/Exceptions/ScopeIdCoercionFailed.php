<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

/**
 * @property-read string $name
 * <p>The scope name.</p>
 */
class ScopeIdCoercionFailed extends IdCoercionFailed
{
	//Public constants
	/** Invalid name error code. */
	public const ERROR_CODE_INVALID_NAME = 'INVALID_NAME';
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Scope ID coercion failed for {{name}} with value {{value}} using entity {{entity}}" . 
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
