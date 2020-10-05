<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;

/**
 * @property int $info_scope [coercive = enumeration value] [default = INTERNAL]
 * <p>The info scope to use, as a value from the <code>Dracodeum\Kit\Enumerations\InfoScope</code> enumeration.</p>
 * @see \Dracodeum\Kit\Enumerations\InfoScope
 */
class Text extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'info_scope':
				return $this->createProperty()
					->setAsEnumerationValue(EInfoScope::class)
					->setDefaultValue(EInfoScope::INTERNAL)
				;
		}
		return null;
	}
}
