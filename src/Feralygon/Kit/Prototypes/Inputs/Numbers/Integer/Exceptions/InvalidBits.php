<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Numbers\Integer\Exceptions;

use Feralygon\Kit\Prototypes\Inputs\Numbers\Integer\Exception;
use Feralygon\Kit\Prototypes\Inputs\Numbers\Integer;

/**
 * This exception is thrown from an integer number input whenever a given number of bits is invalid.
 * 
 * @since 1.0.0
 * @property-read int $bits <p>The number of bits.</p>
 * @property-read int $max_bits <p>The maximum allowed number of bits.</p>
 * @property-read \Feralygon\Kit\Prototypes\Inputs\Numbers\Integer $prototype 
 * <p>The integer number input prototype instance.</p>
 * @property-read bool $unsigned [default = false] <p>Handle as an unsigned integer.</p>
 */
class InvalidBits extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid bits {{bits}} in integer number input {{prototype}}.\n" . (
			$this->is('unsigned')
				? "HINT: Only up to {{max_bits}} bits are allowed for unsigned integers."
				: "HINT: Only up to {{max_bits}} bits are allowed for signed integers."
		);
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('bits')->setAsInteger()->setAsRequired();
		$this->addProperty('max_bits')->setAsInteger()->setAsRequired();
		$this->addProperty('prototype')->setAsStrictObject(Integer::class)->setAsRequired();
		$this->addProperty('unsigned')->setAsBoolean()->setDefaultValue(false);
	}
}
