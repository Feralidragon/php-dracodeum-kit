<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Byte\Exceptions;

use Feralygon\Kit\Utilities\Byte\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Traits\Exception as Traits;

/**
 * This exception is thrown from the byte utility whenever the coercion into a multiple fails with a given value.
 * 
 * @since 1.0.0
 */
class MultipleCoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	/** Invalid error code. */
	public const ERROR_CODE_INVALID = 'INVALID';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Multiple coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Multiple coercion failed with value {{value}}.";
	}
}
