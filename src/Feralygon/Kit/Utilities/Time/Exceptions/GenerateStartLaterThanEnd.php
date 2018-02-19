<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Time\Exceptions;

use Feralygon\Kit\Utilities\Time as UTime;

/**
 * Time utility <code>generate</code> method start later than end exception class.
 * 
 * This exception is thrown from the time utility <code>generate</code> method whenever a given start is later than a given end.
 * 
 * @since 1.0.0
 * @property-read int|float $start <p>The start.</p>
 * @property-read int|float $end <p>The end.</p>
 */
class GenerateStartLaterThanEnd extends Generate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Start {{start}} is later than end {{end}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('start')->setAsStrictNumber()->setAsRequired();
		$this->addProperty('end')->setAsStrictNumber()->setAsRequired();
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'start' || $placeholder === 'end') {
			return UTime::stringifyDateTime($value);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
