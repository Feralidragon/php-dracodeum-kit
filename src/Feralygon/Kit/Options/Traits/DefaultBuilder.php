<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Options\Traits;

/** 
 * This trait defines a method to get the default builder function from an options class.
 * 
 * @see \Feralygon\Kit\Options
 */
trait DefaultBuilder
{
	//Protected static methods
	/**
	 * Get default builder function.
	 * 
	 * The returning function is used to build an instance during a coercion or evaluation if none is given, 
	 * and it is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Feralygon\Kit\Options</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Options</b></code><br>
	 * The built instance.
	 * 
	 * @return callable|null
	 * <p>The default builder function or <code>null</code> if none is set.</p>
	 */
	protected static function getDefaultBuilder(): ?callable
	{
		return null;
	}
}
