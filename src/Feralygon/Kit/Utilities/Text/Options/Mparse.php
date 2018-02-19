<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Options;

use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * Text utility mparse method options class.
 * 
 * @since 1.0.0
 * @property bool $keep_nulls [default = false] <p>Keep the <code>null</code> values in the returned array.</p>
 */
class Mparse extends Parse
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'keep_nulls':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return parent::buildProperty($name);
	}
}
