<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

class ScopeIdsCoercionFailed extends IdCoercionFailed
{
	//Public constants
	/** Missing names error code. */
	public const ERROR_CODE_MISSING_NAMES = 'MISSING_NAMES';
	
	/** Invalid names error code. */
	public const ERROR_CODE_INVALID_NAMES = 'INVALID_NAMES';
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Scope IDs coercion failed with values {{value}} using entity {{entity}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
}
