<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Input\Interfaces;

use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Components\Input\Options\Info as InfoOptions;

/**
 * This interface defines a set of methods to get information from an input prototype, 
 * namely the label, description and message.
 */
interface Information
{
	//Public methods
	/**
	 * Get label.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info $info_options
	 * <p>The info options instance to use.</p>
	 * @return string
	 * <p>The label.</p>
	 */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string;
	
	/**
	 * Get description.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info $info_options
	 * <p>The info options instance to use.</p>
	 * @return string
	 * <p>The description.</p>
	 */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string;
	
	/**
	 * Get message.
	 * 
	 * The returning message is meant to be assertive relative to the expected value.<br>
	 * It may also be used as an error message if the value evaluation fails.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info $info_options
	 * <p>The info options instance to use.</p>
	 * @return string
	 * <p>The message.</p>
	 */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string;
}
