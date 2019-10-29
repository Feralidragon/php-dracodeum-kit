<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Exceptions;

use Dracodeum\Kit\Utilities\Type\Exception;
use Dracodeum\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Dracodeum\Kit\Traits\Exception as Traits;

/** This exception is thrown from the type utility whenever the coercion into an object fails with a given value. */
class ObjectCoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid class error code. */
	public const ERROR_CODE_INVALID_CLASS = 'INVALID_CLASS';
	
	/** Instance exception error code. */
	public const ERROR_CODE_INSTANCE_EXCEPTION = 'INSTANCE_EXCEPTION';
	
	/** Invalid error code. */
	public const ERROR_CODE_INVALID = 'INVALID';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('error_message')
			? "Object coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Object coercion failed with value {{value}}.";
	}
}
