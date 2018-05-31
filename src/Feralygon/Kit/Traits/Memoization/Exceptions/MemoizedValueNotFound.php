<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Memoization\Exceptions;

use Feralygon\Kit\Traits\Memoization\Exception;

/**
 * This exception is thrown from a class or object using the memoization trait whenever no memoized value 
 * is found at a given key.
 * 
 * @since 1.0.0
 * @property-read string $key
 * <p>The key.</p>
 * @property-read string $namespace [default = '']
 * <p>The namespace.</p>
 */
class MemoizedValueNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->get('namespace') !== ''
			? "No memoized value found at key {{key}} in namespace {{namespace}}."
			: "No memoized value found at key {{key}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('key')->setAsString()->setAsRequired();
		$this->addProperty('namespace')->setAsString()->setDefaultValue('');
	}
}
