<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

/** 
 * This exception is thrown from an entity whenever the coercion into a set of scope IDs fails with a given set of 
 * values.
 */
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
		return $this->isset('error_message')
			? "Scope IDs coercion failed with values {{value}} using entity {{entity}}, " . 
				"with the following error: {{error_message}}"
			: "Scope IDs coercion failed with values {{value}} using entity {{entity}}.";
	}
}
