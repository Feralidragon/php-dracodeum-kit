<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Hash\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;

/** This filter prototype converts a given hash input value to uppercase. */
class Uppercase extends Filter implements ISubtype
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'uppercase';
	}
	
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (is_string($value)) {
			$value = strtoupper($value);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'hash';
	}
}
