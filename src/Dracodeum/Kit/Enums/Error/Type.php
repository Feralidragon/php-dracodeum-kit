<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enums\Error;

use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\ExceptionV2 as Exception;
use Throwable;

enum Type
{
	/** Throw a `Throwable` instance when an error occurs. */
	case THROWABLE;
	
	/** Return a `Dracodeum\Kit\Primitives\Error` instance when an error occurs. */
	case ERROR;
	
	/** Return `null` when an error occurs. */
	case NULL;
	
	
	
	//Final public methods
	/**
	 * Handle a given throwable instance.
	 * 
	 * @param \Throwable $throwable
	 * The throwable instance to handle.
	 * 
	 * @throws $throwable
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * The handling result of the given throwable instance, as an error instance or `null`.
	 */
	final public function handleThrowable(Throwable $throwable): ?Error
	{
		return match ($this) {
			self::ERROR => $throwable instanceof Exception
				? $throwable->toError()
				: Error::build($throwable::class, $throwable->getMessage())->setThrowable($throwable),
			self::NULL => null,
			default => throw $throwable
		};
	}
}
