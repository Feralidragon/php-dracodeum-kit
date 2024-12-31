<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Uid\Exceptions;

use Dracodeum\Kit\Structures\Uid\Exception;
use Dracodeum\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Dracodeum\Kit\Traits\Exception as Traits;

class IdCoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "ID coercion failed with value {{value}} using UID {{uid}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
}
