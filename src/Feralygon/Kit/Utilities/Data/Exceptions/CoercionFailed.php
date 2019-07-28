<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Traits\Exception as Traits;

/** This exception is thrown from the data utility whenever the coercion fails with a given value. */
class CoercionFailed extends Exception implements ICoercive
{
	//Traits
	use Traits\Coercive;
	
	
	
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	/** Empty error code. */
	public const ERROR_CODE_EMPTY = 'EMPTY';
	
	/** Associative error code. */
	public const ERROR_CODE_ASSOCIATIVE = 'ASSOCIATIVE';
	
	/** Invalid element error code. */
	public const ERROR_CODE_INVALID_ELEMENT = 'INVALID_ELEMENT';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('error_message')
			? "Coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Coercion failed with value {{value}}.";
	}
}
