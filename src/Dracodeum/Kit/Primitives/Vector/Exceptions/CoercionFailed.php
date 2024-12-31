<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Vector\Exceptions;

use Dracodeum\Kit\Primitives\Vector\Exception;
use Dracodeum\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Dracodeum\Kit\Traits\Exception as Traits;

class CoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	/** Build exception error code. */
	public const ERROR_CODE_BUILD_EXCEPTION = 'BUILD_EXCEPTION';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Coercion failed with value {{value}} using vector {{vector}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
}
