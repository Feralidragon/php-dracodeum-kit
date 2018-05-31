<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumeration\Exceptions;

use Feralygon\Kit\Enumeration\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Traits\Exception as Traits;

/**
 * This exception is thrown from an enumeration whenever the coercion into an element value fails with a given value.
 * 
 * @since 1.0.0
 */
class ValueCoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	/** Not found error code. */
	public const ERROR_CODE_NOT_FOUND = 'NOT_FOUND';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Value coercion failed with value {{value}} in enumeration {{enumeration}}, " . 
				"with the following error: {{error_message}}"
			: "Value coercion failed with value {{value}} in enumeration {{enumeration}}.";
	}
}
