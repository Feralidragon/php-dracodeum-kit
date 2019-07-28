<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Traits\Exception as Traits;

/** This exception is thrown from the type utility whenever the coercion into a boolean fails with a given value. */
class BooleanCoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid error code. */
	public const ERROR_CODE_INVALID = 'INVALID';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('error_message')
			? "Boolean coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Boolean coercion failed with value {{value}}.";
	}
}
