<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces;

use Feralygon\Kit\Core\Options\Text as TextOptions;

/**
 * Core input modifier prototype error information interface.
 * 
 * This interface defines a method to retrieve error information from an input modifier prototype, 
 * namely an error message.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier
 */
interface ErrorInformation
{
	//Public methods
	/**
	 * Get error message.
	 * 
	 * The returning error message is used when the value evaluation fails.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string <p>The error message.</p>
	 */
	public function getErrorMessage(TextOptions $text_options) : string;
}
