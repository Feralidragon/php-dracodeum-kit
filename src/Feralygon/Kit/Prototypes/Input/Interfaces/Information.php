<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Interfaces;

use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;

/**
 * Input prototype information interface.
 * 
 * This interface defines a set of methods to retrieve information from an input prototype, 
 * namely a label, a description and a message.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input
 */
interface Information
{
	//Public methods
	/**
	 * Get label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options <p>The text options instance to use.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info $info_options <p>The info options instance to use.</p>
	 * @return string <p>The label.</p>
	 */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string;
	
	/**
	 * Get description.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options <p>The text options instance to use.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info $info_options <p>The info options instance to use.</p>
	 * @return string <p>The description.</p>
	 */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string;
	
	/**
	 * Get message.
	 * 
	 * The returning message is meant to be assertive relative to the expected value.<br>
	 * It may also be used as an error message if the value evaluation fails.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options <p>The text options instance to use.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info $info_options <p>The info options instance to use.</p>
	 * @return string <p>The message.</p>
	 */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string;
}
