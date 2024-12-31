<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Exceptions;

use Dracodeum\Kit\Utilities\Type\Exception;
use Dracodeum\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Dracodeum\Kit\Traits\Exception as Traits;

class ClassCoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid class error code. */
	public const ERROR_CODE_INVALID_CLASS = 'INVALID_CLASS';
	
	/** Invalid error code. */
	public const ERROR_CODE_INVALID = 'INVALID';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Class coercion failed with value {{value}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
}
