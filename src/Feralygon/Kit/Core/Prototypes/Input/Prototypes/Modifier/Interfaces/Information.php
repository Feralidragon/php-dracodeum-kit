<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces;

use Feralygon\Kit\Core\Options\Text as TextOptions;

/**
 * Core input modifier prototype information interface.
 * 
 * This interface defines a set of methods to retrieve information from an input modifier prototype, such as label, description and message.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier
 */
interface Information
{
	//Public methods
	/**
	 * Get label.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string <p>The label.</p>
	 */
	public function getLabel(TextOptions $text_options) : string;
	
	/**
	 * Get description.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string <p>The description.</p>
	 */
	public function getDescription(TextOptions $text_options) : string;
	
	/**
	 * Get message.
	 * 
	 * The returning message is meant to be assertive relative the expected value.<br>
	 * It may also be used as an error message if the value evaluation fails.<br>
	 * <br>
	 * For cases where the value evaluation is expected to be always successful, and thus never fail, 
	 * the returning message may be <samp>null</samp>.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string|null <p>The message or <samp>null</samp> if none is set.</p>
	 */
	public function getMessage(TextOptions $text_options) : ?string;
}
