<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces;

use Feralygon\Kit\Options\Text as TextOptions;

/**
 * This interface defines a set of methods to retrieve information from an input modifier prototype, 
 * namely the label and message.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifier
 */
interface Information
{
	//Public methods
	/**
	 * Get label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The label.</p>
	 */
	public function getLabel(TextOptions $text_options) : string;
	
	/**
	 * Get message.
	 * 
	 * The returning message is meant to be assertive relative to the expected value.<br>
	 * It may also be used as an error message if the value evaluation fails.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The message.</p>
	 */
	public function getMessage(TextOptions $text_options) : string;
}
