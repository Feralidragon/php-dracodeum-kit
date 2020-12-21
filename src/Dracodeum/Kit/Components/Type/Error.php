<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type;

use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Primitives\Text;

final class Error
{
	//Traits
	use Traits\EmptyConstructor;
	
	
	
	//Private properties
	private ?Text $text = null;
	
	
	
	//Final public methods
	/**
	 * Check if has text.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has text.</p>
	 */
	final public function hasText(): bool
	{
		return $this->text !== null;
	}
	
	/**
	 * Get text instance.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * <p>The text instance or <code>null</code> if none is set.</p>
	 */
	final public function getText(): ?Text
	{
		return $this->text;
	}
	
	/**
	 * Set text.
	 * 
	 * @param text $text
	 * <p>The text to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setText($text)
	{
		Text::coerce($text);
		$this->text = $text;
		return $this;
	}
}
