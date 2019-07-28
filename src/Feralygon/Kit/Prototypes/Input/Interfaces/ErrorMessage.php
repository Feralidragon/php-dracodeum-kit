<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Interfaces;

use Feralygon\Kit\Options\Text as TextOptions;

/**
 * This interface defines a method to get the error message from an input prototype.
 * 
 * @see \Feralygon\Kit\Prototypes\Input
 */
interface ErrorMessage
{
	//Public methods
	/**
	 * Get error message.
	 * 
	 * The returning error message is used when the value evaluation fails.
	 * 
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string|null
	 * <p>The error message or <code>null</code> if none is set.</p>
	 */
	public function getErrorMessage(TextOptions $text_options): ?string;
}
