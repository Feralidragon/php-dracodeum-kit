<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Url\Exceptions\Querify;

use Feralygon\Kit\Utilities\Url\Exceptions\Querify;

/**
 * This exception is thrown from the URL utility <code>querify</code> method whenever a given parameter type 
 * is unsupported.
 * 
 * @since 1.0.0
 * @property-read string $name
 * <p>The name.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string $type [default = auto]
 * <p>The type.</p>
 */
class UnsupportedParameterType extends Querify
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Unsupported parameter type {{type}} for {{name}} given as {{value}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('type')
			->setAsString(true)
			->setDefaultGetter(function () {
				return gettype($this->get('value'));
			})
		;
	}
}
