<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Text utility <code>localize</code> method options.
 * 
 * @property array $parameters [coercive] [default = []]
 * <p>The parameters to replace the respective message placeholders with, as <samp>name => value</samp> pairs.</p>
 * @property \Dracodeum\Kit\Utilities\Text\Options\Stringify $string_options [coercive] [default = null]
 * <p>The text utility <code>Dracodeum\Kit\Utilities\Text</code> stringification method options to use.</p>
 * @property callable|null $stringifier [coercive] [default = null]
 * <p>The function to use to stringify a given value for a given placeholder.<br>
 * It is expected to be compatible with the following signature:<br>
 * <br>
 * <code>function (string $placeholder, $value): ?string</code><br>
 * <br>
 * Parameters:<br>
 * &nbsp; &#8226; &nbsp; <code><b>string $placeholder</b></code><br>
 * &nbsp; &nbsp; &nbsp; The placeholder to stringify for.<br>
 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
 * &nbsp; &nbsp; &nbsp; The value to stringify.<br>
 * <br>
 * Return: <code><b>string|null</b></code><br>
 * The stringified value for the given placeholder or <code>null</code> if no stringification occurred.</p>
 */
class Localize extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'parameters':
				return $this->createProperty()->setAsArray()->setDefaultValue([]);
			case 'string_options':
				return $this->createProperty()->setAsOptions(Stringify::class)->setDefaultValue(null);
			case 'stringifier':
				return $this->createProperty()
					->setAsCallable(function (string $placeholder, $value): ?string {}, true, true)
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
